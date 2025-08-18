/**
 * Admin Products JavaScript
 * Shared functionality for product create/edit pages
 */

// Global variables
window.AdminProducts = window.AdminProducts || {};
window.AdminProducts.inventoryRowCounter = 0;
window.AdminProducts.selectedSizes = [];
window.AdminProducts.availableSizes = [];
window.AdminProducts.availableColors = [];

/**
 * Initialize admin products functionality
 */
window.AdminProducts.init = function(config) {
    // Set configuration
    if (config) {
        this.availableSizes = config.availableSizes || [];
        this.availableColors = config.availableColors || [];
        this.selectedSizes = config.selectedSizes || [];
    }
    
    console.log('✅ AdminProducts initialized');
    console.log('Available sizes:', this.availableSizes.length);
    console.log('Available colors:', this.availableColors.length);
};

/**
 * Add new inventory row
 */
window.AdminProducts.addInventoryRow = function() {
    const matrixContainer = document.getElementById('inventoryMatrix');
    if (!matrixContainer) {
        console.error('❌ Inventory matrix container not found');
        alert('خطأ: لم يتم العثور على حاوية المخزون');
        return;
    }
    
    const rowId = 'inventory-row-' + this.inventoryRowCounter++;
    
    // Build size options
    const sizeOptions = this.availableSizes.map(size => 
        `<option value="${size.id}">${size.name}${size.description ? ' - ' + size.description : ''}</option>`
    ).join('');
    
    // Build color options
    const colorOptions = this.availableColors.map(color => 
        `<option value="${color.id}">${color.name}${color.description ? ' - ' + color.description : ''}</option>`
    ).join('');
    
    const rowHtml = `
        <div class="inventory-row card mb-3" id="${rowId}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-cube me-2"></i>
                    مجموعة مخزون جديدة
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="AdminProducts.removeInventoryRow('${rowId}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">المقاس</label>
                        <select class="form-select size-select" name="inventories[${rowId}][size_id]" required>
                            <option value="">اختر المقاس...</option>
                            ${sizeOptions}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">اللون</label>
                        <select class="form-select color-select" name="inventories[${rowId}][color_id]" required>
                            <option value="">اختر اللون...</option>
                            ${colorOptions}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الكمية</label>
                        <input type="number" class="form-control stock-input" 
                               name="inventories[${rowId}][stock]" 
                               min="0" value="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">السعر</label>
                        <input type="number" class="form-control price-input" 
                               name="inventories[${rowId}][price]" 
                               min="0" step="0.01" value="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">متاح؟</label>
                        <select class="form-select" name="inventories[${rowId}][is_available]">
                            <option value="1">متاح</option>
                            <option value="0">غير متاح</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    matrixContainer.insertAdjacentHTML('beforeend', rowHtml);
    console.log('✅ Added new inventory row:', rowId);
};

/**
 * Remove inventory row
 */
window.AdminProducts.removeInventoryRow = function(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        if (confirm('هل أنت متأكد من حذف هذه المجموعة؟')) {
            row.remove();
            console.log('✅ Removed inventory row:', rowId);
        }
    }
};

/**
 * Debug form data
 */
window.AdminProducts.debugFormData = function() {
    console.log('🔍 === تشخيص البيانات ===');
    console.log('selectedSizes:', this.selectedSizes);
    console.log('availableSizes:', this.availableSizes);
    console.log('availableColors:', this.availableColors);
    console.log('inventoryRowCounter:', this.inventoryRowCounter);
    
    const form = document.querySelector('form');
    if (form) {
        const formData = new FormData(form);
        console.log('📝 Form Data:');
        
        // Group data by type
        const inventory = {};
        const other = {};
        
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('inventories[')) {
                if (!inventory[key]) inventory[key] = [];
                inventory[key].push(value);
            } else {
                if (!other[key]) other[key] = [];
                other[key].push(value);
            }
        }
        
        console.log('📦 Inventory Data:', inventory);
        console.log('📋 Other Data:', other);
        
        // Count inventory rows
        const inventoryRows = document.querySelectorAll('.inventory-row');
        console.log('📊 Total inventory rows:', inventoryRows.length);
        
        // Validate inventory data
        let hasErrors = false;
        inventoryRows.forEach((row, index) => {
            const sizeSelect = row.querySelector('.size-select');
            const colorSelect = row.querySelector('.color-select');
            const stockInput = row.querySelector('.stock-input');
            const priceInput = row.querySelector('.price-input');
            
            if (!sizeSelect?.value) {
                console.warn(`⚠️ Row ${index + 1}: Missing size`);
                hasErrors = true;
            }
            if (!colorSelect?.value) {
                console.warn(`⚠️ Row ${index + 1}: Missing color`);
                hasErrors = true;
            }
            if (!stockInput?.value || stockInput.value < 0) {
                console.warn(`⚠️ Row ${index + 1}: Invalid stock`);
                hasErrors = true;
            }
            if (!priceInput?.value || priceInput.value < 0) {
                console.warn(`⚠️ Row ${index + 1}: Invalid price`);
                hasErrors = true;
            }
        });
        
        if (hasErrors) {
            console.error('❌ Form has validation errors');
            alert('تم العثور على أخطاء في النموذج. تحقق من وحدة التحكم للتفاصيل.');
        } else {
            console.log('✅ Form validation passed');
            alert('✅ النموذج صحيح ولا يحتوي على أخطاء');
        }
    } else {
        console.error('❌ Form not found');
    }
};

/**
 * Add new size (for old system compatibility)
 */
window.AdminProducts.addNewSize = function() {
    try {
        const sizeContainer = document.getElementById('sizeColorMatrix');
        if (!sizeContainer) {
            console.error('Size container not found');
            return false;
        }
        
        // Implementation for adding new size in old system
        console.log('✅ Adding new size (old system)');
        
        // Add your existing addNewSize logic here
        return true;
    } catch (error) {
        console.error('Error in addNewSize:', error);
        alert('حدث خطأ أثناء إضافة مقاس جديد: ' + error.message);
        return false;
    }
};

/**
 * Initialize default inventory row for create page
 */
window.AdminProducts.initializeDefaultRow = function() {
    if (this.inventoryRowCounter === 0) {
        this.addInventoryRow();
        console.log('✅ Added default inventory row');
    }
};

/**
 * Validate form before submission
 */
window.AdminProducts.validateForm = function() {
    const inventoryRows = document.querySelectorAll('.inventory-row');
    
    if (inventoryRows.length === 0) {
        alert('يجب إضافة مجموعة مخزون واحدة على الأقل');
        return false;
    }
    
    let isValid = true;
    inventoryRows.forEach((row, index) => {
        const sizeSelect = row.querySelector('.size-select');
        const colorSelect = row.querySelector('.color-select');
        const stockInput = row.querySelector('.stock-input');
        const priceInput = row.querySelector('.price-input');
        
        if (!sizeSelect?.value || !colorSelect?.value || 
            !stockInput?.value || !priceInput?.value) {
            alert(`مجموعة المخزون رقم ${index + 1} غير مكتملة`);
            isValid = false;
            return false;
        }
    });
    
    return isValid;
};

// Expose functions to global scope for backward compatibility
window.addInventoryRow = function() {
    return window.AdminProducts.addInventoryRow();
};

window.debugFormData = function() {
    return window.AdminProducts.debugFormData();
};

window.addNewSize = function() {
    return window.AdminProducts.addNewSize();
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 AdminProducts script loaded');
});
