let selectedColor = null;
let selectedColorId = null;
let selectedSize = null;
let selectedSizeId = null;

function updateMainImage(src, thumbnail) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail-wrapper').forEach(thumb => {
        thumb.classList.remove('active');
    });
    if (thumbnail) {
        thumbnail.classList.add('active');
    }
}

function selectColor(element) {
    if (!element.classList.contains('available')) return;

    if (element.classList.contains('active')) {
        element.classList.remove('active');
        selectedColor = null;
        return;
    }

    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    if (useCustomColorCheckbox) {
        useCustomColorCheckbox.checked = false;
        document.getElementById('customColorGroup').classList.add('d-none');
        document.getElementById('customColor').value = '';
        document.getElementById('customColor').disabled = true;
    }

    document.querySelectorAll('.color-item').forEach(item => {
        item.classList.remove('active');
    });

    element.classList.add('active');
    selectedColor = element.dataset.color;
    
    // Store the color ID if available
    if (element.dataset.colorId) {
        selectedColorId = element.dataset.colorId;
    }
}

function selectSize(element) {
    if (!element.classList.contains('available')) return;

    if (element.classList.contains('active')) {
        element.classList.remove('active');
        selectedSize = null;
        selectedSizeId = null;
        selectedVariantId = null;

        updatePrice();
        return;
    }

    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    if (useCustomSizeCheckbox) {
        useCustomSizeCheckbox.checked = false;
        document.getElementById('customSizeGroup').classList.add('d-none');
        document.getElementById('customSize').value = '';
        document.getElementById('customSize').disabled = true;
    }

    document.querySelectorAll('.size-option').forEach(item => {
        item.classList.remove('active');
    });

    element.classList.add('active');
    selectedSize = element.dataset.size;
    
    // Store the size ID if available
    if (element.dataset.sizeId) {
        selectedSizeId = element.dataset.sizeId;
    }

    // Store the variant ID if available
    if (element.dataset.variantId) {
        selectedVariantId = element.dataset.variantId;
    }

    updatePrice();
}

function updatePrice() {
    const priceElement = document.getElementById('product-price');
    const originalPrice = parseFloat(document.getElementById('original-price').value);
    let currentPrice = originalPrice;

    // إذا تم اختيار variant، نستخدم سعره
    if (selectedVariantId) {
        const variantElement = document.querySelector(`.size-option[data-variant-id="${selectedVariantId}"]`);
        if (variantElement && variantElement.dataset.price) {
            currentPrice = parseFloat(variantElement.dataset.price);
        }
    }
    // إذا تم اختيار مقاس محدد، نستخدم سعره
    else if (selectedSize) {
        const sizeElement = document.querySelector(`.size-option[data-size="${selectedSize}"]`);
        if (sizeElement && sizeElement.dataset.price) {
            currentPrice = parseFloat(sizeElement.dataset.price);
        }
    }
    // وإلا نستخدم السعر الأصلي
    else {
        currentPrice = originalPrice;
    }

    if (priceElement) {
        priceElement.textContent = currentPrice.toFixed(2) + ' ر.س';
    }
}

document.querySelectorAll('.size-option').forEach(el => {
    el.addEventListener('click', function() {
        selectedSize = this.dataset.size;
        document.querySelectorAll('.size-option').forEach(s => s.classList.remove('active'));
        this.classList.add('active');
        updatePrice();
    });
});

