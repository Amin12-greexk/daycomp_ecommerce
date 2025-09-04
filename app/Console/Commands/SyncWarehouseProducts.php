<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Models\SyncReport; // <-- Import the new model
use Illuminate\Support\Facades\Auth;

class SyncWarehouseProducts extends Command
{
    protected $signature = 'products:sync-warehouse {--since= : Sync products created on or after this date (YYYY-MM-DD)}';
    protected $description = 'Fetches products from the warehouse API and syncs them to the local database';

    public function handle()
    {
        $since = $this->option('since');
        $apiUrl = env('WAREHOUSE_API_URL');
        $apiToken = env('WAREHOUSE_API_TOKEN');

        if (!$apiUrl || !$apiToken) {
            $this->error('Warehouse API URL or Token is not configured.');
            return Command::FAILURE;
        }

        try {
            $fullApiUrl = $apiUrl . '/api/v1/products' . ($since ? '?created_since=' . $since : '');
            $response = Http::withToken($apiToken)->acceptJson()->get($fullApiUrl);

            if ($response->failed()) {
                $this->error('API Connection Failed.');
                return Command::FAILURE;
            }

            $warehouseProducts = $response->json()['data'] ?? [];
            $fetchedCount = count($warehouseProducts);
            $newProductsCount = 0;
            $skippedCount = 0;
            $newlyAddedProducts = [];

            foreach ($warehouseProducts as $wProduct) {
                if (!Product::where('product_code', $wProduct['product_code'])->exists()) {
                    // ... (logic for creating product and category is the same)
                    $categoryId = null;
                    if (!empty($wProduct['category'])) {
                        $category = Category::updateOrCreate(
                            ['slug' => $wProduct['category']['slug']],
                            ['category_name' => $wProduct['category']['name']]
                        );
                        $categoryId = $category->id;
                    }
                    $newProduct = Product::create([
                        'product_code' => $wProduct['product_code'],
                        'product_name' => $wProduct['product_name'],
                        'sale_price' => $wProduct['sale_price'],
                        'image_url' => $wProduct['image_url'],
                        'date_in' => $wProduct['date_in'],
                        'category_id' => $categoryId,
                        'is_approved' => false,
                        'is_custom_form' => false,
                        'short_description' => null,
                        'minimum_quantity' => 1,
                    ]);
                    $newProductsCount++;
                    $newlyAddedProducts[] = [
                        'name' => $newProduct->product_name,
                        'code' => $newProduct->product_code,
                        'category' => $category ? $category->category_name : 'N/A',
                    ];
                } else {
                    $skippedCount++;
                }
            }

            // ** THE CHANGE **: Save the report to the database
            $reportData = [
                'fetched_count' => $fetchedCount,
                'added_count' => $newProductsCount,
                'skipped_count' => $skippedCount,
                'details' => ['new_products' => $newlyAddedProducts],
                'user_id' => Auth::id() ?? null, // Get the ID of the admin who ran the sync, if available
            ];
            SyncReport::create($reportData);

            // Output the report as JSON for the controller
            $this->line(json_encode($reportData));
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('An unexpected error occurred: ' . $e->getMessage());
            Log::error('Warehouse Sync Exception: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return Command::FAILURE;
        }
    }
}