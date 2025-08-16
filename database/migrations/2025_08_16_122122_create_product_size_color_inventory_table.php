<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_size_color_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('size_id')->nullable()->constrained('size_options')->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained('color_options')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->integer('consumed_stock')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            // Add unique constraint to prevent duplicates
            $table->unique(['product_id', 'size_id', 'color_id'], 'product_size_color_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_size_color_inventory');
    }
};
