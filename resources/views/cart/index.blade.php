@extends('layouts.customer')

@section('title', 'سلة التسوق')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('assets/css/customer/cart.css') }}">
<style>
  /* Ensure right-to-left (RTL) layout */
  html {
    direction: rtl;
  }

  /* تحسين تصميم الـ quantity control */
  .quantity-control {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* منع التعديل المباشر في input الكمية */
  .quantity-input {
    background-color: #f8f9fa !important;
    cursor: default !important;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
  }

  /* إخفاء أسهم input number */
  .quantity-input::-webkit-outer-spin-button,
  .quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .quantity-input[type=number] {
    -moz-appearance: textfield;
  }



  /* Maximize space on mobile */
  @media (max-width: 768px) {
    .container.py-4 {
      padding-left: 0;
      padding-right: 0;
      max-width: 100%;
    }

    .row {
      margin-left: 0;
      margin-right: 0;
    }

    .col-12, .col-lg-8, .col-lg-4 {
      padding-left: 0;
      padding-right: 0;
    }
  }
</style>
@endsection

@section('content')
<div class="container py-4">
  <div id="alerts-container"></div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">سلة التسوق</h2>
    <span class="text-muted">{{ $cart_items_count ?? 0 }} منتجات</span>
  </div>

  <div class="cart-container">
    @if(isset($cart_items) && count($cart_items) > 0)
    <div class="row">
      <div class="col-12 col-lg-8">
        @foreach($cart_items as $item)
        @php
          $itemPrice = $item->unit_price;
          $itemSubtotal = $item->subtotal;
          // Get any available image for the product, not just primary
          $productImage = $item->product->images->first();
          $imagePath = $productImage ? url('storage/' . $productImage->image_path) : url('images/no-image.png');
        @endphp
        <div class="cart-item" data-item-id="{{ $item->id }}">
          <button type="button" class="remove-item" onclick="removeCartItem({{ $item->id }})">
            <i class="bi bi-x-circle"></i>
          </button>

          <img src="{{ $imagePath }}" alt="{{ $item->product->name }}" class="cart-item-image">

          <div class="cart-item-details">
            <div class="d-flex justify-content-between align-items-start w-100">
              <div>
                <h5 class="cart-item-title">{{ $item->product->name }}</h5>
                <div class="cart-item-meta">
                  @if($item->product->category)
                  <span>{{ $item->product->category->name }}</span>
                  @endif
                  @if($item->size)
                  <span>المقاس: {{ $item->size }}</span>
                  @endif
                  @if($item->color)
                  <span>اللون: {{ $item->color }}</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="cart-item-bottom">
              <div class="quantity-control">
                <button type="button" class="quantity-btn decrease" onclick="updateQuantity({{ $item->id }}, -1)">
                  <i class="bi bi-dash"></i>
                </button>
                <input type="number" value="{{ $item->quantity }}" min="1" class="quantity-input"
                       onchange="updateQuantity({{ $item->id }}, 0, this.value)" readonly>
                <button type="button" class="quantity-btn increase" 
                        onclick="updateQuantity({{ $item->id }}, 1)"
                        data-product-id="{{ $item->product_id }}"
                        data-size="{{ $item->size ?? '' }}"
                        data-color="{{ $item->color ?? '' }}"
                        data-size-id="{{ $item->size_id ?? '' }}"
                        data-color-id="{{ $item->color_id ?? '' }}">
                  <i class="bi bi-plus"></i>
                </button>
              </div>

              <div class="cart-item-price">
                <div class="d-flex align-items-center">
                  <span class="price-label">سعر الوحدة:</span>
                  <div class="price-box unit-price">
                    {{ number_format($itemPrice, 2) }} ريال
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <span class="price-label">الإجمالي الفرعي:</span>
                  <div class="price-box subtotal" id="price-{{ $item->id }}">
                    {{ number_format($itemSubtotal, 2) }} ريال
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="col-lg-4">
        <div class="cart-summary">
          <h4>ملخص الطلب</h4>
          <div class="summary-item">
            <span class="summary-label">إجمالي المنتجات</span>
            <span class="summary-value" id="subtotal">{{ number_format($subtotal, 2) }} ريال</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">الإجمالي الكلي</span>
            <span class="total-amount" id="total">{{ number_format($total, 2) }} ريال</span>
          </div>
          <a href="{{ route('checkout.index') }}" class="checkout-btn">
            متابعة الشراء
          </a>
          <div class="continue-shopping">
            <a href="{{ route('products.index') }}">
              <i class="bi bi-arrow-right"></i>
              متابعة التسوق
            </a>
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="empty-cart">
      <div class="empty-cart-icon">
        <i class="bi bi-cart-x"></i>
      </div>
      <h3>السلة فارغة</h3>
      <p>لم تقم بإضافة أي منتجات إلى سلة التسوق بعد</p>
      <a href="{{ route('products.index') }}" class="btn">
        تصفح المنتجات
      </a>
    </div>
    @endif
  </div>
</div>

@endsection

@section('scripts')
<script>
function showAlert(message, type = 'success') {
    const alertsContainer = document.getElementById('alerts-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        <span>${message}</span>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
    `;
    alertsContainer.appendChild(alert);

    // Auto hide after 3 seconds
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 150);
    }, 3000);
}

function formatPrice(price) {
    return new Intl.NumberFormat('ar-SA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price) + ' ريال';
}

function checkInventoryBeforeIncrease(itemId, productId, size, color, requestedQuantity, currentQuantity) {
    // عرض loading على الزر
    const increaseBtn = document.querySelector(`[data-item-id="${itemId}"] .quantity-btn.increase`);
    const originalHtml = increaseBtn.innerHTML;
    increaseBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    increaseBtn.disabled = true;

    // إرسال طلب فحص المخزون
    fetch('/check-inventory', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            size: size,
            color: color,
            requested_quantity: requestedQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.available) {
            // المخزون متاح، تابع عملية الزيادة
            proceedWithQuantityUpdate(itemId, requestedQuantity);
        } else {
            // المخزون غير متاح
            showAlert(data.message || 'عذراً، الكمية المطلوبة غير متاحة في المخزون', 'warning');
        }
    })
    .catch(error => {
        console.error('Error checking inventory:', error);
        showAlert('حدث خطأ أثناء التحقق من المخزون', 'danger');
    })
    .finally(() => {
        // إعادة الزر لحالته الطبيعية
        increaseBtn.innerHTML = originalHtml;
        increaseBtn.disabled = false;
    });
}

async function proceedWithQuantityUpdate(itemId, quantity) {
    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
    const input = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
    const oldQuantity = parseInt(input.value);
    
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
            input.value = quantity;

            // تحديث السعر الفرعي للمنتج
            document.getElementById(`price-${itemId}`).textContent = formatPrice(data.item_subtotal);

            // تحديث إجمالي السلة
            document.getElementById('total').textContent = formatPrice(data.cart_total);
            document.getElementById('subtotal').textContent = formatPrice(data.cart_total);

            // تحديث المخزون المحلي للمنتج
            const increaseBtn = document.querySelector(`[data-item-id="${itemId}"] .quantity-btn.increase`);
            const productId = increaseBtn.getAttribute('data-product-id');
            const size = increaseBtn.getAttribute('data-size');
            const color = increaseBtn.getAttribute('data-color');
            const sizeId = increaseBtn.getAttribute('data-size-id');
            const colorId = increaseBtn.getAttribute('data-color-id');
            
            // استخدم الـ IDs إذا كانت متوفرة، وإلا استخدم الأسماء
            const useColorId = colorId || color;
            const useSizeId = sizeId || size;
            
            updateProductLocalInventory(productId, useSizeId, useColorId, quantity - oldQuantity);

            showAlert('تم تحديث الكمية بنجاح');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('حدث خطأ أثناء تحديث الكمية', 'danger');
    })
    .finally(() => {
        cartItem.style.opacity = '1';
    });
}

// دالة تحديث المخزون المحلي للمنتج
function updateProductLocalInventory(productId, size, color, quantityChange) {
    if (!productId || quantityChange === 0) return;
    
    const storageKey = `inventory_reduction_${productId}`;
    let localInventoryReduction = {};
    
    // تحميل البيانات الحالية من localStorage
    const savedData = localStorage.getItem(storageKey);
    if (savedData) {
        try {
            localInventoryReduction = JSON.parse(savedData);
        } catch (e) {
            console.error('Error parsing stored inventory data:', e);
            localInventoryReduction = {};
        }
    }
    
    // محاولة إنشاء مفاتيح متعددة لضمان التوافق
    let keyToUse = null;
    
    if (size && color) {
        // إذا كانت القيم أرقام (IDs)، استخدمها مباشرة
        const isNumericColor = !isNaN(color) && color !== '';
        const isNumericSize = !isNaN(size) && size !== '';
        
        let possibleKeys = [];
        
        if (isNumericColor && isNumericSize) {
            // استخدم الـ IDs مباشرة (التنسيق المفضل)
            possibleKeys = [
                `${color}_${size}`,
                `${size}_${color}`
            ];
        } else {
            // استخدم الأسماء مع تنظيفها
            possibleKeys = [
                `${color}_${size}`,
                `${size}_${color}`,
                // أنماط أخرى محتملة (مع تنظيف الأسماء)
                `${color.toString().trim().toLowerCase()}_${size.toString().trim().toLowerCase()}`,
                `${size.toString().trim().toLowerCase()}_${color.toString().trim().toLowerCase()}`,
                // تنسيقات محتملة أخرى
                `${color.toString().replace(/\s+/g, '')}_${size.toString().replace(/\s+/g, '')}`,
                `${size.toString().replace(/\s+/g, '')}_${color.toString().replace(/\s+/g, '')}`
            ];
        }
        
        // ابحث عن مفتاح موجود
        for (const key of possibleKeys) {
            if (localInventoryReduction.hasOwnProperty(key)) {
                keyToUse = key;
                console.log('Found existing key:', key);
                break;
            }
        }
        
        // إذا لم نجد مفتاح موجود، استخدم الافتراضي
        if (!keyToUse) {
            keyToUse = `${color}_${size}`;
            console.log('Using default key:', keyToUse);
        }
        
        // تحديث التخفيض المحلي
        localInventoryReduction[keyToUse] = (localInventoryReduction[keyToUse] || 0) + quantityChange;
        
        // تأكد من أن القيمة لا تقل عن الصفر
        if (localInventoryReduction[keyToUse] <= 0) {
            delete localInventoryReduction[keyToUse];
        }
        
        console.log('Updated local inventory for product', productId, 'key:', keyToUse, 'change:', quantityChange);
    } else {
        console.warn('Missing size or color information for local inventory update');
        return;
    }
    
    // حفظ التحديث في localStorage
    localStorage.setItem(storageKey, JSON.stringify(localInventoryReduction));
    
    console.log('New local inventory:', localInventoryReduction);
}

async function updateQuantity(itemId, change, newValue = null) {
    const input = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
    const currentValue = parseInt(input.value);
    let quantity = newValue !== null ? parseInt(newValue) : currentValue + change;

    if (quantity < 1) return;

    // إذا كانت زيادة في الكمية، تحقق من المخزون أولاً
    if (change > 0 || (newValue !== null && newValue > currentValue)) {
        const increaseBtn = document.querySelector(`[data-item-id="${itemId}"] .quantity-btn.increase`);
        const productId = increaseBtn.getAttribute('data-product-id');
        const size = increaseBtn.getAttribute('data-size');
        const color = increaseBtn.getAttribute('data-color');
        
        // استدعاء فحص المخزون قبل الزيادة
        checkInventoryBeforeIncrease(itemId, productId, size, color, quantity, currentValue);
        return;
    }

    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
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
            input.value = quantity;

            // تحديث السعر الفرعي للمنتج
            document.getElementById(`price-${itemId}`).textContent = formatPrice(data.item_subtotal);

            // تحديث إجمالي السلة
            document.getElementById('total').textContent = formatPrice(data.cart_total);
            document.getElementById('subtotal').textContent = formatPrice(data.cart_total);

            // تحديث المخزون المحلي للمنتج
            const increaseBtn = document.querySelector(`[data-item-id="${itemId}"] .quantity-btn.increase`);
            const productId = increaseBtn.getAttribute('data-product-id');
            const size = increaseBtn.getAttribute('data-size');
            const color = increaseBtn.getAttribute('data-color');
            const sizeId = increaseBtn.getAttribute('data-size-id');
            const colorId = increaseBtn.getAttribute('data-color-id');
            
            // استخدم الـ IDs إذا كانت متوفرة، وإلا استخدم الأسماء
            const useColorId = colorId || color;
            const useSizeId = sizeId || size;
            
            updateProductLocalInventory(productId, useSizeId, useColorId, quantity - currentValue);

            showAlert('تم تحديث الكمية بنجاح');
        } else {
            input.value = currentValue;
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        input.value = currentValue;
        showAlert('حدث خطأ أثناء تحديث الكمية', 'danger');
    })
    .finally(() => {
        cartItem.style.opacity = '1';
    });
}

async function removeCartItem(itemId) {
    if (!confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
        return;
    }

    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
    
    // الحصول على بيانات المنتج قبل الحذف
    const increaseBtn = document.querySelector(`[data-item-id="${itemId}"] .quantity-btn.increase`);
    const productId = increaseBtn ? increaseBtn.getAttribute('data-product-id') : null;
    const size = increaseBtn ? increaseBtn.getAttribute('data-size') : null;
    const color = increaseBtn ? increaseBtn.getAttribute('data-color') : null;
    const sizeId = increaseBtn ? increaseBtn.getAttribute('data-size-id') : null;
    const colorId = increaseBtn ? increaseBtn.getAttribute('data-color-id') : null;
    const currentQuantity = parseInt(document.querySelector(`[data-item-id="${itemId}"] .quantity-input`).value) || 0;
    
    cartItem.style.opacity = '0.5';

    try {
        const response = await fetch(`/cart/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // إرجاع الكمية للمخزون المحلي (نقص التخفيض المحلي)
            if (productId && currentQuantity > 0) {
                // استخدم الـ IDs إذا كانت متوفرة، وإلا استخدم الأسماء
                const useColorId = colorId || color;
                const useSizeId = sizeId || size;
                
                if (useColorId && useSizeId) {
                    updateProductLocalInventory(productId, useSizeId, useColorId, -currentQuantity);
                    console.log('تم استعادة المخزون المحلي:', {
                        productId, 
                        colorId: useColorId, 
                        sizeId: useSizeId, 
                        quantity: currentQuantity
                    });
                } else {
                    console.warn('Missing color or size data for inventory restoration');
                }
            }
            
            cartItem.style.transform = 'translateX(100px)';
            cartItem.style.opacity = '0';

            setTimeout(() => {
                cartItem.remove();

                // تحديث إجمالي السلة
                document.getElementById('total').textContent = formatPrice(data.cart_total);
                document.getElementById('subtotal').textContent = formatPrice(data.cart_total);

                // إذا أصبحت السلة فارغة
                if (data.cart_count === 0) {
                    location.reload();
                }
            }, 300);

            showAlert('تم حذف المنتج من السلة بنجاح');
        } else {
            cartItem.style.opacity = '1';
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        cartItem.style.opacity = '1';
        showAlert('حدث خطأ أثناء حذف المنتج', 'danger');
    }
}

// منع جميع أنواع التعديل على input الكمية
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        
        // منع استخدام أسهم الكيبورد
        input.addEventListener('keydown', function(e) {
            // منع جميع المفاتيح ما عدا Tab للانتقال
            if (e.key !== 'Tab') {
                e.preventDefault();
            }
        });
        
        // منع الكتابة
        input.addEventListener('keypress', function(e) {
            e.preventDefault();
        });
        
        // منع النسخ واللصق
        input.addEventListener('paste', function(e) {
            e.preventDefault();
        });
        
        // منع السحب والإفلات
        input.addEventListener('drop', function(e) {
            e.preventDefault();
        });
        
        // منع تغيير القيمة مباشرة
        input.addEventListener('input', function(e) {
            // إعادة القيمة للقيمة الأصلية في حالة تغييرها
            const originalValue = this.getAttribute('value');
            if (this.value !== originalValue) {
                this.value = originalValue;
            }
        });
        
        // منع التحديد
        input.addEventListener('focus', function() {
            this.blur();
        });
    });
});


</script>
@endsection
