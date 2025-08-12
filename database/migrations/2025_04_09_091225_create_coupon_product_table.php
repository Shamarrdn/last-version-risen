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
        Schema::create('coupon_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['coupon_id', 'product_id']);
        });

        // Add applies_to_all_products field to coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->boolean('applies_to_all_products')->default(true)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_product');

        // Remove applies_to_all_products field from coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('applies_to_all_products');
        });
    }
};
