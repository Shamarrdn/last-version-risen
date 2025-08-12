let activeFilters = {
    categories: [],
    minPrice: 0,
    maxPrice: 1000,
    sort: 'newest',
    hasDiscounts: false
};

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();

    if (document.body.classList.contains('user-logged-in')) {
        loadCartItems();
    }

    document.getElementById('closeCart').addEventListener('click', closeCart);

    document.getElementById('cartToggle')?.addEventListener('click', function() {
        if (!document.body.classList.contains('user-logged-in')) {
            showLoginPrompt('{{ route("login") }}');
            return;
        }

        const cartSidebar = document.getElementById('cartSidebar');
        if (cartSidebar.classList.contains('active')) {
            closeCart();
        } else {
            openCart();
        }
    });

    document.getElementById('fixedCartBtn')?.addEventListener('click', function() {
        if (!document.body.classList.contains('user-logged-in')) {
            showLoginPrompt('{{ route("login") }}');
            return;
        }

        const cartSidebar = document.getElementById('cartSidebar');
        if (cartSidebar.classList.contains('active')) {
            closeCart();
        } else {
            openCart();
        }
    });

    document.querySelector('.cart-overlay')?.addEventListener('click', closeCart);

    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            quickAddToCart(this.dataset.productId);
        });
    });
});

function initializeFilters() {
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');
    let priceUpdateTimeout;

    if (priceRange) {
        priceRange.addEventListener('input', function() {
            priceValue.textContent = Number(this.value).toLocaleString() + ' ر.س';

            clearTimeout(priceUpdateTimeout);
            priceUpdateTimeout = setTimeout(() => {
                activeFilters.maxPrice = Number(this.value);
                applyFilters();
            }, 500);
        });

        priceRange.addEventListener('change', function() {
            clearTimeout(priceUpdateTimeout);
            activeFilters.maxPrice = Number(this.value);
            applyFilters();
        });
    }

    document.querySelectorAll('.form-check-input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.id === 'discountFilter') {
                activeFilters.hasDiscounts = this.checked;
                applyFilters();
                return;
            }

            const categorySlug = this.value;
            if (this.checked) {
                if (!activeFilters.categories.includes(categorySlug)) {
                    activeFilters.categories.push(categorySlug);
                }
            } else {
                activeFilters.categories = activeFilters.categories.filter(slug => slug !== categorySlug);
            }
            applyFilters();
        });
    });

    document.getElementById('sortSelect').addEventListener('change', function() {
        activeFilters.sort = this.value;
        applyFilters();
    });
}

