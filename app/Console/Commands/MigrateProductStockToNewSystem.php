<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductSizeColorInventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateProductStockToNewSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:product-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate product stock data from the old system to the new inventory system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of product stock data to the new inventory system...');
        
        // Step 1: Migrate general product stock
        $this->migrateGeneralProductStock();
        
        // Step 2: Migrate product sizes with colors stock
        $this->migrateProductSizesStock();
        
        $this->info('Migration completed successfully!');
        return Command::SUCCESS;
    }
    
    /**
     * Migrate general product stock to the new system
     */
    private function migrateGeneralProductStock()
    {
        $this->info('Migrating general product stock...');
        
        $count = 0;
        $products = Product::all();
        $bar = $this->output->createProgressBar(count($products));
        
        foreach ($products as $product) {
            // Check if product already has an entry in the new system
            $exists = ProductSizeColorInventory::where('product_id', $product->id)
                ->whereNull('size_id')
                ->whereNull('color_id')
                ->exists();
                
            if (!$exists) {
                // Create a new inventory entry for the general product stock
                ProductSizeColorInventory::create([
                    'product_id' => $product->id,
                    'size_id' => null,
                    'color_id' => null,
                    'stock' => $product->stock ?? 0,
                    'consumed_stock' => $product->consumed_stock ?? 0,
                    'price' => $product->base_price,
                    'is_available' => $product->is_available,
                ]);
                
                $count++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$count} general product stock entries.");
    }
    
    /**
     * Migrate product sizes with colors stock to the new system
     */
    private function migrateProductSizesStock()
    {
        $this->info('Migrating product sizes with colors stock...');
        
        $count = 0;
        $productSizes = DB::table('product_sizes')->get();
        $bar = $this->output->createProgressBar(count($productSizes));
        
        foreach ($productSizes as $productSize) {
            // Check if this size-color combination already exists in the new system
            $exists = ProductSizeColorInventory::where('product_id', $productSize->product_id)
                ->where('size_id', $productSize->size_id)
                ->where('color_id', $productSize->color_id)
                ->exists();
                
            if (!$exists && isset($productSize->size_id) && isset($productSize->color_id)) {
                // Create a new inventory entry for this size-color combination
                ProductSizeColorInventory::create([
                    'product_id' => $productSize->product_id,
                    'size_id' => $productSize->size_id,
                    'color_id' => $productSize->color_id,
                    'stock' => $productSize->stock ?? 0,
                    'consumed_stock' => 0, // Assuming no consumed stock tracking in old system
                    'price' => $productSize->price,
                    'is_available' => $productSize->is_available ?? true,
                ]);
                
                $count++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Migrated {$count} product size-color stock entries.");
    }
}