function addToCart() {
    const productId = document.getElementById('product-id').value;
    const quantity = parseInt(document.getElementById('productQuantity').value) || 1;

    const hasColorSelectionEnabled = document.querySelector('.colors-section') !== null;
    const hasCustomColorEnabled = document.getElementById('customColor') !== null;
    const hasSizeSelectionEnabled = document.querySelector('.available-sizes') !== null;
    const hasCustomSizeEnabled = document.getElementById('customSize') !== null;

    let colorValue = null;
    let colorId = null;
    let variantId = null;
    
    if (hasColorSelectionEnabled && selectedColor) {
        colorValue = selectedColor;
        colorId = selectedColorId;
    } else if (hasCustomColorEnabled) {
        const customColor = document.getElementById('customColor').value.trim();
        if (customColor) {
            colorValue = customColor;
        }
    }

    let sizeValue = null;
    let sizeId = null;
    if (hasSizeSelectionEnabled && selectedSize) {
        sizeValue = selectedSize;
        sizeId = selectedSizeId;
        // الحصول على variant_id من العنصر المختار
        const selectedSizeElement = document.querySelector('.size-option.active');
        if (selectedSizeElement && selectedSizeElement.dataset.variantId) {
            variantId = selectedSizeElement.dataset.variantId;
        }
    } else if (hasCustomSizeEnabled) {
        const customSize = document.getElementById('customSize').value.trim();
        if (customSize) {
            sizeValue = customSize;
        }
    }

    // Validate color and size selections
    if ((hasColorSelectionEnabled || hasCustomColorEnabled) && !colorValue) {
        let errorText = '';
        if (hasColorSelectionEnabled && hasCustomColorEnabled) {
            errorText = 'يرجى اختيار لون أو كتابة اللون المطلوب';
        } else if (hasColorSelectionEnabled) {
            errorText = 'يرجى اختيار لون للمنتج';
        } else if (hasCustomColorEnabled) {
            errorText = 'يرجى كتابة اللون المطلوب';
        }
        showNotification(errorText, 'error');
        return;
    }

    if ((hasSizeSelectionEnabled || hasCustomSizeEnabled) && !sizeValue) {
        let errorText = '';
        if (hasSizeSelectionEnabled && hasCustomSizeEnabled) {
            errorText = 'يرجى اختيار مقاس أو كتابة المقاس المطلوب';
        } else if (hasSizeSelectionEnabled) {
            errorText = 'يرجى اختيار مقاس للمنتج';
        } else if (hasCustomSizeEnabled) {
            errorText = 'يرجى كتابة المقاس المطلوب';
        }
        showNotification(errorText, 'error');
        return;
    }

    const addToCartBtn = document.querySelector('.btn-primary[onclick="addToCart()"]');
    const originalBtnText = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...';
    addToCartBtn.disabled = true;

    const data = {
        product_id: productId,
        quantity: quantity,
        color: colorValue,
        size: sizeValue,
        color_id: colorId,
        size_id: sizeId,
        variant_id: variantId
    };

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث عدد العناصر في جميع أيقونات السلة
            document.querySelectorAll('.cart-count').forEach(element => {
                element.textContent = data.cart_count;
            });

            showNotification('تم إضافة المنتج للسلة بنجاح', 'success');

            // تحديث محتوى السلة
            loadCartItems();

            // Reset form fields
            if (document.querySelector('.colors-section')) {
                selectedColor = null;
                document.querySelectorAll('.color-item').forEach(item => {
                    item.classList.remove('active');
                });
            }
            if (document.querySelector('.available-sizes')) {
                selectedSize = null;
                document.querySelectorAll('.size-option').forEach(item => {
                    item.classList.remove('active');
                });
            }

            if (document.getElementById('customColor')) {
                document.getElementById('customColor').value = '';
            }
            if (document.getElementById('customSize')) {
                document.getElementById('customSize').value = '';
            }
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إضافة المنتج للسلة', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء إضافة المنتج إلى السلة', 'error');
    })
    .finally(() => {
        const addToCartBtn = document.querySelector('.btn-primary[onclick="addToCart()"]');
        addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>أضف إلى السلة';
        addToCartBtn.disabled = false;
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast position-fixed top-0 start-50 translate-middle-x mt-3`;
    notification.style.zIndex = '9999';
    notification.style.opacity = '1';
    notification.innerHTML = message;
    document.body.appendChild(notification);

    // إظهار الإشعار لمدة 15 ثانية
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s ease';
        // إزالة العنصر بعد انتهاء التأثير البصري
        setTimeout(() => notification.remove(), 500);
    }, 6000);
}

function updateCartDisplay(data) {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCountElements = document.querySelectorAll('.cart-count');

    cartCountElements.forEach(element => {
        element.textContent = data.count;
    });

    cartTotal.textContent = data.total + ' ر.س';

    cartItems.innerHTML = '';

    if (!data.items || data.items.length === 0) {
        cartItems.innerHTML = `
            <div class="cart-empty text-center p-4">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p class="mb-3">السلة فارغة</p>
                <a href="/products" class="btn btn-primary">تصفح المنتجات</a>
            </div>
        `;
        return;
    }

    data.items.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item';
        itemElement.dataset.itemId = item.id;

        const additionalInfo = [];
        if (item.color) additionalInfo.push(`اللون: ${item.color}`);
        if (item.size) additionalInfo.push(`المقاس: ${item.size}`);

        itemElement.innerHTML = `
            <div class="cart-item-inner p-3 border-bottom">
                <button class="remove-btn btn btn-link text-danger" onclick="removeFromCart(this, ${item.id})">
                    <i class="fas fa-times"></i>
                </button>
                <div class="d-flex gap-3">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details flex-grow-1">
                        <h5 class="cart-item-title mb-2">${item.name}</h5>
                        <div class="cart-item-info mb-2">
                            ${additionalInfo.length > 0 ?
                                `<small class="text-muted">${additionalInfo.join(' | ')}</small>` : ''}
                        </div>
                        <div class="cart-item-price mb-2">${item.price} ر.س</div>
                        <div class="quantity-controls d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.id}, -1)">-</button>
                            <input type="number" value="${item.quantity}" min="1"
                                onchange="updateCartQuantity(${item.id}, 0, this.value)"
                                class="form-control form-control-sm quantity-input">
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.id}, 1)">+</button>
                        </div>
                        <div class="cart-item-subtotal mt-2 text-primary">
                            الإجمالي: ${item.subtotal} ر.س
                        </div>
                    </div>
                </div>
            </div>
        `;
        cartItems.appendChild(itemElement);
    });
}

function updateCartQuantity(itemId, change, newValue = null) {
    const quantityInput = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
    const currentValue = parseInt(quantityInput.value);
    let quantity = newValue !== null ? parseInt(newValue) : currentValue + change;

    if (quantity < 1) {
        return;
    }

    const cartItem = quantityInput.closest('.cart-item');
    cartItem.style.opacity = '0.5';

    fetch(`/cart/items/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            quantityInput.value = quantity;
            const subtotalElement = cartItem.querySelector('.cart-item-subtotal');
            subtotalElement.textContent = `الإجمالي: ${data.item_subtotal} ر.س`;

            const cartTotal = document.getElementById('cartTotal');
            if (cartTotal) {
                cartTotal.textContent = data.cart_total + ' ر.س';
            }

            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = data.cart_count;
            });

            showNotification('تم تحديث الكمية بنجاح', 'success');
        } else {
            quantityInput.value = currentValue;
            showNotification(data.message || 'فشل تحديث الكمية', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        quantityInput.value = currentValue;
        showNotification('حدث خطأ أثناء تحديث الكمية', 'error');
    })
    .finally(() => {
        cartItem.style.opacity = '1';
    });
}

