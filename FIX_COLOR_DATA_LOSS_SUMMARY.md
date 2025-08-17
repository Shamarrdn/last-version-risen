# إصلاح مشكلة مسح بيانات الألوان عند الإضافة

## المشكلة
عند إضافة لون جديد، كانت الدالة `updateSizeColorMatrix()` تمسح كل البيانات المدخلة (الكميات والأسعار) لجميع الألوان الأخرى.

## السبب
الدالة `updateSizeColorMatrix()` كانت تستخدم `innerHTML = ''` لمسح المحتوى وإعادة إنشائه، لكنها كانت تفقد البيانات المدخلة في العملية.

## الحل المطبق

### 1. **حفظ البيانات قبل الإعادة الإنشاء**
```javascript
// حفظ قيم المخزون في selectedSizes
stockInputs.forEach(input => {
    const matches = input.name.match(/stock\[([^\]]+)\]\[([^\]]+)\]/);
    if (matches) {
        const sizeId = matches[1];
        const colorId = matches[2];
        const value = input.value;
        
        // العثور على المقاس واللون في selectedSizes
        const size = selectedSizes.find(s => String(s.id) === String(sizeId));
        if (size && size.colors) {
            const color = size.colors.find(c => String(c.id) === String(colorId));
            if (color) {
                color.stock = value;
            }
        }
    }
});
```

### 2. **استرجاع البيانات المحفوظة**
```javascript
value="${color.stock || ''}"
value="${color.price || ''}"
```

### 3. **إضافة زر التشخيص**
- زر "تشخيص البيانات" يساعد في رؤية حالة النموذج
- يظهر عدد الحقول الموجودة والبيانات المحفوظة
- يحضر البيانات تلقائياً إذا لزم الأمر

## الملفات المعدلة

### 1. `resources/views/admin/products/edit.blade.php`
- إصلاح دالة `updateSizeColorMatrix()`
- إضافة زر التشخيص
- إضافة دالة `debugFormData()`

### 2. `resources/views/admin/products/create.blade.php`
- نفس الإصلاحات المطبقة على edit
- إضافة نفس أدوات التشخيص

## كيفية الاختبار

### اختبار البيانات لا تضيع:
1. أضف مقاس جديد
2. أضف لون واكتب كمية وسعر
3. أضف لون ثاني واكتب كمية وسعر  
4. **تأكد أن بيانات اللون الأول لم تختفي**
5. أضف مقاس ثاني مع ألوان
6. **تأكد أن جميع البيانات محفوظة**

### استخدام التشخيص:
1. اضغط زر "تشخيص البيانات" أي وقت
2. راجع الرسالة المنبثقة
3. راجع Console للتفاصيل الكاملة
4. تأكد أن الحقول المخفية موجودة

## التحسينات المضافة

### 1. **تسجيل مفصل**
```javascript
console.log(`Saved stock: ${sizeId}-${colorId} = ${value}`);
console.log(`Saved price: ${sizeId}-${colorId} = ${value}`);
```

### 2. **معالجة أخطاء محسنة**
- التأكد من وجود المقاس واللون قبل الحفظ
- رسائل تشخيصية واضحة

### 3. **أدوات التشخيص**
- زر تشخيص مؤقت في الواجهة
- دالة `debugFormData()` عامة
- ملف `debug_form_console.js` للاستخدام في Console

## ملاحظات مهمة

### البيانات محفوظة في مكانين:
1. **في `selectedSizes`**: البيانات الأساسية في الذاكرة
2. **في حقول النموذج**: البيانات المرسلة للسيرفر

### الدالة تضمن:
- حفظ البيانات من الحقول إلى `selectedSizes`
- استرجاع البيانات من `selectedSizes` عند الإعادة الإنشاء
- عدم فقدان أي بيانات عند إضافة عناصر جديدة

## الخطوات التالية
1. **اختبر الوظيفة** كما هو موضح أعلاه
2. **استخدم زر التشخيص** لمراقبة النموذج
3. **راجع Console** للتأكد من حفظ البيانات
4. **أزل زر التشخيص** عندما تتأكد أن كل شيء يعمل

## في حالة المشاكل
- استخدم `debug_form_console.js` في Console
- راجع `storage/logs/laravel.log`
- تحقق من Network tab في Developer Tools