function applyFilters() {
    const productGrid = document.getElementById('productGrid');
    productGrid.style.opacity = '0.5';

    const filterData = {
        categories: Array.isArray(activeFilters.categories) ? activeFilters.categories : [],
        minPrice: isNaN(Number(activeFilters.minPrice)) ? 0 : Number(activeFilters.minPrice),
        maxPrice: isNaN(Number(activeFilters.maxPrice)) ? 1000 : Number(activeFilters.maxPrice),
        sort: activeFilters.sort || 'newest',
        has_discounts: activeFilters.hasDiscounts ? 1 : 0
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const token = csrfToken ? csrfToken.content : '';

    fetch(window.appConfig.routes.products.filter, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(filterData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server responded with ${response.status}: ${response.statusText}`);
        }
        return response.text().then(text => {
            try {
                return text ? JSON.parse(text) : {};
            } catch (e) {
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.success === false) {
            throw new Error(data.message || 'حدث خطأ أثناء تحديث المنتجات');
        }

        updateProductGrid(data.products || []);

        if (data.pagination) {
            updatePagination(data.pagination);
        } else if (data.links) {
            updatePagination(data.links);
        }
    })
    .catch(error => {
        showNotification(error.message || 'حدث خطأ أثناء تحديث المنتجات', 'error');
        updateProductGrid([]);
    })
    .finally(() => {
        productGrid.style.opacity = '1';
    });
}

function updateProductGrid(products) {
    const productGrid = document.getElementById('productGrid');
    productGrid.innerHTML = '';

    if (!products || products.length === 0) {
        productGrid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                <h3>لا توجد منتجات</h3>
                <p class="text-muted">لم يتم العثور على منتجات تطابق معايير البحث</p>
                <button onclick="resetFilters()" class="btn btn-primary mt-3">
                    <i class="fas fa-sync-alt me-2"></i>
                    إعادة ضبط الفلتر
                </button>
            </div>
        `;
        return;
    }

    products.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'col-md-6 col-lg-4';

        const couponBadgeHtml = product.coupon_badge
            ? product.coupon_badge.badge_html
            : '';

        const imagePath = product.image_url ||
                         (product.images && product.images.length > 0 && product.images[0]?.image_path
                          ? '/storage/' + product.images[0].image_path
                          : '/images/placeholder.jpg');

        productElement.innerHTML = `
            <div class="product-card">
                <a href="/products/${product.slug}" class="product-image-wrapper position-relative">
                    <img src="${imagePath}"
                         alt="${product.name}"
                         class="product-image">
                    ${couponBadgeHtml}
                </a>
                <div class="product-details">
                    <div class="product-category d-flex flex-wrap gap-1 align-items-center mb-2">
                        <a href="?category=${getCategorySlug(product)}" class="text-decoration-none">
                            <span class="badge rounded-pill bg-primary">${product.category?.name || product.category}</span>
                        </a>
                        ${renderAdditionalCategories(product)}
                    </div>
                    <a href="/products/${product.slug}" class="product-title text-decoration-none">
                        <h3>${product.name}</h3>
                    </a>
                    <div class="product-rating">
                        <div class="stars" style="--rating: ${product.rating || 0}"></div>

                    </div>
                    <p class="product-price">${product.price_display || (
                        product.price_range ? (
                            product.price_range.min === product.price_range.max ?
                            `${product.price_range.min.toLocaleString()} ر.س` :
                            `${product.price_range.min.toLocaleString()} - ${product.price_range.max.toLocaleString()} ر.س`
                        ) : '0 ر.س'
                    )}</p>
                    <div class="product-actions">
                        <a href="/products/${product.slug}" class="order-product-btn">
                            <i class="fas fa-shopping-cart me-2"></i>
                            طلب المنتج
                        </a>
                    </div>
                </div>
            </div>
        `;
        productGrid.appendChild(productElement);
    });
}

function updatePagination(pagination) {
    const paginationContainer = document.querySelector('.pagination');
    if (!paginationContainer) return;

    paginationContainer.innerHTML = '';

    const links = pagination.links || pagination;
    const prevLink = pagination.prev || (links.prev_page_url ? links.prev_page_url : null);
    const nextLink = pagination.next || (links.next_page_url ? links.next_page_url : null);

    if (prevLink) {
        paginationContainer.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadPage('${prevLink}'); return false;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }

    if (links.links && Array.isArray(links.links)) {
        links.links.forEach(link => {
            if (link.url === null) return;

            paginationContainer.innerHTML += `
                <li class="page-item ${link.active ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadPage('${link.url}'); return false;">
                        ${link.label}
                    </a>
                </li>
            `;
        });
    } else if (pagination.current_page && pagination.last_page) {
        for (let i = 1; i <= pagination.last_page; i++) {
            paginationContainer.innerHTML += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadPage('${window.location.pathname}?page=${i}'); return false;">
                        ${i}
                    </a>
                </li>
            `;
        }
    }

    if (nextLink) {
        paginationContainer.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadPage('${nextLink}'); return false;">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    }
}

function loadPage(url) {
    const productGrid = document.getElementById('productGrid');
    productGrid.style.opacity = '0.5';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server responded with ${response.status}: ${response.statusText}`);
        }
        return response.text().then(text => {
            try {
                return text ? JSON.parse(text) : {};
            } catch (e) {
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.success === false) {
            throw new Error(data.message || 'حدث خطأ أثناء تحميل الصفحة');
        }
        updateProductGrid(data.products || []);
        if (data.pagination) {
            updatePagination(data.pagination);
        } else if (data.links) {
            updatePagination(data.links);
        }
    })
    .catch(error => {
        showNotification('حدث خطأ أثناء تحميل الصفحة', 'error');
        updateProductGrid([]);
    })
    .finally(() => {
        productGrid.style.opacity = '1';
    });
}

