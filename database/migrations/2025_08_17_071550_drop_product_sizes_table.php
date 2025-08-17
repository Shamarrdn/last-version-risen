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
        // حذف الجدول القديم بعد التأكد من نقل البيانات
        Schema::dropIfExists('product_sizes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة إنشاء الجدول القديم إذا لزم الأمر
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('size');
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->foreign('size_id')->references('id')->on('size_options')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('color_options')->onDelete('cascade');
            $table->index(['product_id', 'size_id', 'color_id']);
        });
    }
};
