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
        Schema::table('product_size_color_inventory', function (Blueprint $table) {
            $table->unique(['product_id', 'size_id', 'color_id'], 'psci_unique_triplet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_size_color_inventory', function (Blueprint $table) {
            $table->dropUnique('psci_unique_triplet');
        });
    }
};