function resetFilters() {
    document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    const discountFilter = document.getElementById('discountFilter');
    if (discountFilter) {
        discountFilter.checked = false;
    }

    const priceRangeInput = document.getElementById('priceRange');
    if (priceRangeInput) {
        priceRangeInput.value = priceRangeInput.max;
        document.getElementById('priceValue').textContent = Number(priceRangeInput.max).toLocaleString() + ' ر.س';
    }

    document.getElementById('sortSelect').value = 'newest';

    activeFilters = {
        categories: [],
        minPrice: Number(priceRangeInput?.min || 0),
        maxPrice: Number(priceRangeInput?.max || 1000),
        sort: 'newest',
        hasDiscounts: false
    };

    applyFilters();
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.innerHTML = message;
    document.body.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateCartDisplay(data) {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCountElements = document.querySelectorAll('.cart-count');

    cartCountElements.forEach(element => {
        element.textContent = data.count || data.cart_count;
    });

    cartTotal.textContent = (data.total || data.cart_total) + ' ر.س';

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
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, -1)">-</button>
                            <input type="number" value="${item.quantity}" min="1"
                                onchange="updateQuantity(${item.id}, 0, this.value)"
                                class="form-control form-control-sm quantity-input">
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${item.id}, 1)">+</button>
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

function updateQuantity(itemId, change, newValue = null) {
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
            const cartCountElements = document.querySelectorAll('.cart-count');

            cartTotal.textContent = data.cart_total + ' ر.س';
            cartCountElements.forEach(element => {
                element.textContent = data.cart_count;
            });
        } else {
            quantityInput.value = currentValue;
            showNotification(data.message || 'فشل تحديث الكمية', 'error');
        }
    })
    .catch(error => {
        quantityInput.value = currentValue;
        showNotification('حدث خطأ أثناء تحديث الكمية', 'error');
    })
    .finally(() => {
        cartItem.style.opacity = '1';
    });
}

function removeFromCart(button, cartItemId) {
    event.preventDefault();

    if (!confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
        return;
    }

    const cartItem = button.closest('.cart-item');
    cartItem.style.opacity = '0.5';

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
            cartItem.style.opacity = '0';
            cartItem.style.transform = 'translateX(50px)';

            updateCartDisplay(data);

            setTimeout(() => {
                loadCartItems();
            }, 300);

            showNotification('تم حذف المنتج من السلة بنجاح', 'success');
        } else {
            cartItem.style.opacity = '1';
            showNotification(data.message || 'حدث خطأ أثناء حذف المنتج', 'error');
        }
    })
    .catch(error => {
        cartItem.style.opacity = '1';
        showNotification('حدث خطأ أثناء حذف المنتج', 'error');
    });
}

function openCart() {
    document.getElementById('cartSidebar').classList.add('active');
    document.querySelector('.cart-overlay').classList.add('active');
    document.body.classList.add('cart-open');
}

function closeCart() {
    document.getElementById('cartSidebar').classList.remove('active');
    document.querySelector('.cart-overlay').classList.remove('active');
    document.body.classList.remove('cart-open');
}

function loadCartItems() {
    fetch('/cart/items')
        .then(response => response.json())
        .then(data => updateCartDisplay(data))
        .catch(error => {});
}

function showLoginPrompt(loginUrl) {
    const currentUrl = window.location.href;
    const modal = new bootstrap.Modal(document.getElementById('loginPromptModal'));
    document.getElementById('loginButton').href = `${loginUrl}?redirect=${encodeURIComponent(currentUrl)}`;
    modal.show();
}

function renderAdditionalCategories(product) {
    if (!product.categories || product.categories.length <= 1) {
        return '';
    }

    // Get the primary category ID to exclude it
    const primaryCategoryId = product.category_id;

    // Filter out the primary category
    const additionalCategoriesHtml = product.categories
        .filter(category => category.id != primaryCategoryId)
        .map(category => {
            return `<a href="?category=${category.slug}" class="text-decoration-none">
                <span class="badge rounded-pill bg-light text-dark border">${category.name}</span>
            </a>`;
        }).join('');

    return additionalCategoriesHtml;
}

function getCategorySlug(product) {
    // For backward compatibility - some products might have category as string
    if (typeof product.category === 'string') {
        // Try to find the category in the categories array
        const mainCategory = product.categories?.find(cat => cat.name === product.category);
        return mainCategory?.slug || '';
    }

    // If product has category_id, find that category's slug
    if (product.category_id && product.categories?.length) {
        const mainCategory = product.categories.find(cat => cat.id === product.category_id);
        return mainCategory?.slug || '';
    }

    // Fallback to the first category
    return product.categories?.[0]?.slug || '';
}
