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
        Schema::create('order_friends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id'); // صاحب الطلب
            $table->string('friend_email'); // بريد الصديق
            $table->string('friend_name'); // اسم الصديق
            $table->string('friend_phone')->nullable(); // رقم هاتف الصديق
            $table->string('access_token')->unique(); // رمز الوصول الفريد
            $table->boolean('is_active')->default(true); // هل الصديق نشط
            $table->timestamp('last_accessed_at')->nullable(); // آخر مرة تم الوصول
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['order_id', 'is_active']);
            $table->index(['access_token']);
            $table->index(['friend_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_friends');
    }
};
