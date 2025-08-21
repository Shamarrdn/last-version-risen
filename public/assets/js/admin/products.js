

window.selectedSizes = [];
window.availableSizes = [];
window.availableColors = [];
window.imageCount = 1;
window.inventoryRows = [];
window.inventoryRowCounter = 0;


function generateSlug(name) {
    let slug = name.toLowerCase().trim().replace(/\s+/g, '-');
    slug = slug.replace(/[^\u0621-\u064A\u0660-\u0669a-z0-9-]/g, '');
    slug = slug.replace(/-+/g, '-');
    return slug;
}

window.addImageInput = function() {
    const container = document.getElementById('imagesContainer');
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `
        <div class="input-group shadow-sm">
            <input type="file" name="images[]" class="form-control" accept="image/*">
            <div class="input-group-text">
                <label class="mb-0">
                    <input type="radio" name="is_primary[${window.imageCount}]" value="1" class="me-1">
                    صورة رئيسية
                </label>
            </div>
            <button type="button" class="btn btn-light-danger" onclick="this.closest('.mb-2').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    window.imageCount++;
}

window.addNewImageInput = function() {
    const container = document.getElementById('newImagesContainer');
    if (!container) {
        console.error('Container newImagesContainer not found');
        return;
    }

    const div = document.createElement('div');
    div.className = 'mb-2';

    const existingImages = container.querySelectorAll('.mb-2').length;
    const imageIndex = existingImages;

    div.innerHTML = `
        <div class="input-group shadow-sm">
            <input type="file" name="new_images[]" class="form-control" accept="image/*">
            <div class="input-group-text">
                <label class="mb-0">
                    <input type="radio" name="is_primary_new[${imageIndex}]" value="1" class="me-1">
                    صورة رئيسية
                </label>
            </div>
            <button type="button" class="btn btn-light-danger" onclick="this.closest('.mb-2').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    console.log('تم إضافة حقل صورة جديد بنجاح');
}

