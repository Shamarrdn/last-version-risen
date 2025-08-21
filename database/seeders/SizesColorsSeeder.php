<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductSize;
use App\Models\ProductColor;

class SizesColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة المقاسات الافتراضية
        $sizes = [
            ['name' => 'XS', 'description' => 'مقاس صغير جداً'],
            ['name' => 'S', 'description' => 'مقاس صغير'],
            ['name' => 'M', 'description' => 'مقاس متوسط'],
            ['name' => 'L', 'description' => 'مقاس كبير'],
            ['name' => 'XL', 'description' => 'مقاس كبير جداً'],
            ['name' => '2XL', 'description' => 'مقاس كبير جداً جداً'],
            ['name' => '3XL', 'description' => 'مقاس كبير جداً جداً جداً'],
            ['name' => '4XL', 'description' => 'مقاس كبير جداً جداً جداً جداً'],
            ['name' => '5XL', 'description' => 'مقاس كبير جداً جداً جداً جداً جداً'],
        ];

        foreach ($sizes as $size) {
            ProductSize::firstOrCreate(
                ['name' => $size['name']],
                $size
            );
        }

        // إضافة الألوان الافتراضية
        $colors = [
            ['name' => 'أحمر', 'code' => '#FF0000', 'description' => 'لون أحمر نابض'],
            ['name' => 'أزرق', 'code' => '#0000FF', 'description' => 'لون أزرق كلاسيكي'],
            ['name' => 'أخضر', 'code' => '#00FF00', 'description' => 'لون أخضر طبيعي'],
            ['name' => 'أصفر', 'code' => '#FFFF00', 'description' => 'لون أصفر مشرق'],
            ['name' => 'برتقالي', 'code' => '#FFA500', 'description' => 'لون برتقالي دافئ'],
            ['name' => 'بنفسجي', 'code' => '#800080', 'description' => 'لون بنفسجي أنيق'],
            ['name' => 'وردي', 'code' => '#FFC0CB', 'description' => 'لون وردي ناعم'],
            ['name' => 'بني', 'code' => '#A52A2A', 'description' => 'لون بني طبيعي'],
            ['name' => 'رمادي', 'code' => '#808080', 'description' => 'لون رمادي محايد'],
            ['name' => 'أسود', 'code' => '#000000', 'description' => 'لون أسود كلاسيكي'],
            ['name' => 'أبيض', 'code' => '#FFFFFF', 'description' => 'لون أبيض نقي'],
            ['name' => 'ذهبي', 'code' => '#FFD700', 'description' => 'لون ذهبي فاخر'],
            ['name' => 'فضي', 'code' => '#C0C0C0', 'description' => 'لون فضي أنيق'],
            ['name' => 'أزرق داكن', 'code' => '#000080', 'description' => 'لون أزرق داكن'],
            ['name' => 'أخضر داكن', 'code' => '#006400', 'description' => 'لون أخضر داكن'],
        ];

        foreach ($colors as $color) {
            ProductColor::firstOrCreate(
                ['name' => $color['name']],
                $color
            );
        }

        $this->command->info('تم إضافة المقاسات والألوان الافتراضية بنجاح!');
    }
}







