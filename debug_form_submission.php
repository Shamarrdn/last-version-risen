<?php
/**
 * ملف تشخيص إرسال النماذج
 * يستخدم لمحاكاة البيانات المرسلة من النماذج واختبار معالجتها
 */

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ProductController;

echo "🔍 تشخيص إرسال النماذج\n";
echo "======================\n\n";

// محاكاة بيانات النموذج - إنشاء منتج جديد
echo "1. محاكاة بيانات إنشاء منتج جديد:\n";

$createData = [
    'name' => 'منتج تجريبي',
    'description' => 'وصف المنتج التجريبي',
    'category_id' => 1,
    'base_price' => 100.00,
    'stock' => 50,
    'is_available' => 1,
    
    // بيانات المقاسات والألوان
    'selected_sizes' => [1, 2, 3], // IDs للمقاسات
    'selected_colors' => [1, 2],   // IDs للألوان
    
    // بيانات المخزون والسعر
    'stock' => [
        1 => [1 => 10, 2 => 15], // stock[size_id][color_id]
        2 => [1 => 20, 2 => 25],
        3 => [1 => 30, 2 => 35]
    ],
    
    'price' => [
        1 => [1 => 100.00, 2 => 110.00], // price[size_id][color_id]
        2 => [1 => 120.00, 2 => 130.00],
        3 => [1 => 140.00, 2 => 150.00]
    ]
];

echo "   البيانات المرسلة:\n";
echo "   - المقاسات: " . implode(', ', $createData['selected_sizes']) . "\n";
echo "   - الألوان: " . implode(', ', $createData['selected_colors']) . "\n";
echo "   - عدد تركيبات المخزون: " . count($createData['stock']) . "\n";
echo "   - عدد تركيبات الأسعار: " . count($createData['price']) . "\n";

// محاكاة بيانات النموذج - تعديل منتج
echo "\n2. محاكاة بيانات تعديل منتج:\n";

$updateData = [
    'name' => 'منتج معدل',
    'description' => 'وصف المنتج المعدل',
    'category_id' => 1,
    'base_price' => 120.00,
    'stock' => 60,
    'is_available' => 1,
    '_method' => 'PUT',
    
    // بيانات المقاسات والألوان المحدثة
    'selected_sizes' => [1, 2], // حذف مقاس واحد
    'selected_colors' => [1, 2, 3], // إضافة لون جديد
    
    // بيانات المخزون والسعر المحدثة
    'stock' => [
        1 => [1 => 15, 2 => 20, 3 => 25], // إضافة لون جديد
        2 => [1 => 25, 2 => 30, 3 => 35]  // إضافة لون جديد
    ],
    
    'price' => [
        1 => [1 => 110.00, 2 => 120.00, 3 => 130.00],
        2 => [1 => 130.00, 2 => 140.00, 3 => 150.00]
    ]
];

echo "   البيانات المرسلة:\n";
echo "   - المقاسات: " . implode(', ', $updateData['selected_sizes']) . "\n";
echo "   - الألوان: " . implode(', ', $updateData['selected_colors']) . "\n";
echo "   - عدد تركيبات المخزون: " . count($updateData['stock']) . "\n";
echo "   - عدد تركيبات الأسعار: " . count($updateData['price']) . "\n";

// اختبار معالجة البيانات
echo "\n3. اختبار معالجة البيانات:\n";

