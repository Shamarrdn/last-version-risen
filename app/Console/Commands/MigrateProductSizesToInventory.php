<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateProductSizesToInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-product-sizes-to-inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from old product_sizes table to product_size_color_inventory table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of product_sizes data to product_size_color_inventory table...');
        
        // Check if product_sizes table exists
        if (!\Schema::hasTable('product_sizes')) {
            $this->warn('The product_sizes table does not exist. Migration not needed.');
            return 0;
        }
        
        // Get data from product_sizes table
        $oldData = \DB::table('product_sizes')->get();
        
        if ($oldData->isEmpty()) {
            $this->info('No data found in product_sizes table. Nothing to migrate.');
            return 0;
        }
        
        $this->info('Found ' . $oldData->count() . ' records in product_sizes table.');
        
        $migrated = 0;
        $skipped = 0;
        
        foreach ($oldData as $item) {
            // Check if this combination already exists in the new table
            $exists = \DB::table('product_size_color_inventory')
                ->where('product_id', $item->product_id)
                ->where('size_id', $item->size_id ?? null)
                ->where('color_id', $item->color_id ?? null)
                ->exists();
                
            if ($exists) {
                $skipped++;
                continue;
            }
            
            // Insert into new table
            \DB::table('product_size_color_inventory')->insert([
                'product_id' => $item->product_id,
                'size_id' => $item->size_id ?? null,
                'color_id' => $item->color_id ?? null,
                'size' => $item->size ?? null,
                'stock' => $item->stock ?? 0,
                'consumed_stock' => 0,
                'price' => $item->price ?? null,
                'is_available' => $item->is_available ?? true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $migrated++;
        }
        
        $this->info("Migration completed: $migrated records migrated, $skipped records skipped (already exist).");
        
        return 0;
    }
}
