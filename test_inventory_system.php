<?php
/**
 * ملف اختبار نظام المقاسات والألوان
 * استخدم هذا الملف للتحقق من حالة النظام قبل وبعد الإصلاحات
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\ProductSizeColorInventory;

echo "🔍 اختبار نظام المقاسات والألوان\n";
echo "================================\n\n";

try {
    // 1. التحقق من وجود الجداول
    echo "1. التحقق من وجود الجداول:\n";
    
    $tables = [
        'product_sizes' => 'جدول المقاسات',
        'product_colors' => 'جدول الألوان',
        'product_size_color_inventory' => 'جدول المخزون',
        'products' => 'جدول المنتجات'
    ];
    
    foreach ($tables as $table => $description) {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        echo "   - {$description} ({$table}): " . ($exists ? "✅ موجود" : "❌ غير موجود") . "\n";
    }
    
    echo "\n";
    
    // 2. التحقق من البيانات الموجودة
    echo "2. التحقق من البيانات الموجودة:\n";
    
    $sizesCount = ProductSize::count();
    $colorsCount = ProductColor::count();
    $productsCount = Product::count();
    $inventoryCount = ProductSizeColorInventory::count();
    
    echo "   - عدد المقاسات: {$sizesCount}\n";
    echo "   - عدد الألوان: {$colorsCount}\n";
    echo "   - عدد المنتجات: {$productsCount}\n";
    echo "   - عدد سجلات المخزون: {$inventoryCount}\n";
    
    echo "\n";
    
    // 3. عرض المقاسات والألوان المتاحة
    if ($sizesCount > 0) {
        echo "3. المقاسات المتاحة:\n";
        $sizes = ProductSize::all();
        foreach ($sizes as $size) {
            echo "   - ID: {$size->id}, الاسم: {$size->name}, الوصف: {$size->description}\n";
        }
        echo "\n";
    }
    
    if ($colorsCount > 0) {
        echo "4. الألوان المتاحة:\n";
        $colors = ProductColor::all();
        foreach ($colors as $color) {
            echo "   - ID: {$color->id}, الاسم: {$color->name}, الكود: {$color->code}\n";
        }
        echo "\n";
    }
    
    // 4. عرض المنتجات مع مخزونها
    if ($productsCount > 0) {
        echo "5. المنتجات مع مخزونها:\n";
        $products = Product::with(['inventory.size', 'inventory.color'])->get();
        
        foreach ($products as $product) {
            echo "   المنتج: {$product->name} (ID: {$product->id})\n";
            
            if ($product->inventory->count() > 0) {
                foreach ($product->inventory as $inventory) {
                    $sizeName = $inventory->size ? $inventory->size->name : 'غير محدد';
                    $colorName = $inventory->color ? $inventory->color->name : 'غير محدد';
                    echo "     - المقاس: {$sizeName}, اللون: {$colorName}, المخزون: {$inventory->stock}, السعر: {$inventory->price}\n";
                }
            } else {
                echo "     - لا يوجد مخزون محدد\n";
            }
            echo "\n";
        }
    }
    
    // 5. اختبار العلاقات
    echo "6. اختبار العلاقات:\n";
    
    if ($productsCount > 0) {
        $testProduct = Product::first();
        echo "   - المنتج الأول: {$testProduct->name}\n";
        
        $sizes = $testProduct->sizes;
        $colors = $testProduct->colors;
        $inventory = $testProduct->inventory;
        
        echo "     - المقاسات المرتبطة: {$sizes->count()}\n";
        echo "     - الألوان المرتبطة: {$colors->count()}\n";
        echo "     - سجلات المخزون: {$inventory->count()}\n";
    }
    
    echo "\n";
    
    // 6. التحقق من صحة البيانات
    echo "7. التحقق من صحة البيانات:\n";
    
    $invalidInventory = ProductSizeColorInventory::where(function($query) {
        $query->whereNull('product_id')
              ->orWhereNull('stock')
              ->orWhere('stock', '<', 0);
    })->count();
    
    echo "   - سجلات مخزون غير صحيحة: {$invalidInventory}\n";
    
    // 7. إحصائيات عامة
    echo "\n8. إحصائيات عامة:\n";
    
    $avgStock = ProductSizeColorInventory::avg('stock');
    $maxStock = ProductSizeColorInventory::max('stock');
    $minStock = ProductSizeColorInventory::min('stock');
    
    echo "   - متوسط المخزون: " . round($avgStock, 2) . "\n";
    echo "   - أعلى مخزون: {$maxStock}\n";
    echo "   - أقل مخزون: {$minStock}\n";
    
    // 8. توصيات
    echo "\n9. التوصيات:\n";
    
    if ($sizesCount === 0) {
        echo "   ⚠️  لا توجد مقاسات في قاعدة البيانات. يجب إنشاء مقاسات افتراضية.\n";
    }
    
    if ($colorsCount === 0) {
        echo "   ⚠️  لا توجد ألوان في قاعدة البيانات. يجب إنشاء ألوان افتراضية.\n";
    }
    
    if ($inventoryCount === 0) {
        echo "   ℹ️  لا توجد سجلات مخزون. هذا طبيعي إذا لم يتم إضافة منتجات بعد.\n";
    }
    
    if ($invalidInventory > 0) {
        echo "   ⚠️  توجد سجلات مخزون غير صحيحة. يجب مراجعتها.\n";
    }
    
    echo "\n✅ تم الانتهاء من الاختبار بنجاح!\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في الاختبار: " . $e->getMessage() . "\n";
    echo "التفاصيل: " . $e->getTraceAsString() . "\n";
}

echo "\n";
echo "📝 ملاحظات:\n";
echo "- استخدم هذا الملف للتحقق من حالة النظام قبل وبعد الإصلاحات\n";
echo "- إذا كانت هناك مشاكل، راجع الـ logs في storage/logs/laravel.log\n";
echo "- تأكد من تشغيل الـ migrations قبل الاختبار\n";
echo "- استخدم php artisan migrate إذا لم تكن الجداول موجودة\n";
?>
