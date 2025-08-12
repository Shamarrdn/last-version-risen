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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('original_amount', 10, 2)->after('total_amount')->nullable();
            $table->decimal('coupon_discount', 10, 2)->after('original_amount')->default(0);
            $table->string('coupon_code')->after('coupon_discount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('original_amount');
            $table->dropColumn('coupon_discount');
            $table->dropColumn('coupon_code');
        });
    }
};