window.addDetailInput = function() {
    const container = document.getElementById('detailsContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 shadow-sm';
    div.innerHTML = `
        <input type="text" name="detail_keys[]" class="form-control" placeholder="الخاصية">
        <input type="text" name="detail_values[]" class="form-control" placeholder="القيمة">
        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

window.addInventoryRow = function() {
    const matrixContainer = document.getElementById('inventoryMatrix');
    const rowId = 'inventory-row-' + window.inventoryRowCounter++;

    const rowHtml = `
        <div class="inventory-row card mb-3" id="${rowId}">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">المقاس</label>
                        <select class="form-select size-select" name="inventories[${rowId}][size_id]" required>
                            <option value="">اختر المقاس...</option>
                            ${window.availableSizes.map(size => `
                                <option value="${size.id}">${size.name} - ${size.description || ''}</option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">اللون</label>
                        <select class="form-select color-select" name="inventories[${rowId}][color_id]" required>
                            <option value="">اختر اللون...</option>
                            ${window.availableColors.map(color => `
                                <option value="${color.id}">${color.name} - ${color.description || ''}</option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">المخزون</label>
                        <input type="number"
                               class="form-control"
                               name="inventories[${rowId}][stock]"
                               placeholder="50"
                               min="0"
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">السعر (ر.س)</label>
                        <input type="number"
                               class="form-control"
                               name="inventories[${rowId}][price]"
                               placeholder="150"
                               step="0.01"
                               min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger d-block w-100" onclick="window.removeInventoryRow('${rowId}')">
                            <i class="fas fa-trash"></i>
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    matrixContainer.insertAdjacentHTML('beforeend', rowHtml);
    window.inventoryRows.push(rowId);
    console.log('Added inventory row:', rowId);
};

window.removeInventoryRow = function(rowId) {
    if (confirm('هل أنت متأكد من حذف هذا الصف؟')) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            window.inventoryRows = window.inventoryRows.filter(id => id !== rowId);
            console.log('Removed inventory row:', rowId);
        }
    }
};

window.removeExistingInventoryRow = function(rowId, existingId) {
    if (confirm('هل أنت متأكد من حذف هذا الصف؟ سيتم حذفه نهائياً من قاعدة البيانات.')) {
        const row = document.getElementById(rowId);
        if (row && existingId) {
            // Show loading state
            const button = row.querySelector('button');
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحذف...';
            button.disabled = true;

            // Get product ID from URL
            const pathParts = window.location.pathname.split('/');
            const productSlug = pathParts[pathParts.length - 2]; // Get product slug from URL

            // Make AJAX request to delete
            fetch(`/admin/products/${productSlug}/inventory/${existingId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from DOM
                    row.remove();
                    
                    // Remove from tracking array
                    if (window.inventoryRows) {
                        window.inventoryRows = window.inventoryRows.filter(id => id !== rowId);
                    }
                    
                    console.log('Successfully deleted inventory row:', existingId);
                    
                    // Show success message
                    showAlert('تم حذف العنصر بنجاح', 'success');
                } else {
                    throw new Error(data.message || 'حدث خطأ أثناء الحذف');
                }
            })
            .catch(error => {
                console.error('Error deleting inventory:', error);
                
                // Restore button state
                button.innerHTML = originalHtml;
                button.disabled = false;
                
                // Show error message
                showAlert('حدث خطأ أثناء الحذف: ' + error.message, 'error');
            });
        } else if (!existingId) {
            // For new rows (no existing ID), just remove from DOM
            if (row) {
                row.remove();
                if (window.inventoryRows) {
                    window.inventoryRows = window.inventoryRows.filter(id => id !== rowId);
                }
                console.log('Removed new inventory row:', rowId);
            }
        }
    }
};

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed top-0 start-50 translate-middle-x mt-4`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} me-2"></i>
            <div>${message}</div>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}

window.updateInventoryMatrix = function() {
    const matrixContainer = document.getElementById('inventoryMatrix');
    if (matrixContainer) {
        matrixContainer.innerHTML = '';
        window.inventoryRows = [];
        window.inventoryRowCounter = 0;

        // Removed automatic inventory row addition - user will add manually if needed
        console.log('Inventory matrix cleared - ready for manual additions');
    }
};

window.updateSizeColorMatrix = function() {
    try {
        const matrixContainer = document.getElementById('sizeColorMatrix');
        if (!matrixContainer) {
            console.error('Size color matrix container not found');
            return;
        }

        const stockInputs = matrixContainer.querySelectorAll('input[name*="stock"]');
        const priceInputs = matrixContainer.querySelectorAll('input[name*="price"]');

        console.log('Saving current values before refresh...');

        stockInputs.forEach(input => {
            const matches = input.name.match(/stock\[([^\]]+)\]\[([^\]]+)\]/);
            if (matches) {
                const sizeId = matches[1];
                const colorId = matches[2];
                const value = input.value;

                let size = window.selectedSizes.find(s => String(s.id) === String(sizeId));
                if (size && size.colors) {
                    let color = size.colors.find(c => String(c.id) === String(colorId));
                    if (color) {
                        color.stock = value;
                        console.log(`✅ Saved stock: ${sizeId}-${colorId} = ${value}`);
                    }
                }
            }
        });


        priceInputs.forEach(input => {
            const matches = input.name.match(/price\[([^\]]+)\]\[([^\]]+)\]/);
            if (matches) {
                const sizeId = matches[1];
                const colorId = matches[2];
                const value = input.value;

                let size = window.selectedSizes.find(s => String(s.id) === String(sizeId));
                if (size && size.colors) {
                    let color = size.colors.find(c => String(c.id) === String(colorId));
                    if (color) {
                        color.price = value;
                        console.log(`✅ Saved price: ${sizeId}-${colorId} = ${value}`);
                    }
                }
            }
        });

        matrixContainer.innerHTML = '';
        console.log('Updating size color matrix with', window.selectedSizes ? window.selectedSizes.length : 0, 'sizes');

        if (!window.selectedSizes || !Array.isArray(window.selectedSizes)) {
            console.warn('selectedSizes is not an array, initializing it');
            window.selectedSizes = [];
            return;
        }

        window.selectedSizes.forEach((size, sizeIndex) => {
            const sizeContainer = document.createElement('div');
            sizeContainer.className = 'size-container active';
            sizeContainer.dataset.sizeId = size.id;

            const selectedColors = size.colors || [];

            sizeContainer.innerHTML = `
                <div class="size-header">
                    <div class="size-title">
                        <i class="fas fa-ruler"></i>
                        المقاس ${sizeIndex + 1}
                        <span class="size-number">${sizeIndex + 1}</span>
                    </div>
                    <button type="button" class="size-remove-btn" onclick="window.removeSizeFromCard(${sizeIndex})">
                        <i class="fas fa-times"></i>
                        حذف المقاس
                    </button>
                </div>

                <select class="size-select" onchange="updateSizeName(${sizeIndex}, this.value)">
                    <option value="">اختر المقاس...</option>
                    ${window.availableSizes.map(s => `
                        <option value="${s.id}" ${s.id == size.id ? 'selected' : ''}>
                            ${s.name} - ${s.description || ''}
                        </option>
                    `).join('')}
                </select>

                <div class="colors-section" id="colors-section-${size.id}">
                    <h6 class="mb-3" style="color: #007bff; font-weight: 600;">
                        <i class="fas fa-palette me-2"></i>
                        الألوان المتاحة
                    </h6>
                    <div class="size-colors-container" id="size-colors-${size.id}">
                        ${selectedColors.map(color => `
                            <div class="color-item" data-color-id="${color.id}">
                                <select class="color-select" onchange="updateColorName(this, '${size.id}')">
                                    <option value="">اختر اللون...</option>
                                    ${window.availableColors.map(c => `
                                        <option value="${c.id}" data-hex="${c.code || '#007bff'}" ${c.id == color.id ? 'selected' : ''}>
                                            ${c.name} - ${c.description || ''}
                                        </option>
                                    `).join('')}
                                </select>

                                <div class="color-inputs">
                                    <div class="input-group-sm">
                                        <label>عدد القطع:</label>
                                        <input type="number"
                                            name="stock[${size.id}][${color.id}]"
                                            placeholder="50"
                                            min="0"
                                            value="${color.stock || ''}"
                                            required>
                                    </div>
                                    <div class="input-group-sm">
                                        <label>السعر (اختياري):</label>
                                        <input type="number"
                                            name="price[${size.id}][${color.id}]"
                                            placeholder="150"
                                            step="0.01"
                                            min="0"
                                            value="${color.price || ''}">
                                    </div>
                                </div>

                                <button type="button" class="color-remove-btn" onclick="window.removeColorFromSize('${size.id}', '${color.id}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" class="add-color-btn" onclick="window.addColorToSize('${size.id}')">
                        <i class="fas fa-plus me-1"></i>
                        إضافة لون آخر
                    </button>
                </div>
            `;

            matrixContainer.appendChild(sizeContainer);
        });

        console.log('Size color matrix updated successfully');
    } catch (error) {
        console.error('Error in updateSizeColorMatrix:', error);
        alert('حدث خطأ أثناء تحديث مصفوفة المقاسات والألوان: ' + error.message);
    }
}

window.removeSizeFromCard = function(sizeIndex) {
    if (confirm('هل أنت متأكد من حذف هذا المقاس؟')) {
        window.selectedSizes.splice(sizeIndex, 1);
        window.updateSizeColorMatrix();
    }
}

window.addColorToSize = function(sizeId) {
    console.log('Adding color to size:', sizeId);
    console.log('Available sizes:', window.selectedSizes);

    let size = null;
    size = window.selectedSizes.find(s => String(s.id) === String(sizeId));

    if (!size && window.selectedSizes.length > 0) {
        size = window.selectedSizes[0];
        console.log('Using first available size:', size);
    }

    if (!size) {
        console.error('Size not found:', sizeId);
        alert('خطأ: لم يتم العثور على المقاس المحدد. يرجى إعادة المحاولة.');
        return;
    }

    if (!size.colors) {
        size.colors = [];
    }

    let newColor;

    if (window.availableColors.length > 0) {
        const firstColor = window.availableColors[0];
        newColor = {
            id: firstColor.id,
            name: firstColor.name,
            stock: '',
            price: ''
        };
    } else {
        newColor = {
            id: 'temp_' + Date.now(),
            name: '',
            stock: '',
            price: ''
        };
    }

    size.colors.push(newColor);

    addColorToUI(size, newColor);

    console.log('Color added successfully to size:', size.id, 'Total colors:', size.colors.length);
}

function addColorToUI(size, color) {
    console.log('Adding color to UI for size:', size.id, 'color:', color);

    let colorsContainer = document.querySelector(`#size-colors-${size.id}`);

    if (!colorsContainer) {
        console.log('Falling back to full matrix update');
        window.updateSizeColorMatrix();
        return;
    }

    console.log('Found colors container:', colorsContainer);

    const colorItem = document.createElement('div');
    colorItem.className = 'color-item';
    colorItem.dataset.colorId = color.id;

    colorItem.innerHTML = `
        <select class="color-select" onchange="updateColorName(this, '${size.id}')">
            <option value="">اختر اللون...</option>
            ${window.availableColors.map(c => `
                <option value="${c.id}" data-hex="${c.code || '#4A5568'}" ${c.id == color.id ? 'selected' : ''}>
                    ${c.name} ${c.description ? '- ' + c.description : ''}
                </option>
            `).join('')}
        </select>

        <div class="color-inputs">
            <div class="input-group-sm">
                <label>عدد القطع:</label>
                <input type="number"
                    name="stock[${size.id}][${color.id}]"
                    placeholder="50"
                    min="0"
                    value="${color.stock || ''}"
                    required>
            </div>
            <div class="input-group-sm">
                <label>السعر (ر.س):</label>
                <input type="number"
                    name="price[${size.id}][${color.id}]"
                    placeholder="150"
                    step="0.01"
                    min="0"
                    value="${color.price || ''}">
            </div>
        </div>

        <button type="button" class="color-remove-btn" onclick="window.removeColorFromSize('${size.id}', '${color.id}')">
            <i class="fas fa-times"></i>
        </button>
    `;

    colorsContainer.appendChild(colorItem);
    console.log('Color item added successfully to container');
}

function updateColorName(selectElement, sizeId) {
    const colorItem = selectElement.closest('.color-item');
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    if (selectedOption.value) {
        const colorId = selectedOption.value;
        const colorName = selectedOption.textContent;

        let size = window.selectedSizes.find(s => String(s.id) === String(sizeId));

        if (!size || !size.colors) {
            console.error('Size not found:', sizeId);
            alert('خطأ: لم يتم العثور على المقاس. يرجى إعادة تحميل الصفحة.');
            return;
        }


        const colorIndex = size.colors.findIndex(c => c.id === colorItem.dataset.colorId);
        if (colorIndex === -1) {
            return;
        }

        const existingColor = size.colors.find((c, index) =>
            index !== colorIndex && c.id == colorId
        );

        if (existingColor) {
            alert('هذا اللون موجود بالفعل في هذا المقاس');
            selectElement.value = size.colors[colorIndex].id || '';
            return;
        }

        // تحديث بيانات اللون
        size.colors[colorIndex].id = colorId;
        size.colors[colorIndex].name = colorName;

        colorItem.dataset.colorId = colorId;

        const colorStockInput = colorItem.querySelector('input[name*="stock"]');
        const priceInput = colorItem.querySelector('input[name*="price"]');

        if (colorStockInput) {
            colorStockInput.name = `stock[${sizeId}][${colorId}]`;
        }
        if (priceInput) {
            priceInput.name = `price[${sizeId}][${colorId}]`;
        }
    }
}

window.removeColorFromSize = function(sizeId, colorId) {
    if (confirm('هل أنت متأكد من حذف هذا اللون من المقاس؟')) {
        let size = window.selectedSizes.find(s => String(s.id) === String(sizeId));

        if (!size || !size.colors) {
            console.error('Size not found:', sizeId);
            alert('خطأ: لم يتم العثور على المقاس. يرجى إعادة تحميل الصفحة.');
            return;
        }

        const colorIndex = size.colors.findIndex(c => c.id === colorId);
        if (colorIndex !== -1) {
            size.colors.splice(colorIndex, 1);
            window.updateSizeColorMatrix();
        }
    }
}

window.addNewSize = function() {
    try {
        console.log('Adding new size...');
        let newSize;

        if (window.availableSizes && window.availableSizes.length > 0) {
            const firstSize = window.availableSizes[0];
            newSize = {
                id: firstSize.id,
                name: firstSize.name,
                colors: [] // مصفوفة فارغة للألوان
            };
            console.log('Using available size:', firstSize);
        } else {
            newSize = {
                id: 'temp_' + Date.now(),
                name: 'مقاس جديد',
                colors: [] // مصفوفة فارغة للألوان
            };
            console.log('Created temporary size');
        }

        if (!window.selectedSizes) {
            window.selectedSizes = [];
            console.log('Initialized selectedSizes array');
        }

        window.selectedSizes.push(newSize);
        console.log('New size added:', newSize);
        console.log('Total sizes:', window.selectedSizes.length);

        window.updateSizeColorMatrix();

        return true;
    } catch (error) {
        console.error('Error in addNewSize:', error);
        alert('حدث خطأ أثناء إضافة مقاس جديد: ' + error.message);
        return false;
    }
}

function updateSizeName(sizeIndex, sizeId) {
    if (sizeId) {
        const existingSize = window.selectedSizes.find((size, index) =>
            index !== sizeIndex && size.id == sizeId
        );

        if (existingSize) {
            alert('هذا المقاس موجود بالفعل في منتج آخر');
            const selectElement = event.target;
            selectElement.value = window.selectedSizes[sizeIndex].id || '';
            return;
        }

        const sizeOption = document.querySelector(`option[value="${sizeId}"]`);
        if (sizeOption) {
            window.selectedSizes[sizeIndex].id = sizeId;
            window.selectedSizes[sizeIndex].name = sizeOption.textContent;
        }
    }
}

function validateForm() {
    const form = document.querySelector('form[action*="products"]');

    if (!form) {
        console.error('Form not found!');
        alert('خطأ: لم يتم العثور على النموذج');
        return false;
    }

    const nameInput = form.querySelector('input[name="name"]');
    const categoryInput = form.querySelector('select[name="category_id"]');
    const descriptionInput = form.querySelector('textarea[name="description"]');
    const stockInput = form.querySelector('input[name="stock"]');

    if (!nameInput || !nameInput.value || !nameInput.value.trim()) {
        alert('يرجى إدخال اسم المنتج');
        nameInput?.focus();
        return false;
    }

    if (!categoryInput || !categoryInput.value) {
        alert('يرجى اختيار التصنيف الرئيسي');
        categoryInput?.focus();
        return false;
    }

    if (!descriptionInput || !descriptionInput.value.trim()) {
        alert('يرجى إدخال وصف المنتج');
        descriptionInput?.focus();
        return false;
    }

    // إزالة التحقق من حقل المخزون العام - الاعتماد على المخزون التفصيلي
    if (stockInput && stockInput.offsetParent !== null) {
        const stockValue = parseInt(stockInput.value) || 0;
        if (stockValue < 0) {
            alert('يرجى إدخال قيمة صحيحة للمخزون (0 أو أكثر)');
            stockInput.focus();
            return false;
        }
        stockInput.value = Math.max(0, stockValue);
        console.log('Stock value updated to:', stockInput.value);
    }

    const isCreatePage = window.location.href.includes('/create');
    if (isCreatePage) {
        const imageInputs = form.querySelectorAll('input[name="images[]"]');
        const hasImages = Array.from(imageInputs).some(input => input.files && input.files.length > 0);

        if (!hasImages) {
            const confirmResult = confirm('لم تقم بإضافة أي صور للمنتج. هل تريد المتابعة بدون صور؟');
            if (!confirmResult) {
                return false;
            }
        }
    }

    const inventoryRows = form.querySelectorAll('.inventory-row');
    if (inventoryRows.length > 0) {
        let hasValidInventory = false;

        for (const row of inventoryRows) {
            const sizeSelect = row.querySelector('select[name*="size_id"]');
            const colorSelect = row.querySelector('select[name*="color_id"]');
            const stockInput = row.querySelector('input[name*="stock"]');

            if (sizeSelect && sizeSelect.value && colorSelect && colorSelect.value && stockInput && stockInput.value) {
                hasValidInventory = true;
                break;
            }
        }

        if (!hasValidInventory) {
            const confirmProceed = confirm('لم تقم بإضافة مخزون تفصيلي. هل تريد المتابعة بالمخزون العام فقط؟');
            if (!confirmProceed) {
                return false;
            }
        }
    }

    console.log('✅ Form validation passed');
    return true;
}

window.prepareFormData = function() {
    console.log('🔍 [DEBUG] Preparing form data...');

    const form = document.getElementById('product-form');
    if (!form) {
        console.error('Form not found!');
        return false;
    }

    const stockInput = form.querySelector('input[name="stock"]');
    if (stockInput) {
        const stockValue = parseInt(stockInput.value) || 0;
        stockInput.value = Math.max(0, stockValue);
        console.log('Stock value set to:', stockInput.value);
    }

    const oldInputs = form.querySelectorAll('.dynamic-field');
    oldInputs.forEach(input => {
        console.log('Removing old input:', input.name, input.value);
        input.remove();
    });

    const inventoryRows = document.querySelectorAll('.inventory-row');
    console.log('Found inventory rows:', inventoryRows.length);

    if (inventoryRows.length > 0) {
        console.log('🔍 Using new inventory system');

        inventoryRows.forEach((row, index) => {
            const sizeSelect = row.querySelector('select[name*="size_id"]');
            const colorSelect = row.querySelector('select[name*="color_id"]');
            const stockInput = row.querySelector('input[name*="stock"]');
            const priceInput = row.querySelector('input[name*="price"]');

            if (sizeSelect && sizeSelect.value && colorSelect && colorSelect.value) {
                const sizeId = sizeSelect.value;
                const colorId = colorSelect.value;
                const stockValue = stockInput ? stockInput.value : '0';
                const priceValue = priceInput ? priceInput.value : '0';

                console.log(`Processing inventory row ${index + 1}:`, {
                    sizeId, colorId, stockValue, priceValue
                });

                if (sizeSelect) sizeSelect.name = `inventories[${sizeId}][${colorId}][size_id]`;
                if (colorSelect) colorSelect.name = `inventories[${sizeId}][${colorId}][color_id]`;
                if (stockInput) stockInput.name = `inventories[${sizeId}][${colorId}][stock]`;
                if (priceInput) priceInput.name = `inventories[${sizeId}][${colorId}][price]`;

                console.log(`✅ Updated field names for ${sizeId}-${colorId}`);
            } else {
                console.warn(`Row ${index + 1} has missing required fields`);
            }
        });

        console.log('✅ New inventory system data prepared');
        return true;
    } else {
        console.log('🔍 Using fallback old system');

        const sizeContainers = document.querySelectorAll('.size-container');
        const collectedSizes = new Set();
        const collectedColors = new Set();
        const collectedStockData = {};
        const collectedPriceData = {};

        console.log('Found size containers:', sizeContainers.length);

        sizeContainers.forEach((container, index) => {
            const sizeSelect = container.querySelector('.size-select');
            if (sizeSelect && sizeSelect.value) {
                const sizeId = sizeSelect.value;
                collectedSizes.add(sizeId);
                console.log(`Processing size ${index + 1}:`, sizeId);

                let colorItems = container.querySelectorAll('.color-item');

                if (colorItems.length === 0) {
                    const colorsContainer = container.querySelector('.size-colors-container');
                    if (colorsContainer) {
                        colorItems = colorsContainer.querySelectorAll('.color-item');
                    }
                }

                console.log(`Processing ${colorItems.length} color items for size ${sizeId}`);

                colorItems.forEach((colorItem, colorIndex) => {
                    const colorSelect = colorItem.querySelector('.color-select');
                    if (colorSelect && colorSelect.value) {
                        const colorId = colorSelect.value;
                        collectedColors.add(colorId);
                        console.log(`Found color ${colorIndex + 1}: ${colorId}`);

                        const stockInput = colorItem.querySelector('input[name*="stock"]');
                        const priceInput = colorItem.querySelector('input[name*="price"]');

                        if (stockInput && stockInput.value) {
                            if (!collectedStockData[sizeId]) collectedStockData[sizeId] = {};
                            collectedStockData[sizeId][colorId] = stockInput.value;
                            console.log(`Collected stock: ${sizeId}-${colorId} = ${stockInput.value}`);
                        }

                        if (priceInput && priceInput.value) {
                            if (!collectedPriceData[sizeId]) collectedPriceData[sizeId] = {};
                            collectedPriceData[sizeId][colorId] = priceInput.value;
                            console.log(`Collected price: ${sizeId}-${colorId} = ${priceInput.value}`);
                        }
                    }
                });
            }
        });

        Array.from(collectedSizes).forEach(sizeId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_sizes[]';
            input.value = sizeId;
            input.classList.add('dynamic-field');
            form.appendChild(input);
            console.log('Added size input:', sizeId);
        });

        Array.from(collectedColors).forEach(colorId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_colors[]';
            input.value = colorId;
            input.classList.add('dynamic-field');
            form.appendChild(input);
            console.log('Added color input:', colorId);
        });

        Object.keys(collectedStockData).forEach(sizeId => {
            Object.keys(collectedStockData[sizeId]).forEach(colorId => {
                const stockValue = collectedStockData[sizeId][colorId];
                const priceValue = collectedPriceData[sizeId]?.[colorId] || '';

                const stockInput = document.createElement('input');
                stockInput.type = 'hidden';
                stockInput.name = `stock[${sizeId}][${colorId}]`;
                stockInput.value = stockValue;
                stockInput.classList.add('dynamic-field');
                form.appendChild(stockInput);
                console.log(`Added stock input: stock[${sizeId}][${colorId}] = ${stockValue}`);

                if (priceValue) {
                    const priceInput = document.createElement('input');
                    priceInput.type = 'hidden';
                    priceInput.name = `price[${sizeId}][${colorId}]`;
                    priceInput.value = priceValue;
                    priceInput.classList.add('dynamic-field');
                    form.appendChild(priceInput);
                    console.log(`Added price input: price[${sizeId}][${colorId}] = ${priceValue}`);
                }
            });
        });

        console.log('✅ Fallback system data prepared');
    }


    const finalInventories = form.querySelectorAll('input[name*="inventories["]');
    const finalSizes = form.querySelectorAll('input[name="selected_sizes[]"]');
    const finalColors = form.querySelectorAll('input[name="selected_colors[]"]');
    const finalStock = form.querySelectorAll('input[name*="stock["]');
    const finalPrice = form.querySelectorAll('input[name*="price["]');

    console.log('🔍 [DEBUG] Final form data summary:');
    console.log('- Inventories fields:', finalInventories.length);
    console.log('- Sizes:', finalSizes.length);
    console.log('- Colors:', finalColors.length);
    console.log('- Stock fields:', finalStock.length);
    console.log('- Price fields:', finalPrice.length);

    console.log('✅ Form data prepared successfully');
    return true;
}