try {
    // محاكاة Request object
    $request = new Request($createData);
    
    echo "   - تم إنشاء Request object بنجاح\n";
    echo "   - عدد الحقول: " . count($request->all()) . "\n";
    
    // اختبار استخراج البيانات
    $selectedSizes = $request->get('selected_sizes', []);
    $selectedColors = $request->get('selected_colors', []);
    $stockData = $request->get('stock', []);
    $priceData = $request->get('price', []);
    
    echo "   - المقاسات المستخرجة: " . implode(', ', $selectedSizes) . "\n";
    echo "   - الألوان المستخرجة: " . implode(', ', $selectedColors) . "\n";
    echo "   - بيانات المخزون: " . count($stockData) . " مقاس\n";
    echo "   - بيانات الأسعار: " . count($priceData) . " مقاس\n";
    
    // اختبار بناء البيانات النهائية
    $finalData = [];
    
    foreach ($selectedSizes as $sizeId) {
        foreach ($selectedColors as $colorId) {
            $stock = $stockData[$sizeId][$colorId] ?? 0;
            $price = $priceData[$sizeId][$colorId] ?? 0;
            
            $finalData[] = [
                'size_id' => $sizeId,
                'color_id' => $colorId,
                'stock' => $stock,
                'price' => $price
            ];
        }
    }
    
    echo "   - عدد التركيبات النهائية: " . count($finalData) . "\n";
    
    foreach ($finalData as $index => $item) {
        $indexNum = $index + 1;
        echo "     {$indexNum}. المقاس: {$item['size_id']}, اللون: {$item['color_id']}, المخزون: {$item['stock']}, السعر: {$item['price']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ خطأ في معالجة البيانات: " . $e->getMessage() . "\n";
}

// اختبار صحة البيانات
echo "\n4. اختبار صحة البيانات:\n";

$validationRules = [
    'name' => 'required|string|max:255',
    'description' => 'required|string',
    'category_id' => 'required|exists:categories,id',
    'selected_sizes' => 'nullable|array',
    'selected_sizes.*' => 'exists:product_sizes,id',
    'selected_colors' => 'nullable|array',
    'selected_colors.*' => 'exists:product_colors,id',
    'stock' => 'required|numeric|min:0',
    'base_price' => 'nullable|numeric|min:0'
];

echo "   - قواعد الفاليديشن:\n";
foreach ($validationRules as $field => $rules) {
    echo "     - {$field}: {$rules}\n";
}

// اختبار أسماء الحقول المطلوبة
echo "\n5. أسماء الحقول المطلوبة:\n";

$requiredFields = [
    'selected_sizes[]' => 'مصفوفة المقاسات المختارة',
    'selected_colors[]' => 'مصفوفة الألوان المختارة',
    'stock[size_id][color_id]' => 'المخزون لكل تركيبة مقاس/لون',
    'price[size_id][color_id]' => 'السعر لكل تركيبة مقاس/لون'
];

foreach ($requiredFields as $field => $description) {
    echo "   - {$field}: {$description}\n";
}

// اختبار تنسيق البيانات
echo "\n6. تنسيق البيانات المتوقع:\n";

echo "   البيانات الأساسية:\n";
echo "   - name: نص\n";
echo "   - description: نص\n";
echo "   - category_id: رقم\n";
echo "   - base_price: رقم عشري\n";
echo "   - stock: رقم صحيح\n";
echo "   - is_available: 0 أو 1\n\n";

echo "   بيانات المقاسات والألوان:\n";
echo "   - selected_sizes[]: مصفوفة أرقام\n";
echo "   - selected_colors[]: مصفوفة أرقام\n";
echo "   - stock[size_id][color_id]: مصفوفة متداخلة\n";
echo "   - price[size_id][color_id]: مصفوفة متداخلة\n";

// توصيات
echo "\n7. التوصيات:\n";
echo "   ✅ تأكد من إرسال جميع الحقول المطلوبة\n";
echo "   ✅ تأكد من صحة تنسيق البيانات\n";
echo "   ✅ تأكد من وجود المقاسات والألوان في قاعدة البيانات\n";
echo "   ✅ تأكد من تطابق أسماء الحقول مع ما يتوقعه التحكم\n";
echo "   ✅ استخدم دالة التشخيص في النماذج للتحقق من البيانات\n";

echo "\n✅ تم الانتهاء من التشخيص!\n";

echo "\n📝 ملاحظات:\n";
echo "- استخدم هذا الملف لفهم تنسيق البيانات المطلوب\n";
echo "- تأكد من تطابق البيانات المرسلة مع ما يتوقعه التحكم\n";
echo "- راجع الـ logs للتحقق من معالجة البيانات\n";
echo "- استخدم Developer Tools في المتصفح لمراقبة البيانات المرسلة\n";
?>
