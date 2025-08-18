<?php
/**
 * ملف اختبار نظام المخزون
 * استخدم هذا الملف لاختبار النظام
 */

// تأكد من أن Laravel مُحمل
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Product;
use App\Models\ProductSizeColorInventory;
use App\Models\ProductSize;
use App\Models\ProductColor;

echo "🔍 اختبار نظام المخزون\n";
echo "========================\n\n";

// 1. اختبار المقاسات والألوان المتاحة
echo "1. المقاسات المتاحة:\n";
$sizes = ProductSize::all();
foreach ($sizes as $size) {
    echo "   - ID: {$size->id}, Name: {$size->name}\n";
}

echo "\n2. الألوان المتاحة:\n";
$colors = ProductColor::all();
foreach ($colors as $color) {
    echo "   - ID: {$color->id}, Name: {$color->name}\n";
}

// 2. اختبار المنتجات الموجودة
echo "\n3. المنتجات الموجودة:\n";
$products = Product::with('inventory')->get();
foreach ($products as $product) {
    echo "   - ID: {$product->id}, Name: {$product->name}\n";
    echo "     Inventory count: " . $product->inventory->count() . "\n";
    
    // عرض تفاصيل المخزون
    foreach ($product->inventory as $inventory) {
        echo "       Size: {$inventory->size_id}, Color: {$inventory->color_id}, Stock: {$inventory->stock}, Price: {$inventory->price}\n";
    }
}

// 3. اختبار السجلات بدون color_id
echo "\n4. السجلات بدون color_id:\n";
$nullColorRecords = ProductSizeColorInventory::whereNull('color_id')->get();
echo "   Count: " . $nullColorRecords->count() . "\n";
foreach ($nullColorRecords as $record) {
    echo "   - Product: {$record->product_id}, Size: {$record->size_id}, Color: NULL, Stock: {$record->stock}\n";
}

// 4. اختبار السجلات بدون size_id
echo "\n5. السجلات بدون size_id:\n";
$nullSizeRecords = ProductSizeColorInventory::whereNull('size_id')->get();
echo "   Count: " . $nullSizeRecords->count() . "\n";
foreach ($nullSizeRecords as $record) {
    echo "   - Product: {$record->product_id}, Size: NULL, Color: {$record->color_id}, Stock: {$record->stock}\n";
}

// 5. إحصائيات عامة
echo "\n6. إحصائيات عامة:\n";
$totalInventory = ProductSizeColorInventory::count();
$validInventory = ProductSizeColorInventory::whereNotNull('color_id')->whereNotNull('size_id')->count();
echo "   Total inventory records: {$totalInventory}\n";
echo "   Valid inventory records: {$validInventory}\n";
echo "   Invalid records: " . ($totalInventory - $validInventory) . "\n";

echo "\n✅ انتهى الاختبار\n";
?>
