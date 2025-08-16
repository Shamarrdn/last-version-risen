<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Get the first available color (red)
        $firstColor = DB::table('color_options')->first();
        
        if ($firstColor) {
            // Update all product_colors records to use the first available color
            DB::table('product_colors')->update(['color_id' => $firstColor->id]);
        }
    }

    public function down()
    {
        // Reset all color_id to 0
        DB::table('product_colors')->update(['color_id' => 0]);
    }
};