window.debugFormData = function() {
    console.log('🔍 === تشخيص البيانات ===');
    console.log('selectedSizes:', window.selectedSizes);
    console.log('availableSizes:', window.availableSizes);
    console.log('availableColors:', window.availableColors);
};

function prepareDataForSubmission(availableSizes, availableColors) {
    window.availableSizes = availableSizes || [];
    window.availableColors = availableColors || [];

    console.log('Available sizes:', window.availableSizes);
    console.log('Available colors:', window.availableColors);
}

function loadExistingInventoryData(inventoryData) {
    console.log('🔍 [DEBUG] Loading existing inventory data:', inventoryData);

    if (!inventoryData || inventoryData.length === 0) {
        console.log('No existing inventory data to load');
        return;
    }

    const sizeGroups = {};
    inventoryData.forEach(item => {
        if (!sizeGroups[item.size_id]) {
            sizeGroups[item.size_id] = {
                id: item.size_id,
                name: item.size_name || 'مقاس غير محدد',
                colors: []
            };
        }

        if (item.color_id) {
            sizeGroups[item.size_id].colors.push({
                id: item.color_id,
                name: item.color_name || 'لون غير محدد',
                stock: item.stock || 0,
                price: item.price || ''
            });
        }
    });

    window.selectedSizes = Object.values(sizeGroups);
    console.log('Processed existing data:', window.selectedSizes);

    window.updateSizeColorMatrix();
}

