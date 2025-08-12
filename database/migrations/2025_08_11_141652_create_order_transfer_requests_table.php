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
        Schema::create('order_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('from_admin_id'); // الأدمن الحالي
            $table->unsignedBigInteger('to_admin_id');   // الأدمن المطلوب النقل إليه
            $table->text('reason')->nullable(); // سبب النقل
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable(); // سبب الرفض
            $table->timestamp('responded_at')->nullable(); // وقت الرد
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('from_admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_admin_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['order_id', 'status']);
            $table->index(['to_admin_id', 'status']);
            $table->index(['from_admin_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_transfer_requests');
    }
};
