<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // التحقق من وجود الجدول القديم
        if (!Schema::hasTable('product_sizes')) {
            return;
        }

        // نقل البيانات من الجدول القديم إلى الجديد
        $oldData = DB::table('product_sizes')->get();
        
        foreach ($oldData as $item) {
            // التحقق من عدم وجود بيانات مكررة
            $exists = DB::table('product_size_color_inventory')
                ->where('product_id', $item->product_id)
                ->where('size_id', $item->size_id)
                ->where('color_id', $item->color_id)
                ->exists();

            if (!$exists) {
                DB::table('product_size_color_inventory')->insert([
                    'product_id' => $item->product_id,
                    'size_id' => $item->size_id,
                    'color_id' => $item->color_id,
                    'stock' => $item->stock ?? 0,
                    'consumed_stock' => 0,
                    'price' => $item->price,
                    'is_available' => $item->is_available ?? true,
                    'created_at' => $item->created_at ?? now(),
                    'updated_at' => $item->updated_at ?? now(),
                ]);
            }
        }

        // تسجيل عدد السجلات المنقولة
        $migratedCount = DB::table('product_size_color_inventory')->count();
        \Log::info("Migrated {$migratedCount} records from product_sizes to product_size_color_inventory");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نحتاج لتراجع عن نقل البيانات
        // البيانات ستظل في الجدول الجديد
    }
};