function setupEditForm() {
    const form = document.querySelector('form[action*="products"]');
    if (!form) {
        console.error('Form not found in setupEditForm');
        return;
    }

    console.log('Setting up edit form...');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        console.log('Edit form submitted, processing...');

        const inventoryRows = document.querySelectorAll('.inventory-row');
        console.log(`Found ${inventoryRows.length} inventory rows`);

        inventoryRows.forEach((row, index) => {
            const sizeSelect = row.querySelector('select[name*="size_id"]');
            const colorSelect = row.querySelector('select[name*="color_id"]');
            const stockInput = row.querySelector('input[name*="stock"]');
            const priceInput = row.querySelector('input[name*="price"]');

            if (sizeSelect && colorSelect && stockInput) {
                const sizeId = sizeSelect.value;
                const colorId = colorSelect.value;
                const stockValue = stockInput.value || '0';
                const priceValue = priceInput ? priceInput.value || '' : '';

                if (sizeId && colorId) {
                    sizeSelect.name = `inventories[${sizeId}][${colorId}][size_id]`;
                    colorSelect.name = `inventories[${sizeId}][${colorId}][color_id]`;
                    stockInput.name = `inventories[${sizeId}][${colorId}][stock]`;
                    if (priceInput) {
                        priceInput.name = `inventories[${sizeId}][${colorId}][price]`;
                    }

                    console.log(`✅ Updated inventory row ${index + 1}: size=${sizeId}, color=${colorId}, stock=${stockValue}, price=${priceValue}`);
                } else {
                    console.warn(`⚠️ Inventory row ${index + 1} has missing data: size=${sizeId}, color=${colorId}`);
                }
            }
        });

        const colorItems = document.querySelectorAll('.color-item');
        if (colorItems.length > 0) {
            console.log(`Found ${colorItems.length} color items (old system)`);
            const stockPriceData = {};

            colorItems.forEach(colorItem => {
                const stockInput = colorItem.querySelector('input[placeholder="50"]');
                const priceInput = colorItem.querySelector('input[placeholder="150"]');
                const colorSelect = colorItem.querySelector('.color-select');
                const sizeContainer = colorItem.closest('.size-container');
                const sizeSelect = sizeContainer ? sizeContainer.querySelector('.size-select') : null;

                if (stockInput && priceInput && colorSelect && colorSelect.value && sizeSelect && sizeSelect.value) {
                    const sizeId = sizeSelect.value;
                    const colorId = colorSelect.value;
                    const stockValue = stockInput.value || '0';
                    const priceValue = priceInput.value || '';

                    if (!stockPriceData[sizeId]) stockPriceData[sizeId] = {};
                    stockPriceData[sizeId][colorId] = {
                        stock: stockValue,
                        price: priceValue
                    };

                    console.log(`Saved old system values: size=${sizeId}, color=${colorId}, stock=${stockValue}, price=${priceValue}`);
                }
            });
        }


        const loadingAlert = document.createElement('div');
        loadingAlert.className = 'alert alert-info position-fixed top-0 start-50 translate-middle-x mt-4';
        loadingAlert.style.zIndex = '9999';
        loadingAlert.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <div>جاري حفظ التغييرات...</div>
            </div>
        `;
        document.body.appendChild(loadingAlert);

        // التحقق من صحة النموذج
        if (!validateForm()) {
            loadingAlert.remove();
            return false;
        }

        console.log('✅ Form validation passed, submitting...');

        const formData = new FormData(form);

        formData.append('_method', 'PUT');

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(Object.values(data.errors).flat().join('\n'));
                    });
                }
                throw new Error('حدث خطأ أثناء حفظ البيانات (رمز الخطأ: ' + response.status + ')');
            }
            return response.text();
        })
        .then(data => {
            loadingAlert.remove();

            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-4';
            successAlert.style.zIndex = '9999';
            successAlert.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>تم حفظ التغييرات بنجاح</div>
                </div>
            `;
            document.body.appendChild(successAlert);

            setTimeout(() => {
                successAlert.remove();
                window.location.href = window.location.href.replace('/edit', '');
            }, 2000);
        })
        .catch(error => {
            loadingAlert.remove();

            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-4';
            errorAlert.style.zIndex = '9999';
            errorAlert.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>حدث خطأ أثناء حفظ البيانات: ${error.message}</div>
                </div>
            `;
            document.body.appendChild(errorAlert);

            setTimeout(() => {
                errorAlert.remove();
            }, 5000);

            console.error('Error:', error);
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up form...');

    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            slugInput.value = generateSlug(this.value);
        });
    }

    const mainStockInput = document.querySelector('input[name="stock"]');
    if (mainStockInput) {
        const initialValue = parseInt(mainStockInput.value) || 0;
        mainStockInput.value = Math.max(0, initialValue);
        console.log('Stock input initialized with value:', mainStockInput.value);

        mainStockInput.addEventListener('blur', function() {
            const value = parseInt(this.value) || 0;
            this.value = Math.max(0, value);
            console.log('Stock input blur event - value set to:', this.value);
        });

        mainStockInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            console.log('Stock input input event - value:', this.value);
        });
    }

    if (typeof window.inventoryRows === 'undefined') {
        window.inventoryRows = [];
    }

    if (typeof window.inventoryRowCounter === 'undefined') {
        window.inventoryRowCounter = 0;
    }

    // تحديد نوع الصفحة أولاً
    const isEditPage = window.location.href.includes('/edit');
    const isCreatePage = window.location.href.includes('/create');

    console.log('Page type detected:', isEditPage ? 'Edit' : isCreatePage ? 'Create' : 'Unknown');

    if (isCreatePage) {
        console.log('Initializing new inventory system for create page...');
        try {
            window.updateInventoryMatrix();
            console.log('New inventory system initialized successfully');
        } catch (error) {
            console.error('Error initializing new inventory system:', error);
        }
    }

    console.log('Checking if we need to add a default size...');
    // Removed automatic size addition on page load - user will add manually if needed
    console.log('Auto-add disabled - user will add sizes manually');

    const addSizeButton = document.getElementById('addSizeButton');
    if (addSizeButton) {
        addSizeButton.addEventListener('click', function() {
            window.addNewSize();
        });
    }

    if (isEditPage) {

        setupEditForm();
    } else if (isCreatePage) {

                const form = document.querySelector('form');
        if (form) {
            console.log('Form found, adding submit listener...');
            form.addEventListener('submit', function(e) {
                console.log('Form submitted, validating...');

                // التحقق من صحة النموذج
                if (!validateForm()) {
                    e.preventDefault();
                    console.log('Form validation failed');
                    return false;
                }

                console.log('✅ Form validation passed');

                // إعداد البيانات قبل الإرسال
                try {
                    console.log('Preparing form data...');
                    const success = window.prepareFormData();
                    if (!success) {
                        e.preventDefault();
                        alert('حدث خطأ في إعداد البيانات. يرجى المحاولة مرة أخرى.');
                        return false;
                    }
                    console.log('✅ Form data prepared successfully, submitting...');
                    // السماح للنموذج بالإرسال الطبيعي
                } catch (error) {
                    e.preventDefault();
                    console.error('Error preparing form data:', error);
                    alert('حدث خطأ أثناء معالجة البيانات: ' + error.message);
                    return false;
                }
            });
        } else {
            console.error('Form not found!');
        }
    }

    console.log('✅ JavaScript loaded successfully');
});
