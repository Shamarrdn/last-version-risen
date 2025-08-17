// ملف اختبار إضافة الألوان
// استخدم هذا في console المتصفح لاختبار الوظيفة

console.log('=== اختبار إضافة الألوان ===');

// دالة اختبار إضافة لون
function testAddColor() {
    console.log('بدء اختبار إضافة لون...');
    
    // التحقق من وجود المتغيرات
    if (typeof selectedSizes === 'undefined') {
        console.error('❌ selectedSizes غير موجود');
        return false;
    }
    
    if (typeof availableColors === 'undefined' || availableColors.length === 0) {
        console.error('❌ availableColors غير موجود أو فارغ');
        return false;
    }
    
    console.log('✅ المتغيرات موجودة');
    console.log('المقاسات الموجودة:', selectedSizes.length);
    console.log('الألوان المتاحة:', availableColors.length);
    
    // اختبار إضافة لون للمقاس الأول
    if (selectedSizes.length > 0) {
        const firstSize = selectedSizes[0];
        console.log('إضافة لون للمقاس:', firstSize.id);
        
        // حفظ عدد الألوان الحالي
        const colorsBefore = firstSize.colors ? firstSize.colors.length : 0;
        console.log('عدد الألوان قبل الإضافة:', colorsBefore);
        
        // إضافة لون
        addColorToSize(firstSize.id);
        
        // التحقق من النتيجة
        const colorsAfter = firstSize.colors ? firstSize.colors.length : 0;
        console.log('عدد الألوان بعد الإضافة:', colorsAfter);
        
        if (colorsAfter > colorsBefore) {
            console.log('✅ تم إضافة اللون بنجاح');
            
            // التحقق من الحقول في الواجهة
            const stockInputs = document.querySelectorAll('input[name*="stock"]');
            const priceInputs = document.querySelectorAll('input[name*="price"]');
            
            console.log('حقول المخزون في الواجهة:', stockInputs.length);
            console.log('حقول الأسعار في الواجهة:', priceInputs.length);
            
            return true;
        } else {
            console.error('❌ فشل في إضافة اللون');
            return false;
        }
    } else {
        console.error('❌ لا توجد مقاسات متاحة');
        return false;
    }
}

// دالة اختبار حفظ البيانات
function testDataPersistence() {
    console.log('=== اختبار حفظ البيانات ===');
    
    if (selectedSizes.length === 0) {
        console.error('❌ لا توجد مقاسات للاختبار');
        return false;
    }
    
    const size = selectedSizes[0];
    if (!size.colors || size.colors.length === 0) {
        console.error('❌ لا توجد ألوان للاختبار');
        return false;
    }
    
    const color = size.colors[0];
    console.log('اختبار اللون:', color.id);
    
    // تعيين قيم تجريبية
    const testStock = '100';
    const testPrice = '50.5';
    
    color.stock = testStock;
    color.price = testPrice;
    
    console.log('تم تعيين القيم التجريبية');
    console.log('Stock:', color.stock);
    console.log('Price:', color.price);
    
    // إضافة لون جديد لاختبار عدم فقدان البيانات
    addColorToSize(size.id);
    
    // التحقق من عدم فقدان البيانات
    if (color.stock === testStock && color.price === testPrice) {
        console.log('✅ البيانات محفوظة بنجاح');
        return true;
    } else {
        console.error('❌ البيانات مفقودة');
        console.log('Stock الآن:', color.stock);
        console.log('Price الآن:', color.price);
        return false;
    }
}

// دالة اختبار شاملة
function runFullTest() {
    console.log('=== بدء الاختبار الشامل ===');
    
    const test1 = testAddColor();
    const test2 = testDataPersistence();
    
    console.log('=== نتائج الاختبار ===');
    console.log('اختبار إضافة اللون:', test1 ? '✅ نجح' : '❌ فشل');
    console.log('اختبار حفظ البيانات:', test2 ? '✅ نجح' : '❌ فشل');
    
    if (test1 && test2) {
        console.log('🎉 جميع الاختبارات نجحت!');
    } else {
        console.log('⚠️ بعض الاختبارات فشلت');
    }
}

// إضافة الدوال للـ window
window.testAddColor = testAddColor;
window.testDataPersistence = testDataPersistence;
window.runFullTest = runFullTest;

console.log('=== الدوال المتاحة ===');
console.log('testAddColor() - اختبار إضافة لون');
console.log('testDataPersistence() - اختبار حفظ البيانات');
console.log('runFullTest() - اختبار شامل');

console.log('استخدم runFullTest() لبدء الاختبار');
