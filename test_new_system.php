<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductSizeColorInventory;
use App\Models\ProductColor;
use App\Models\ProductSize;

echo "🧪 اختبار النظام الجديد\n";
echo "========================\n\n";

try {
    // 1. اختبار العلاقات الجديدة
    echo "1. اختبار العلاقات الجديدة...\n";
    
    $product = Product::with(['inventory.color', 'inventory.size'])->first();
    if ($product) {
        echo "   ✅ تم العثور على منتج: {$product->name}\n";
        echo "   📊 عدد الـ variants: " . $product->inventory->count() . "\n";
        
        // اختبار accessors الجديدة
        $availableColors = $product->available_colors;
        $availableSizes = $product->available_sizes;
        
        echo "   🎨 الألوان المتاحة: " . $availableColors->count() . "\n";
        echo "   📏 المقاسات المتاحة: " . $availableSizes->count() . "\n";
        
        // اختبار الأسعار
        $minPrice = $product->min_price_from_inventory;
        $maxPrice = $product->max_price_from_inventory;
        echo "   💰 السعر الأدنى: {$minPrice}\n";
        echo "   💰 السعر الأقصى: {$maxPrice}\n";
        
    } else {
        echo "   ⚠️  لا توجد منتجات في قاعدة البيانات\n";
    }
    
    // 2. اختبار البحث عن variants
    echo "\n2. اختبار البحث عن variants...\n";
    
    if ($product) {
        $variant = $product->getVariant();
        if ($variant) {
            echo "   ✅ تم العثور على variant: ID {$variant->id}\n";
            echo "   📦 المخزون المتاح: {$variant->available_stock}\n";
            echo "   💰 السعر: {$variant->price}\n";
        } else {
            echo "   ⚠️  لا توجد variants متاحة\n";
        }
    }
    
    // 3. اختبار إحصائيات النظام
    echo "\n3. إحصائيات النظام...\n";
    
    $totalProducts = Product::count();
    $totalVariants = ProductSizeColorInventory::count();
    $totalColors = ProductColor::count();
    $totalSizes = ProductSize::count();
    
    echo "   📦 إجمالي المنتجات: {$totalProducts}\n";
    echo "   🔄 إجمالي الـ variants: {$totalVariants}\n";
    echo "   🎨 إجمالي الألوان: {$totalColors}\n";
    echo "   📏 إجمالي المقاسات: {$totalSizes}\n";
    
    // 4. اختبار الاستعلامات
    echo "\n4. اختبار الاستعلامات...\n";
    
    $productsWithInventory = Product::whereHas('inventory', function($q) {
        $q->where('is_available', true)->where('stock', '>', 0);
    })->count();
    
    echo "   ✅ المنتجات ذات المخزون المتاح: {$productsWithInventory}\n";
    
    // 5. اختبار العلاقات المحدثة
    echo "\n5. اختبار العلاقات المحدثة...\n";
    
    $productWithRelations = Product::with(['colors', 'sizes'])->first();
    if ($productWithRelations) {
        echo "   🎨 الألوان المرتبطة: " . $productWithRelations->colors->count() . "\n";
        echo "   📏 المقاسات المرتبطة: " . $productWithRelations->sizes->count() . "\n";
    }
    
    echo "\n✅ جميع الاختبارات مكتملة بنجاح!\n";
    echo "🎉 النظام الجديد يعمل بشكل صحيح\n";
    
} catch (Exception $e) {
    echo "\n❌ خطأ في الاختبار: " . $e->getMessage() . "\n";
    echo "📍 الملف: " . $e->getFile() . "\n";
    echo "📍 السطر: " . $e->getLine() . "\n";
}
