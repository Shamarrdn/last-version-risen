<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(__('Checkout')); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/checkout.css')); ?>">
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
    </style>
</head>
<body class="checkout-container">

    <header class="checkout-header">
        <div class="container">
            <div class="header-content">
                <h2><?php echo e(__('إتمام الطلب')); ?></h2>
                <a href="<?php echo e(route('cart.index')); ?>" class="back-to-cart-btn">
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
                <form action="<?php echo e(route('checkout.store')); ?>" method="POST" id="checkout-form">
                    <?php echo csrf_field(); ?>

                    <?php if($errors->any()): ?>
                    <script>
                        window.addEventListener('DOMContentLoaded', function() {
                            var toast = document.getElementById('toast-notification');
                            var msg = document.getElementById('toast-message');
                            msg.innerHTML = `<?php echo implode('<br>', $errors->all()); ?>`;
                            toast.style.display = 'block';
                            setTimeout(function() { toast.style.display = 'none'; }, 7000);
                        });
                    </script>
                    <?php endif; ?>

                    <div class="checkout-grid">
                        <!-- Order Summary -->
                        <div class="order-summary">
                            <h3>ملخص الطلب</h3>
                            <div class="order-items">
                                <?php if(Auth::check() && isset($cart)): ?>
                                    <?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="order-item" data-product-id="<?php echo e($item->product_id); ?>">
                                        <div class="product-info">
                                            <div class="product-image">
                                                <?php if (isset($component)) { $__componentOriginala58dde406db9207f2e2c58e1c4a3d690 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-image','data' => ['product' => $item->product,'size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->product),'size' => '16']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $attributes = $__attributesOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__attributesOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690)): ?>
