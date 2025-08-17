// أضف هذا الكود في console المتصفح لتشخيص مشكلة النموذج

console.log('=== تشخيص النموذج ===');

// التحقق من وجود المتغيرات العامة
console.log('1. التحقق من المتغيرات:');
console.log('selectedSizes:', typeof selectedSizes !== 'undefined' ? selectedSizes : 'غير موجود');
console.log('availableSizes:', typeof availableSizes !== 'undefined' ? availableSizes : 'غير موجود');
console.log('availableColors:', typeof availableColors !== 'undefined' ? availableColors : 'غير موجود');

// التحقق من النموذج
console.log('\n2. التحقق من النموذج:');
const form = document.querySelector('form');
console.log('Form element:', form ? '✓ موجود' : '✗ غير موجود');

if (form) {
    // التحقق من الحقول الموجودة
    console.log('\n3. الحقول الموجودة:');
    const selectedSizesInputs = form.querySelectorAll('input[name="selected_sizes[]"]');
    const selectedColorsInputs = form.querySelectorAll('input[name="selected_colors[]"]');
    const stockInputs = form.querySelectorAll('input[name*="stock["]');
    const priceInputs = form.querySelectorAll('input[name*="price["]');
    
    console.log('selected_sizes[] inputs:', selectedSizesInputs.length);
    console.log('selected_colors[] inputs:', selectedColorsInputs.length);
    console.log('stock inputs:', stockInputs.length);
    console.log('price inputs:', priceInputs.length);
    
    // عرض قيم selected_sizes
    if (selectedSizesInputs.length > 0) {
        console.log('قيم selected_sizes:');
        selectedSizesInputs.forEach((input, index) => {
            console.log(`  [${index}]: ${input.value}`);
        });
    }
    
    // عرض قيم selected_colors
    if (selectedColorsInputs.length > 0) {
        console.log('قيم selected_colors:');
        selectedColorsInputs.forEach((input, index) => {
            console.log(`  [${index}]: ${input.value}`);
        });
    }
    
    // عرض قيم stock
    if (stockInputs.length > 0) {
        console.log('قيم stock:');
        stockInputs.forEach((input, index) => {
            console.log(`  [${index}]: ${input.name} = ${input.value}`);
        });
    }
    
    // عرض قيم price
    if (priceInputs.length > 0) {
        console.log('قيم price:');
        priceInputs.forEach((input, index) => {
            console.log(`  [${index}]: ${input.name} = ${input.value}`);
        });
    }
}

// دالة لمحاكاة إعداد البيانات يدوياً
console.log('\n4. دالة الإصلاح اليدوي:');
console.log('استخدم: fixFormData() لإصلاح البيانات قبل الإرسال');

window.fixFormData = function() {
    console.log('بدء إصلاح البيانات...');
    
    if (typeof selectedSizes === 'undefined') {
        console.error('selectedSizes غير موجود!');
        return false;
    }
    
    const form = document.querySelector('form');
    if (!form) {
        console.error('النموذج غير موجود!');
        return false;
    }
    
    // إزالة الحقول القديمة
    const oldInputs = form.querySelectorAll('input[name^="selected_sizes"], input[name^="selected_colors"], input[name^="stock["], input[name^="price["]');
    console.log(`إزالة ${oldInputs.length} حقل قديم`);
    oldInputs.forEach(input => input.remove());
    
    // إضافة المقاسات
    const sizesAdded = [];
    const colorsAdded = [];
    const stockAdded = [];
    const priceAdded = [];
    
    selectedSizes.forEach(size => {
        if (size.id && !String(size.id).includes('temp_')) {
            // إضافة المقاس
            const sizeInput = document.createElement('input');
            sizeInput.type = 'hidden';
            sizeInput.name = 'selected_sizes[]';
            sizeInput.value = size.id;
            form.appendChild(sizeInput);
            sizesAdded.push(size.id);
            
            // إضافة الألوان لهذا المقاس
            if (size.colors && size.colors.length > 0) {
                size.colors.forEach(color => {
                    if (color.id && !String(color.id).includes('temp_')) {
                        // إضافة اللون
                        const colorInput = document.createElement('input');
                        colorInput.type = 'hidden';
                        colorInput.name = 'selected_colors[]';
                        colorInput.value = color.id;
                        form.appendChild(colorInput);
                        colorsAdded.push(color.id);
                        
                        // إضافة الكمية
                        if (color.stock) {
                            const stockInput = document.createElement('input');
                            stockInput.type = 'hidden';
                            stockInput.name = `stock[${size.id}][${color.id}]`;
                            stockInput.value = color.stock;
                            form.appendChild(stockInput);
                            stockAdded.push(`${size.id}-${color.id}: ${color.stock}`);
                        }
                        
                        // إضافة السعر
                        if (color.price) {
                            const priceInput = document.createElement('input');
                            priceInput.type = 'hidden';
                            priceInput.name = `price[${size.id}][${color.id}]`;
                            priceInput.value = color.price;
                            form.appendChild(priceInput);
                            priceAdded.push(`${size.id}-${color.id}: ${color.price}`);
                        }
                    }
                });
            }
        }
    });
    
    console.log('✅ تم إضافة:');
    console.log('المقاسات:', sizesAdded);
    console.log('الألوان:', [...new Set(colorsAdded)]);
    console.log('الكميات:', stockAdded);
    console.log('الأسعار:', priceAdded);
    
    return true;
};

console.log('\n=== انتهى التشخيص ===');
console.log('استخدم fixFormData() قبل إرسال النموذج إذا كانت هناك مشكلة');