function removeFromCart(button, cartItemId) {
    if (event) {
        event.preventDefault();
    }

    if (!confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
        return;
    }

    const cartItem = button.closest('.cart-item') || document.querySelector(`[data-item-id="${cartItemId}"]`);
    if (cartItem) {
        cartItem.style.opacity = '0.5';
    }

    fetch(`/cart/remove/${cartItemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (cartItem) {
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(50px)';
            }

            // تحديث عرض السلة مباشرة
            updateCartDisplay(data);
            showNotification('تم حذف المنتج من السلة بنجاح', 'success');

            // إعادة تحميل عناصر السلة
            loadCartItems();
        } else {
            if (cartItem) {
                cartItem.style.opacity = '1';
            }
            showNotification(data.message || 'حدث خطأ أثناء حذف المنتج', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (cartItem) {
            cartItem.style.opacity = '1';
        }
        showNotification('حدث خطأ أثناء حذف المنتج', 'error');
    });
}

function openCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.querySelector('.cart-overlay');

    if (cartSidebar) {
        cartSidebar.classList.add('active');
    }

    if (cartOverlay) {
        cartOverlay.classList.add('active');
    }

    document.body.classList.add('cart-open');
}

function closeCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.querySelector('.cart-overlay');

    if (cartSidebar) {
        cartSidebar.classList.remove('active');
    }

    if (cartOverlay) {
        cartOverlay.classList.remove('active');
    }

    document.body.classList.remove('cart-open');
}

function loadCartItems() {
    fetch('/cart/items')
        .then(response => response.json())
        .then(data => {
            updateCartDisplay(data);
        })
        .catch(error => console.error('Error:', error));
}

function showLoginPrompt(loginUrl) {
    const currentUrl = window.location.href;
    const modal = new bootstrap.Modal(document.getElementById('loginPromptModal'));
    document.getElementById('loginButton').href = `${loginUrl}?redirect=${encodeURIComponent(currentUrl)}`;
    modal.show();
}

function updateFeatureVisibility(productFeatures) {
    const colorsSection = document.querySelector('.colors-section');
    const customColorSection = document.querySelector('.custom-color-section');
    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    const customColorGroup = document.getElementById('customColorGroup');

    if (colorsSection) {
        colorsSection.style.display = productFeatures.allow_color_selection ? 'block' : 'none';
    }

    if (customColorSection) {
        customColorSection.style.display = productFeatures.allow_custom_color ? 'block' : 'none';
    }

    if (useCustomColorCheckbox && customColorGroup) {
        useCustomColorCheckbox.closest('.custom-color-input').style.display =
            productFeatures.allow_custom_color ? 'block' : 'none';
    }

    const sizesSection = document.querySelector('.available-sizes');
    const customSizeInput = document.querySelector('.custom-size-input');
    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    const customSizeGroup = document.getElementById('customSizeGroup');

    if (sizesSection) {
        sizesSection.style.display = productFeatures.allow_size_selection ? 'block' : 'none';
    }

    if (customSizeInput) {
        customSizeInput.style.display = productFeatures.allow_custom_size ? 'block' : 'none';
    }
}

function toggleCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    if (cartSidebar) {
        if (cartSidebar.classList.contains('active')) {
            closeCart();
        } else {
            openCart();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.body.classList.contains('user-logged-in')) {
        loadCartItems();
    }

    const closeCartBtn = document.getElementById('closeCart');
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', closeCart);
    }


    const cartToggleBtn = document.getElementById('cartToggle');
    if (cartToggleBtn) {
        cartToggleBtn.addEventListener('click', toggleCart);
    }

    const fixedCartBtn = document.getElementById('fixedCartBtn');
    if (fixedCartBtn) {
        fixedCartBtn.addEventListener('click', toggleCart);
    }

    const cartOverlay = document.querySelector('.cart-overlay');
    if (cartOverlay) {
        cartOverlay.addEventListener('click', closeCart);
    }


    const decreaseQuantityBtn = document.getElementById('decreaseQuantity');
    const increaseQuantityBtn = document.getElementById('increaseQuantity');
    const quantityInput = document.getElementById('productQuantity');

    if (decreaseQuantityBtn && increaseQuantityBtn && quantityInput) {
        decreaseQuantityBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        increaseQuantityBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            quantityInput.value = currentValue + 1;
        });

        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            }
        });
    }

    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    const customColorGroup = document.getElementById('customColorGroup');

    if (useCustomColorCheckbox) {
        useCustomColorCheckbox.addEventListener('change', function() {
            if (this.checked) {
                customColorGroup.classList.remove('d-none');
                document.querySelectorAll('.color-item').forEach(item => {
                    item.classList.remove('active');
                });
                selectedColor = null;
            } else {
                customColorGroup.classList.add('d-none');
                document.getElementById('customColor').value = '';
            }
        });
    }

    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    const customSizeGroup = document.getElementById('customSizeGroup');

    if (useCustomSizeCheckbox) {
        useCustomSizeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                customSizeGroup.classList.remove('d-none');
                document.querySelectorAll('.size-item').forEach(item => {
                    item.classList.remove('active');
                });
                selectedSize = null;
            } else {
                customSizeGroup.classList.add('d-none');
                document.getElementById('customSize').value = '';
            }
        });
    }

    const customSizeInput = document.getElementById('customSize');
    if (customSizeInput) {
        customSizeInput.addEventListener('input', function() {
        });
    }

    const customColorInput = document.getElementById('customColor');
    if (customColorInput) {
        customColorInput.addEventListener('input', function() {
            // Removed error message clearing code
        });
    }

    const productId = document.getElementById('product-id').value;
    fetch(`/products/${productId}/details`)
        .then(response => response.json())
        .then(data => {
            updateFeatureVisibility(data.features);
        })
        .catch(error => console.error('Error:', error));

    // إضافة وظائف لأزرار نسخ الكوبونات
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const couponCode = this.getAttribute('data-code');
            navigator.clipboard.writeText(couponCode).then(() => {
                // تغيير شكل الزر مؤقتًا ليشير إلى نجاح النسخ
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-success');

                // عرض إشعار للمستخدم
                showNotification(`تم نسخ كود الخصم "${couponCode}" بنجاح`, 'success');

                // إعادة الزر إلى حالته الأصلية بعد ثانيتين
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(err => {
                showNotification('حدث خطأ أثناء نسخ الكود', 'error');
                console.error('Could not copy text: ', err);
            });
        });
    });
});

function toggleCustomSize(checkbox) {
    const customSizeGroup = document.getElementById('customSizeGroup');
    const customSizeInput = document.getElementById('customSize');

    if (checkbox.checked) {
        customSizeGroup.classList.remove('d-none');
        customSizeInput.disabled = false;
        customSizeInput.focus();

        document.querySelectorAll('.size-option').forEach(item => {
            item.classList.remove('active');
        });
        selectedSize = null;

        updatePrice();
    } else {
        customSizeGroup.classList.add('d-none');
        customSizeInput.value = '';
        customSizeInput.disabled = true;
    }
}

function toggleCustomColor(checkbox) {
    const customColorGroup = document.getElementById('customColorGroup');
    const customColorInput = document.getElementById('customColor');

    if (checkbox.checked) {
        customColorGroup.classList.remove('d-none');
        customColorInput.disabled = false;
        customColorInput.focus();

        document.querySelectorAll('.color-item').forEach(item => {
            item.classList.remove('active');
        });
        selectedColor = null;
    } else {
        customColorGroup.classList.add('d-none');
        customColorInput.value = '';
        customColorInput.disabled = true;
    }
}
