<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncProductStock extends Command
{
    protected $signature = 'products:sync-stock';
    protected $description = 'Updates local stock quantities by fetching from the Warehouse API';

    public function handle()
    {
        $this->info('Starting stock synchronization...');
        $products = Product::where('is_approved', true)->get();
        $updatedCount = 0;

        foreach ($products as $product) {
            $response = Http::withToken(env('WAREHOUSE_API_TOKEN'))
                ->acceptJson()
                ->get(env('WAREHOUSE_API_URL') . '/api/v1/stock/' . $product->product_code);

            if ($response->successful()) {
                $stockData = $response->json()['data'];
                $product->stock_quantity = $stockData['quantity'];
                $product->save();
                $updatedCount++;
            } else {
                $this->error("Failed to fetch stock for {$product->product_code}.");
                Log::warning("Stock Sync Failed for {$product->product_code}: " . $response->body());
            }
        }

        $this->info("Stock synchronization complete. Updated {$updatedCount} products.");
        return Command::SUCCESS;
    }
}