<?php $component = $__componentOriginala58dde406db9207f2e2c58e1c4a3d690; ?>
<?php unset($__componentOriginala58dde406db9207f2e2c58e1c4a3d690); ?>
<?php endif; ?>
                                            </div>
                                            <div class="product-details">
                                                <h4><?php echo e($item->product->name); ?></h4>
                                                <p>الكمية: <?php echo e($item->quantity); ?></p>
                                            </div>
                                        </div>
                                        <p class="item-price"><?php echo e($item->unit_price); ?> ريال × <?php echo e($item->quantity); ?></p>
                                        <p class="item-subtotal">الإجمالي: <?php echo e($item->subtotal); ?> ريال</p>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="order-item" data-product-id="<?php echo e($product->id); ?>">
                                        <div class="product-info">
                                            <div class="product-image">
                                                <?php if($product->primary_image): ?>
                                                    <img src="<?php echo e(Storage::url($product->primary_image->image_path)); ?>"
                                                        alt="<?php echo e($product->name); ?>">
                                                <?php else: ?>
                                                    <div class="placeholder-image">
                                                        <svg viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="product-details">
                                                <h4><?php echo e($product->name); ?></h4>
                                                <p>الكمية: <?php echo e($sessionCart[$product->id]); ?></p>
                                            </div>
                                        </div>
                                        <p class="item-price"><?php echo e($product->price); ?> ريال × <?php echo e($sessionCart[$product->id]); ?></p>
                                        <p class="item-subtotal">الإجمالي: <?php echo e($product->price * $sessionCart[$product->id]); ?> ريال</p>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between">
                                    <h4>الإجمالي الكلي:</h4>
                                    <span class="total-amount"><?php echo e($cart->total_amount); ?> ريال</span>
                                </div>

                                <!-- Quantity Discounts Section -->
                                <?php if(isset($quantityDiscounts) && count($quantityDiscounts) > 0): ?>
                                    <div class="quantity-discounts mt-4">
                                        <div class="discount-header mb-3">
                                            <h5 class="discount-title">
                                                <i class="fas fa-tags me-2"></i>
                                                خصومات الكمية
                                            </h5>
                                        </div>

                                        <div class="discount-items">
                                            <?php $__currentLoopData = $quantityDiscounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="discount-item mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="product-name"><?php echo e($discount['product_name']); ?></span>
                                                            <small class="discount-details d-block">
                                                                <?php echo e($discount['quantity']); ?> قطعة -
                                                                خصم <?php echo e($discount['discount_type'] === 'percentage' ? $discount['discount_value'] . '%' : number_format($discount['discount_value'], 2) . ' ريال'); ?>

                                                            </small>
                                                        </div>
                                                        <span class="discount-amount">-<?php echo e(number_format($discount['discount_amount'], 2)); ?> ريال</span>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>

                                        <div class="total-discount">
                                            <div class="d-flex justify-content-between">
                                                <span>إجمالي خصم الكمية:</span>
                                                <span class="total-discount-amount">-<?php echo e(number_format($quantityDiscountsTotal, 2)); ?> ريال</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Coupon Section -->
                                <div class="coupon-section mt-4">
                                    <h4>كود الخصم</h4>
                                    <div class="coupon-input-group d-flex">
                                        <input type="text" name="coupon_code" id="coupon_code" class="form-input"
                                            placeholder="أدخل كود الخصم" value="<?php echo e(old('coupon_code', (isset($couponData) ? $couponData['code'] : ''))); ?>">
                                        <button type="button" id="apply-coupon" class="btn-apply-coupon">تطبيق</button>
                                    </div>
                                    <div id="coupon-message" class="mt-2"></div>
                                    <?php $__errorArgs = ['coupon_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-message"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                    <?php if(isset($couponData)): ?>
                                    <div class="coupon-applied">
                                        <div class="coupon-details">
                                            <span class="coupon-name"><?php echo e($couponData['name']); ?></span>
                                            <span class="coupon-discount">- <?php echo e(number_format($couponData['discount_amount'], 2)); ?> ريال</span>
                                        </div>
                                        <?php if($couponData['is_partial']): ?>
                                        <div class="partial-discount-message mt-2">
                                            <small class="text-info"><?php echo e($couponData['partial_discount_message']); ?></small>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between mt-3">
                                        <h4>المبلغ النهائي:</h4>
                                        <span class="final-amount">
                                            <?php echo e($finalAmount); ?> ريال
                                        </span>
                                    </div>

                                    <?php if(isset($discountMessage) && !empty($discountMessage)): ?>
                                    <div class="discount-message mt-3">
                                        <p class="info-message"><?php echo e($discountMessage); ?></p>
                                    </div>
                                    <?php endif; ?>
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
                                        required><?php echo e(old('shipping_address', Auth::user()->address ?? '')); ?></textarea>
                                    <?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-message"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        رقم الهاتف
                                    </label>
                                    <input type="tel" name="phone" id="phone"
                                        value="<?php echo e(old('phone', Auth::user()->phone ?? '')); ?>"
                                        class="form-input"
                                        placeholder="05xxxxxxxx"
                                        required>
                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-message"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                        placeholder="أي ملاحظات إضافية للطلب"><?php echo e(old('notes')); ?></textarea>
                                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-message"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- إضافة حقل الموافقة على السياسة -->
                                <div class="form-group">
                                    <div class="policy-agreement">
                                        <input type="checkbox"
                                               name="policy_agreement"
                                               id="policy_agreement"
                                               class="form-checkbox"
                                               <?php echo e(old('policy_agreement') ? 'checked' : ''); ?>

                                               required>
                                        <label for="policy_agreement" class="form-label">
                                            أوافق على <a href="<?php echo e(route('policy')); ?>" target="_blank">سياسة الشركة وشروط الخدمة</a>
                                        </label>
                                    </div>
                                    <?php $__errorArgs = ['policy_agreement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error-message"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Appointment ID field -->
                    <?php if(session('appointment_id')): ?>
                    <input type="hidden" name="appointment_id" value="<?php echo e(session('appointment_id')); ?>">
                    <?php endif; ?>

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
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
<?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/checkout/index.blade.php ENDPATH**/ ?>