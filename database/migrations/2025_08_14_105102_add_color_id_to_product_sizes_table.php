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
        Schema::table('product_sizes', function (Blueprint $table) {
            // إضافة size_id للربط مع جدول size_options
            $table->unsignedBigInteger('size_id')->nullable()->after('size');
            $table->foreign('size_id')->references('id')->on('size_options')->onDelete('cascade');
            
            // إضافة color_id للربط مع الألوان
            $table->unsignedBigInteger('color_id')->nullable()->after('size_id');
            $table->foreign('color_id')->references('id')->on('color_options')->onDelete('cascade');
            
            // إضافة stock للكمية المتاحة
            $table->integer('stock')->default(0)->after('price');
            
            // إضافة index للتحسين
            $table->index(['product_id', 'size_id', 'color_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropForeign(['size_id', 'color_id']);
            $table->dropIndex(['product_id', 'size_id', 'color_id']);
            $table->dropColumn(['size_id', 'color_id', 'stock']);
        });
    }
};
