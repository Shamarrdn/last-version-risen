<?php
// ملف اختبار بسيط
echo "🔍 اختبار بسيط للنظام\n";
echo "====================\n\n";

// التحقق من وجود الملفات
$files = [
    'vendor/autoload.php',
    'config/database.php',
    'app/Models/Product.php',
    'app/Models/ProductSizeColorInventory.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file} موجود\n";
    } else {
        echo "❌ {$file} غير موجود\n";
    }
}

echo "\n📝 ملاحظات:\n";
echo "- تأكد من تشغيل php artisan migrate\n";
echo "- تأكد من إعداد قاعدة البيانات في .env\n";
echo "- جرب إنشاء منتج جديد من خلال الواجهة\n";
echo "- راجع الـ logs في storage/logs/laravel.log\n";
?>


