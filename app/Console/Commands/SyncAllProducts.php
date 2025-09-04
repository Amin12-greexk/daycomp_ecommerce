<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\SyncReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SyncAllProducts extends Command
{
    protected $signature = 'products:sync-all {--since= : Optional date for new product discovery}';
    protected $description = 'Updates stock, discovers new products, and removes deleted products.';

    public function handle()
    {
        $since = $this->option('since');
        $apiUrl = env('WAREHOUSE_API_URL');
        $apiToken = env('WAREHOUSE_API_TOKEN');

        if (!$apiUrl || !$apiToken) {
            $this->error('Warehouse API configuration is missing.');
            return Command::FAILURE;
        }

        try {
            // --- STEP 1: FETCH ALL DATA FROM WAREHOUSE ---
            $productResponse = Http::withToken($apiToken)->acceptJson()->get($apiUrl . '/api/v1/products' . ($since ? '?created_since=' . $since : ''));
            if ($productResponse->failed()) {
                throw new \Exception('Failed to fetch product list from warehouse.');
            }
            $warehouseProducts = $productResponse->json()['data'] ?? [];
            $warehouseProductCodes = array_column($warehouseProducts, 'product_code');

            // --- STEP 2: HANDLE DELETED PRODUCTS (ONLY ON A FULL SYNC) ---
            $deletedCount = 0;
            $deletedProducts = [];

            // THE FIX: This logic now only runs if no date is specified.
            if (!$since) {
                // Find local products that are NOT in the full warehouse list anymore
                $localProductsToDelete = Product::whereNotIn('product_code', $warehouseProductCodes)->get();

                foreach ($localProductsToDelete as $productToDelete) {
                    $deletedProducts[] = ['name' => $productToDelete->product_name, 'code' => $productToDelete->product_code];
                    $productToDelete->delete();
                    $deletedCount++;
                }
            }

            // --- STEP 3: DISCOVER NEW PRODUCTS ---
            $newProductsCount = 0;
            $newlyAddedProducts = [];
            $skippedCount = 0;

            foreach ($warehouseProducts as $wProduct) {
                if (!Product::where('product_code', $wProduct['product_code'])->exists()) {
                    $categoryId = null;
                    if (!empty($wProduct['category'])) {
                        $category = Category::updateOrCreate(['slug' => $wProduct['category']['slug']], ['category_name' => $wProduct['category']['name']]);
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
                        'stock_quantity' => $wProduct['stock_quantity'] ?? 0,
                    ]);
                    $newProductsCount++;
                    $newlyAddedProducts[] = ['name' => $newProduct->product_name, 'code' => $newProduct->product_code, 'category' => $category->category_name ?? 'N/A'];
                } else {
                    $skippedCount++;
                }
            }

            // --- STEP 4: UPDATE STOCK FOR ALL EXISTING PRODUCTS ---
            $stockResponse = Http::withToken($apiToken)->acceptJson()->get($apiUrl . '/api/v1/stock/all');
            if ($stockResponse->failed()) {
                throw new \Exception('Failed to fetch stock levels from warehouse.');
            }
            $warehouseStockLevels = $stockResponse->json()['data'] ?? [];

            $updatedStockCount = 0;
            $localProducts = Product::whereIn('product_code', array_keys($warehouseStockLevels))->get()->keyBy('product_code');

            foreach ($warehouseStockLevels as $productCode => $quantity) {
                if (isset($localProducts[$productCode])) {
                    $product = $localProducts[$productCode];
                    if ((int) $product->stock_quantity !== (int) $quantity) {
                        $product->stock_quantity = $quantity;
                        $product->save();
                        $updatedStockCount++;
                    }
                }
            }

            // --- STEP 5: SAVE AND OUTPUT THE REPORT ---
            $reportData = [
                'fetched_count' => count($warehouseProducts),
                'added_count' => $newProductsCount,
                'skipped_count' => $skippedCount,
                'stock_updated_count' => $updatedStockCount,
                'deleted_count' => $deletedCount,
                'details' => [
                    'new_products' => $newlyAddedProducts,
                    'deleted_products' => $deletedProducts,
                ],
                'user_id' => Auth::id() ?? null,
            ];
            $newReport = SyncReport::create($reportData);

            $this->line($newReport->toJson());
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            Log::error('Full Sync Exception: ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return Command::FAILURE;
        }
    }
}