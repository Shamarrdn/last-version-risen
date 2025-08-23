<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Checkout') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/customer/checkout.css') }}">
    <style>

        .discount-applied {
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.2);
            position: relative;
        }

        .no-discount {
            opacity: 0.7;
        }

        .discount-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 5px;
        }

        .partial-discount-message {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 14px;
        }

        .info-message {
            color: #0dcaf0;
            padding: 5px 0;
            margin: 5px 0;
        }

        .product-attributes {
            display: flex;
            gap: 8px;
            margin: 6px 0;
            flex-wrap: wrap;
        }

        .product-attributes span {
            background-color: #f8f9fa;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            color: #495057;
            border: 1px solid #dee2e6;
            font-weight: 500;
            white-space: nowrap;
        }

        .product-color {
            background-color: #e3f2fd !important;
            border-color: #90caf9 !important;
            color: #1565c0 !important;
        }

        .product-size {
            background-color: #fff8e1 !important;
            border-color: #ffcc02 !important;
            color: #ef6c00 !important;
        }

        .product-details h4 {
            margin-bottom: 8px;
        }

        .product-details p {
            margin-bottom: 4px;
        }
    </style>
</head>
<body class="checkout-container">

    <header class="checkout-header">
        <div class="container">
            <div class="header-content">
                <h2>{{ __('إتمام الطلب') }}</h2>
                <a href="{{ route('cart.index') }}" class="back-to-cart-btn">
                    العودة إلى السلة
                </a>
            </div>
        </div>
    </header>

    <div class="checkout-content">
        <div class="container">
            <!-- Toast Notification -->
            <div id="toast-notification" class="toast-notification" style="display: none;">
                <span id="toast-message"></span>
                <button type="button" class="toast-close" onclick="document.getElementById('toast-notification').style.display='none';">&times;</button>
            </div>
            <div class="checkout-wrapper">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    @if ($errors->any())
                    <script>
                        window.addEventListener('DOMContentLoaded', function() {
                            var toast = document.getElementById('toast-notification');
                            var msg = document.getElementById('toast-message');
                            msg.innerHTML = `{!! implode('<br>', $errors->all()) !!}`;
                            toast.style.display = 'block';
                            setTimeout(function() { toast.style.display = 'none'; }, 7000);
                        });
                    </script>
                    @endif

                    <div class="checkout-grid">
                        <!-- Order Summary -->
                        <div class="order-summary">
                            <h3>ملخص الطلب</h3>
                            <div class="order-items">
                                @if(Auth::check() && isset($cart))
                                    @foreach($cart->items as $item)
                                    <div class="order-item" data-product-id="{{ $item->product_id }}">
                                        <div class="product-info">
                                            <div class="product-image">
                                                <x-product-image :product="$item->product" size="16" />
                                            </div>
                                            <div class="product-details">
                                                <h4>{{ $item->product->name }}</h4>
                                                @if($item->color || $item->size || $item->colorOption || $item->sizeOption)
                                                <div class="product-attributes">
                                                    @if($item->colorOption)
                                                    <span class="product-color">اللون: {{ $item->colorOption->name }}</span>
                                                    @elseif($item->color)
                                                    <span class="product-color">اللون: {{ $item->color }}</span>
                                                    @endif
                                                    
                                                    @if($item->sizeOption)
                                                    <span class="product-size">المقاس: {{ $item->sizeOption->name }}</span>
                                                    @elseif($item->size)
                                                    <span class="product-size">المقاس: {{ $item->size }}</span>
                                                    @endif
                                                </div>
                                                @endif
                                                <p>الكمية: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                        <p class="item-price">{{ $item->unit_price }} ريال × {{ $item->quantity }}</p>
                                        <p class="item-subtotal">الإجمالي: {{ $item->subtotal }} ريال</p>
                                    </div>
                                    @endforeach
                                @else
                                    @foreach($products as $product)
                                    <div class="order-item" data-product-id="{{ $product->id }}">
                                        <div class="product-info">
                                            <div class="product-image">
                                                @if($product->primary_image)
                                                    <img src="{{ Storage::url($product->primary_image->image_path) }}"
                                                        alt="{{ $product->name }}">
                                                @else
                                                    <div class="placeholder-image">
                                                        <svg viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-details">
                                                <h4>{{ $product->name }}</h4>
                                                {{-- Note: Session cart for guests doesn't typically store color/size info --}}
                                                <p>الكمية: {{ $sessionCart[$product->id] }}</p>
                                            </div>
                                        </div>
                                        <p class="item-price">{{ $product->price }} ريال × {{ $sessionCart[$product->id] }}</p>
                                        <p class="item-subtotal">الإجمالي: {{ $product->price * $sessionCart[$product->id] }} ريال</p>
                                    </div>
                                    @endforeach
                                @endif

                                <div class="d-flex justify-content-between">
                                    <h4>الإجمالي الكلي:</h4>
                                    <span class="total-amount">{{ $cart->total_amount }} ريال</span>
                                </div>

                                <!-- Quantity Discounts Section -->
                                @if(isset($quantityDiscounts) && count($quantityDiscounts) > 0)
                                    <div class="quantity-discounts mt-4">
                                        <div class="discount-header mb-3">
                                            <h5 class="discount-title">
                                                <i class="fas fa-tags me-2"></i>
                                                خصومات الكمية
                                            </h5>
                                        </div>

                                        <div class="discount-items">
                                            @foreach($quantityDiscounts as $discount)
                                                <div class="discount-item mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="product-name">{{ $discount['product_name'] }}</span>
                                                            <small class="discount-details d-block">
                                                                {{ $discount['quantity'] }} قطعة -
                                                                خصم {{ $discount['discount_type'] === 'percentage' ? $discount['discount_value'] . '%' : number_format($discount['discount_value'], 2) . ' ريال' }}
                                                            </small>
                                                        </div>
                                                        <span class="discount-amount">-{{ number_format($discount['discount_amount'], 2) }} ريال</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="total-discount">
                                            <div class="d-flex justify-content-between">
                                                <span>إجمالي خصم الكمية:</span>
                                                <span class="total-discount-amount">-{{ number_format($quantityDiscountsTotal, 2) }} ريال</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Coupon Section -->
                                <div class="coupon-section mt-4">
                                    <h4>كود الخصم</h4>
                                    <div class="coupon-input-group d-flex">
                                        <input type="text" name="coupon_code" id="coupon_code" class="form-input"
                                            placeholder="أدخل كود الخصم" value="{{ old('coupon_code', (isset($couponData) ? $couponData['code'] : '')) }}">
                                        <button type="button" id="apply-coupon" class="btn-apply-coupon">تطبيق</button>
                                    </div>
                                    <div id="coupon-message" class="mt-2"></div>
                                    @error('coupon_code')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror

                                    @if(isset($couponData))
                                    <div class="coupon-applied">
                                        <div class="coupon-details">
                                            <span class="coupon-name">{{ $couponData['name'] }}</span>
                                            <span class="coupon-discount">- {{ number_format($couponData['discount_amount'], 2) }} ريال</span>
                                        </div>
                                        @if($couponData['is_partial'])
                                        <div class="partial-discount-message mt-2">
                                            <small class="text-info">{{ $couponData['partial_discount_message'] }}</small>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    <div class="d-flex justify-content-between mt-3">
                                        <h4>المبلغ النهائي:</h4>
                                        <span class="final-amount">
                                            {{ $finalAmount }} ريال
                                        </span>
                                    </div>

                                    @if(isset($discountMessage) && !empty($discountMessage))
                                    <div class="discount-message mt-3">
                                        <p class="info-message">{{ $discountMessage }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="shipping-info">
                            <h3>معلومات الشحن</h3>
                            <div class="form-groups">
                                <div class="form-group">
                                    <label for="shipping_address" class="form-label">
                                        عنوان الشحن
                                    </label>
                                    <textarea name="shipping_address" id="shipping_address" rows="4"
                                        class="form-input"
                                        placeholder="أدخل عنوان الشحن الكامل"
                                        required>{{ old('shipping_address', Auth::user()->address ?? '') }}</textarea>
                                    @error('shipping_address')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        رقم الهاتف
                                    </label>
                                    <input type="tel" name="phone" id="phone"
                                        value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                        class="form-input"
                                        placeholder="05xxxxxxxx"
                                        required>
                                    @error('phone')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Method -->
                                <div class="form-group">
                                    <label class="form-label">
                                        طريقة الدفع
                                    </label>
                                    <div class="payment-method">
                                        <div class="payment-info">
                                            <span class="payment-label">الدفع عند الاستلام</span>
                                            <input type="hidden" name="payment_method" value="cash">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes" class="form-label">
                                        ملاحظات الطلب (اختياري)
                                    </label>
                                    <textarea name="notes" id="notes" rows="4"
                                        class="form-input"
                                        placeholder="أي ملاحظات إضافية للطلب">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- إضافة حقل الموافقة على السياسة -->
                                <div class="form-group">
                                    <div class="policy-agreement">
                                        <input type="checkbox"
                                               name="policy_agreement"
                                               id="policy_agreement"
                                               class="form-checkbox"
                                               {{ old('policy_agreement') ? 'checked' : '' }}
                                               required>
                                        <label for="policy_agreement" class="form-label">
                                            أوافق على <a href="{{ route('policy') }}" target="_blank">سياسة الشركة وشروط الخدمة</a>
                                        </label>
                                    </div>
                                    @error('policy_agreement')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Appointment ID field -->
                    @if(session('appointment_id'))
                    <input type="hidden" name="appointment_id" value="{{ session('appointment_id') }}">
                    @endif

                    <div class="checkout-actions">
                        <button type="submit" class="place-order-btn">
                            تأكيد الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('apply-coupon').addEventListener('click', function() {
            const couponCode = document.getElementById('coupon_code').value;
            const couponMessage = document.getElementById('coupon-message');

            if (!couponCode) {
                couponMessage.innerHTML = '<p class="error-message">يرجى إدخال كود الخصم</p>';
                return;
            }

            fetch('/checkout/apply-coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ coupon_code: couponCode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    couponMessage.innerHTML = '<p class="success-message">' + data.message + '</p>';

                    document.getElementById('coupon_code').value = data.coupon_code;

                    // تحديث السعر النهائي مباشرة في الواجهة
                    const finalAmountElement = document.querySelector('.final-amount');
                    if (finalAmountElement) {
                        finalAmountElement.textContent = data.final_amount + ' ريال';
                    }

                    // إضافة أو تحديث رسالة الخصم
                    const discountMessageDiv = document.querySelector('.discount-message');
                    if (discountMessageDiv) {
                        discountMessageDiv.innerHTML = '<p class="info-message">' + data.message + '</p>';
                    } else {
                        const newDiscountMessageDiv = document.createElement('div');
                        newDiscountMessageDiv.className = 'discount-message mt-3';
                        newDiscountMessageDiv.innerHTML = '<p class="info-message">' + data.message + '</p>';

                        // إضافة العنصر الجديد بعد عنصر المبلغ النهائي
                        const finalAmountContainer = document.querySelector('.d-flex.justify-content-between.mt-3');
                        if (finalAmountContainer) {
                            finalAmountContainer.parentNode.insertBefore(newDiscountMessageDiv, finalAmountContainer.nextSibling);
                        }
                    }

                    if (data.partial_discount) {
                        couponMessage.innerHTML += '<p class="info-message">' + data.partial_discount_message + '</p>';

                        const orderItems = document.querySelectorAll('.order-item');
                        const validProductIds = data.valid_product_ids;

                        orderItems.forEach(item => {
                            const productId = parseInt(item.getAttribute('data-product-id'));

                            if (validProductIds.includes(productId)) {
                                item.classList.add('discount-applied');
                                item.querySelector('.product-details').innerHTML += '<span class="discount-badge">مشمول بالخصم</span>';
                            } else {
                                item.classList.add('no-discount');
                            }
                        });
                    }
                } else {
                    couponMessage.innerHTML = '<p class="error-message">' + data.message + '</p>';
                }
            })
            .catch(error => {
                couponMessage.innerHTML = '<p class="error-message">حدث خطأ أثناء تطبيق الكوبون</p>';
            });
        });

        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            var submitBtn = document.querySelector('.place-order-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'جاري المعالجة...';
            }
        });
    </script>
</body>
</html>
