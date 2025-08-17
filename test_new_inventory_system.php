<?php
/**
 * ملف اختبار النظام الجديد لإدارة المخزون
 * 
 * هذا الملف يختبر النظام الجديد للتأكد من أنه يعمل بشكل صحيح
 */

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Models\ProductSizeColorInventory;
use App\Models\ProductSize;
use App\Models\ProductColor;

// إعداد قاعدة البيانات
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== اختبار النظام الجديد لإدارة المخزون ===\n\n";

try {
    // 1. اختبار إنشاء مقاسات وألوان افتراضية
    echo "1. إنشاء مقاسات وألوان افتراضية...\n";
    
    // إنشاء مقاسات افتراضية
    $sizes = [
        ['name' => 'XS', 'description' => 'مقاس صغير جداً'],
        ['name' => 'S', 'description' => 'مقاس صغير'],
        ['name' => 'M', 'description' => 'مقاس متوسط'],
        ['name' => 'L', 'description' => 'مقاس كبير'],
        ['name' => 'XL', 'description' => 'مقاس كبير جداً'],
    ];
    
    foreach ($sizes as $sizeData) {
        ProductSize::firstOrCreate(
            ['name' => $sizeData['name']],
            $sizeData
        );
    }
    
    // إنشاء ألوان افتراضية
    $colors = [
        ['name' => 'أحمر', 'code' => '#FF0000', 'description' => 'لون أحمر'],
        ['name' => 'أزرق', 'code' => '#0000FF', 'description' => 'لون أزرق'],
        ['name' => 'أخضر', 'code' => '#00FF00', 'description' => 'لون أخضر'],
        ['name' => 'أسود', 'code' => '#000000', 'description' => 'لون أسود'],
        ['name' => 'أبيض', 'code' => '#FFFFFF', 'description' => 'لون أبيض'],
    ];
    
    foreach ($colors as $colorData) {
        ProductColor::firstOrCreate(
            ['name' => $colorData['name']],
            $colorData
        );
    }
    
    echo "✅ تم إنشاء المقاسات والألوان بنجاح\n\n";
    
    // 2. اختبار إنشاء منتج جديد
    echo "2. إنشاء منتج جديد...\n";
    
    $product = Product::create([
        'name' => 'قميص تجريبي',
        'slug' => 'test-shirt-' . time(),
        'description' => 'قميص تجريبي لاختبار النظام الجديد',
        'category_id' => 1, // تأكد من وجود تصنيف
        'base_price' => 100,
        'stock' => 50,
        'is_available' => true,
    ]);
    
    echo "✅ تم إنشاء المنتج بنجاح (ID: {$product->id})\n\n";
    
    // 3. اختبار إضافة مخزون بالشكل الجديد
    echo "3. إضافة مخزون بالشكل الجديد...\n";
    
    $inventoryData = [
        '1' => [ // size_id = 1 (XS)
            '1' => [ // color_id = 1 (أحمر)
                'stock' => 10,
                'price' => 120.00
            ],
            '2' => [ // color_id = 2 (أزرق)
                'stock' => 15,
                'price' => 125.00
            ]
        ],
        '2' => [ // size_id = 2 (S)
            '1' => [ // color_id = 1 (أحمر)
                'stock' => 20,
                'price' => 130.00
            ],
            '3' => [ // color_id = 3 (أخضر)
                'stock' => 12,
                'price' => 135.00
            ]
        ]
    ];
    
    foreach ($inventoryData as $sizeId => $colors) {
        foreach ($colors as $colorId => $data) {
            ProductSizeColorInventory::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'size_id'    => $sizeId,
                    'color_id'   => $colorId,
                ],
                [
                    'stock'        => $data['stock'],
                    'price'        => $data['price'],
                    'is_available' => true,
                ]
            );
        }
    }
    
    echo "✅ تم إضافة المخزون بنجاح\n\n";
    
    // 4. اختبار استرجاع البيانات
    echo "4. استرجاع البيانات المحفوظة...\n";
    
    $inventory = ProductSizeColorInventory::where('product_id', $product->id)
        ->with(['size', 'color'])
        ->get();
    
    echo "عدد سجلات المخزون: " . $inventory->count() . "\n";
    
    foreach ($inventory as $item) {
        echo "- المقاس: " . ($item->size ? $item->size->name : 'غير محدد');
        echo " | اللون: " . ($item->color ? $item->color->name : 'غير محدد');
        echo " | المخزون: " . $item->stock;
        echo " | السعر: " . $item->price . " ر.س\n";
    }
    
    echo "\n✅ تم استرجاع البيانات بنجاح\n\n";
    
    // 5. اختبار التحقق من البيانات
    echo "5. التحقق من صحة البيانات...\n";
    
    $errors = [];
    
    foreach ($inventory as $item) {
        if ($item->color_id === null) {
            $errors[] = "color_id is NULL for inventory ID: {$item->id}";
        }
        if ($item->stock === 0) {
            $errors[] = "stock is 0 for inventory ID: {$item->id}";
        }
    }
    
    if (empty($errors)) {
        echo "✅ جميع البيانات صحيحة - لا توجد أخطاء\n";
    } else {
        echo "❌ تم العثور على أخطاء:\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
    echo "\n=== انتهى الاختبار ===\n";
    
} catch (Exception $e) {
    echo "❌ حدث خطأ: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
