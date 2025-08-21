<?php $__env->startSection('title', $product->name . ' - risen born in ksa'); ?>

<?php $__env->startSection('content'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($product->name); ?> - risen born in ksa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/clothes-platform/style.css')); ?>?v=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/products-show.css')); ?>?v=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/products.css')); ?>?v=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/custom-black-theme.css')); ?>?v=<?php echo e(time()); ?>">


    <style>
        .quantity-discounts {
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            padding: 16px;
            background-color: #f9f9f9;
        }
        .quantity-discounts h5 {
            margin-bottom: 15px;
            color: #333;
        }
        .quantity-discounts table {
            border-radius: 4px;
            overflow: hidden;
        }
        .quantity-discounts th, .quantity-discounts td {
            text-align: center;
            vertical-align: middle;
        }
        .quantity-discounts .badge {
            font-size: 0.9rem;
            padding: 5px 8px;
        }
        .table-success {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }
        .thumbnail-wrapper {
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .thumbnail-wrapper.active {
            border-color: #000000;
        }
        .color-preview {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
        }
        
        /* Styles for new inventory system */
        .color-item {
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .color-item:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.15);
        }
        
        .color-item.selected {
            border-color: #007bff;
            background-color: #007bff;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .color-item.unavailable {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .size-option {
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            background: white;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .size-option:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.15);
        }
        
        .size-option.selected {
            border-color: #007bff;
            background-color: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.25);
        }
        
        .size-option.unavailable {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        
        .stock-indicator {
            font-size: 0.85rem;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            margin-left: 8px;
        }
        
        .stock-high {
            background-color: #d4edda;
            color: #155724;
        }
        
        .stock-medium {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .stock-low {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .stock-out {
            background-color: #f8f9fa;
            color: #6c757d;
        }
            transition: all 0.3s ease;
            font-weight: 500;
            min-width: 80px;
            text-align: center;
        }
        
        .size-option.available:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }
        
        .size-option.available.selected {
            border-color: #007bff;
            background-color: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .size-option.unavailable {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }
        
        .size-option.unavailable:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }
        
        /* تحسينات إضافية */
        .section-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .color-name {
            font-weight: 500;
        }
        
        .badge {
            font-size: 0.8rem;
        }
        
        .size-option .fw-bold {
            font-size: 1rem;
        }
        
        .text-muted {
            font-size: 0.85rem;
        }
        
        #availableColorsForSize {
            background-color: #f8f9ff;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid rgba(0, 123, 255, 0.1);
            margin-top: 20px;
        }
        
        #availableColorsForSize h6 {
            color: #007bff;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .stock-status-badge {
            font-size: 0.85rem;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }
        
        .stock-available {
            background-color: #d4edda;
            color: #155724;
        }
        
        .stock-low {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .stock-out {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .variant-price {
            font-weight: 600;
            color: #007bff;
            font-size: 1rem;
        }
        
        .size-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }
        
        @media (max-width: 768px) {
            .size-grid {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 8px;
            }
            
            .size-option {
                padding: 12px 15px;
                min-width: 100px;
            }
            
            .color-item {
                padding: 12px;
                margin-bottom: 8px;
            }
            
            #availableColorsForSize {
                padding: 15px;
            }
            
            .product-price .amount {
                font-size: 1.5rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .size-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .size-option {
                padding: 10px;
                min-width: auto;
            }
            
            .color-item {
                padding: 10px;
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }
            
            .color-item .d-flex {
                flex-direction: column;
                gap: 8px;
            }
        }

        /* أنماط أساسية ونظيفة */

        .size-option {
            transition: all 0.3s ease;
            border: 2px solid #e1e5e9;
            background: white;
        }

        .size-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.15);
            border-color: #007bff;
        }

        .size-option.selected {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-color: #007bff;
            transform: translateY(-3px);
        }

        .color-preview {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .color-preview:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* تصميم المقاسات الجديد - Radio Buttons مبسط */
        .size-options-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .size-radio-wrapper {
            position: relative;
        }

        .size-radio-wrapper input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .size-radio-label {
            display: block;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            background: #fff;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .size-radio-label:hover:not(.disabled) {
            border-color: #007bff;
            background-color: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }

        .size-radio-wrapper input[type="radio"]:checked + .size-radio-label {
            border-color: #007bff;
            background-color: #007bff;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.3);
        }

        .size-radio-wrapper input[type="radio"]:checked + .size-radio-label small {
            color: #fff !important;
        }

        .size-radio-label.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .size-radio-label.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* تصميم الألوان الجديد - Radio Buttons */
        .color-options-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .color-radio-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .color-radio-wrapper input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .color-visual {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid #e0e0e0;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            background-size: cover;
            background-position: center;
        }

        .color-visual::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 12px;
            height: 12px;
            background: #fff;
            border-radius: 50%;
            opacity: 0;
            transition: all 0.2s ease;
        }

        .color-radio-wrapper:hover .color-visual:not(.disabled) {
            transform: scale(1.1);
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .color-radio-wrapper input[type="radio"]:checked + .color-visual,
        .color-radio-wrapper input[type="radio"]:checked ~ .color-visual {
            border-color: #007bff;
            transform: scale(1.1);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
        }

        .color-radio-wrapper input[type="radio"]:checked + .color-visual::after,
        .color-radio-wrapper input[type="radio"]:checked ~ .color-visual::after {
            opacity: 1;
        }

        .color-visual.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            filter: grayscale(1);
            position: relative;
        }

        .color-visual.disabled::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 2px,
                rgba(255, 255, 255, 0.5) 2px,
                rgba(255, 255, 255, 0.5) 4px
            );
            border-radius: 50%;
        }

        .color-visual.disabled::after {
            content: '✕';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #dc3545;
            font-weight: bold;
            font-size: 12px;
            opacity: 1;
        }

        /* تعطيل التفاعل مع الألوان المعطلة */
        .color-radio-wrapper input[type="radio"]:disabled + .color-visual,
        .color-radio-wrapper input[type="radio"]:disabled ~ .color-visual {
            pointer-events: none;
        }

        .color-radio-wrapper input[type="radio"]:disabled + .color-visual:hover,
        .color-radio-wrapper input[type="radio"]:disabled ~ .color-visual:hover {
            transform: none;
            border-color: #e0e0e0;
            box-shadow: none;
        }

        .color-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .color-name {
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .color-stock {
            font-size: 12px;
        }

        .color-price {
            font-weight: 600;
            color: #007bff;
            font-size: 13px;
        }

        /* تحسين العناوين */
        .section-title {
            color: #333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            color: #007bff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .size-options-container {
                grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
                gap: 8px;
            }
            
            .size-radio-label {
                padding: 10px 12px;
                min-height: 50px;
                font-size: 13px;
            }
            
            .color-visual {
                width: 36px;
                height: 36px;
            }
            
            .color-options-container {
                gap: 10px;
            }
        }

        @media (max-width: 576px) {
            .size-options-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            .color-radio-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 4px;
            }
            
            .color-info {
                align-items: center;
            }
        }
    </style>
</head>
<body class="<?php echo e(auth()->check() ? 'user-logged-in' : ''); ?>">
    <?php echo $__env->make('parts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Fixed Buttons Group -->
    <div class="fixed-buttons-group">
        <button class="fixed-cart-btn" id="fixedCartBtn">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                0
            </span>
        </button>
        <?php if(auth()->guard()->check()): ?>
        <a href="/dashboard" class="fixed-dashboard-btn">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
        <?php endif; ?>
    </div>


    <!-- Cart Overlay -->
    <div class="cart-overlay"></div>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>سلة التسوق</h3>
            <button class="close-cart" id="closeCart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be dynamically added here -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>الإجمالي:</span>
                <span id="cartTotal">0 ر.س</span>
            </div>
            <a href="<?php echo e(route('checkout.index')); ?>" class="checkout-btn">
                <i class="fas fa-shopping-cart ml-2"></i>
                إتمام الشراء
            </a>
        </div>
    </div>

    <!-- Hidden Cart Toggle Button -->
    <button id="cartToggle" style="display: none;"></button>

    <!-- Main Content -->
    <div class="container py-5" style="margin-top: 60px;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="/products">المنتجات</a></li>
                <li class="breadcrumb-item"><a href="/products?category=<?php echo e($product->category->slug); ?>"><?php echo e($product->category->name); ?></a></li>
                <li class="breadcrumb-item active"><?php echo e($product->name); ?></li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Product Images -->
            <div class="col-md-6">
                <div class="product-gallery card">
                    <div class="card-body">
                        <?php if($product->images->count() > 0): ?>
                            <div class="main-image-wrapper mb-3">
                                <img src="<?php echo e(url('storage/' . $product->primary_image->image_path)); ?>"
                                    alt="<?php echo e($product->name); ?>"
                                    class="main-product-image"
                                    id="mainImage">
                            </div>
                            <?php if($product->images->count() > 1): ?>
                                <div class="image-thumbnails">
                                    <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="thumbnail-wrapper <?php echo e($image->is_primary ? 'active' : ''); ?>"
                                            onclick="updateMainImage('<?php echo e(url('storage/' . $image->image_path)); ?>', this)">
                                            <img src="<?php echo e(url('storage/' . $image->image_path)); ?>"
                                                alt="Product thumbnail"
                                                class="thumbnail-image">
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-image"></i>
                                <p>لا توجد صور متاحة</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <div class="product-info">
                    <h1 class="product-title"><?php echo e($product->name); ?></h1>

                    <div class="product-category d-flex flex-wrap gap-1 align-items-center mb-3">
                        <a href="<?php echo e(route('products.index', ['category' => $product->category->slug])); ?>" class="text-decoration-none">
                            <span class="badge rounded-pill bg-primary"><?php echo e($product->category->name); ?></span>
                        </a>
                        <?php if($product->categories->isNotEmpty()): ?>
                            <?php $__currentLoopData = $product->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additionalCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($additionalCategory->id != $product->category_id): ?>
                                    <a href="<?php echo e(route('products.index', ['category' => $additionalCategory->slug])); ?>" class="text-decoration-none">
                                        <span class="badge rounded-pill bg-light text-dark border"><?php echo e($additionalCategory->name); ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Available Coupons Section -->
                    <?php
                        $availableCoupons = $product->getAvailableCoupons();
                    ?>

                    <?php if($availableCoupons->isNotEmpty()): ?>
                        <div class="available-coupons mb-4">
                            <h5><i class="fas fa-tags"></i> كوبونات خصم متاحة</h5>
                            <div class="coupon-list">
                                <?php $__currentLoopData = $availableCoupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="coupon-item">
                                        <div class="coupon-content">
                                            <div class="coupon-code"><?php echo e($coupon->code); ?></div>
                                            <div class="coupon-value">
                                                <?php if($coupon->type === 'percentage'): ?>
                                                    <span class="badge">خصم <?php echo e($coupon->value); ?>%</span>
                                                <?php else: ?>
                                                    <span class="badge">خصم <?php echo e($coupon->value); ?> ر.س</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="copy-btn-wrapper">
                                            <button class="copy-btn" data-code="<?php echo e($coupon->code); ?>">
                                                <i class="fas fa-copy"></i> نسخ
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <small>
                                <i class="fas fa-info-circle"></i>
                                يمكنك استخدام هذه الكوبونات عند إتمام الطلب
                            </small>
                        </div>
                    <?php endif; ?>

                    <!-- Quantity Discounts Section -->
                    <?php if(isset($quantityDiscounts) && $quantityDiscounts->isNotEmpty()): ?>
                        <div class="quantity-discounts mb-4">
                            <h5><i class="fas fa-percent"></i> خصومات الكميات</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الكمية</th>
                                            <th>الخصم</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $quantityDiscounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <?php if($discount->max_quantity): ?>
                                                        <?php echo e($discount->min_quantity); ?> - <?php echo e($discount->max_quantity); ?>

                                                    <?php else: ?>
                                                        <?php echo e($discount->min_quantity); ?>+
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($discount->type === 'percentage'): ?>
                                                        <span class="badge bg-success"><?php echo e($discount->value); ?>%</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success"><?php echo e($discount->value); ?> ر.س</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <small>
                                <i class="fas fa-info-circle"></i>
                                يتم تطبيق خصم الكمية تلقائياً عند إضافة الكمية المطلوبة للسلة
                            </small>
                        </div>
                    <?php endif; ?>

                    <!-- Product Price -->
                    <div class="price-container mb-3">
                        <div class="product-price d-flex align-items-center gap-2">
                            <?php
                                $actuallyAvailable = isset($totalAvailableStock) && $totalAvailableStock > 0;
                                $showPrice = $actuallyAvailable && isset($minPrice) && isset($maxPrice) && $minPrice > 0;
                            ?>
                            <?php if($showPrice): ?>
                                <?php if($minPrice == $maxPrice): ?>
                                    <span class="amount fs-3 fw-bold text-primary"><?php echo e(number_format($minPrice, 2)); ?></span>
                                    <span class="currency fs-5 text-muted">ر.س</span>
                                <?php else: ?>
                                    <span class="amount fs-3 fw-bold text-primary"><?php echo e(number_format($minPrice, 2)); ?> - <?php echo e(number_format($maxPrice, 2)); ?></span>
                                    <span class="currency fs-5 text-muted">ر.س</span>
                                <?php endif; ?>
                            <?php elseif($actuallyAvailable): ?>
                                <?php
                                    $fallbackPrice = $product->base_price ?? $product->price ?? 0;
                                ?>
                                <?php if($fallbackPrice > 0): ?>
                                    <span class="amount fs-3 fw-bold text-primary"><?php echo e(number_format($fallbackPrice, 2)); ?></span>
                                    <span class="currency fs-5 text-muted">ر.س</span>
                                    <small class="text-muted d-block">السعر الأساسي</small>
                                <?php else: ?>
                                    <span class="amount fs-3 fw-bold text-warning">اتصل للاستفسار عن السعر</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="amount fs-3 fw-bold text-danger">غير متوفر</span>
                            <?php endif; ?>
                        </div>
                        <?php if(isset($availableInventoryData) && $availableInventoryData->isNotEmpty()): ?>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                السعر يتغير حسب المقاس واللون المختار
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="stock-info mb-4">
                        <?php
                            $actuallyAvailable = isset($totalAvailableStock) && $totalAvailableStock > 0;
                        ?>
                        <span class="stock-badge <?php echo e($actuallyAvailable ? 'in-stock' : 'out-of-stock'); ?>" id="productStockBadge">
                            <i class="fas <?php echo e($actuallyAvailable ? 'fa-check-circle' : 'fa-times-circle'); ?> me-1"></i>
                            <?php echo e($actuallyAvailable ? 'متوفر' : 'غير متوفر'); ?>

                        </span>
                        
                        
                    </div>



                    <div class="product-description mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>
                            وصف المنتج
                        </h5>
                        <p><?php echo e($product->description); ?></p>
                    </div>

                    <!-- Product Details Section -->
                    <?php if($product->details && count($product->details) > 0): ?>
                    <div class="product-details-section mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-list-ul me-2"></i>
                            تفاصيل المنتج
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <?php $__currentLoopData = $product->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <th class="bg-light" style="width: 40%"><?php echo e($key); ?></th>
                                        <td><?php echo e($value); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Product Features Guide -->
                    <?php if(!empty($availableFeatures)): ?>
                    <div class="features-guide mb-4">
                        <div class="alert alert-info">
                            <h6 class="alert-heading mb-3">
                                <i class="fas fa-lightbulb me-2"></i>
                                ميزات الطلب المتاحة
                            </h6>
                            <ul class="features-list mb-0">
                                <?php if($availableFeatures['allow_custom_color']): ?>
                                <li class="mb-2">
                                    <i class="fas fa-palette me-2"></i>
                                    يمكنك تحديد لون مخصص
                                </li>
                                <?php endif; ?>

                                <?php if($availableFeatures['allow_custom_size']): ?>
                                <li class="mb-2">
                                    <i class="fas fa-ruler me-2"></i>
                                    يمكنك تحديد مقاس مخصص
                                </li>
                                <?php endif; ?>

                                <?php if(isset($availableFeatures['colors']) && !empty($availableFeatures['colors'])): ?>
                                <li class="mb-2">
                                    <i class="fas fa-palette me-2"></i>
                                    <?php echo e(count($availableFeatures['colors'])); ?> لون متاح للاختيار
                                </li>
                                <?php endif; ?>

                                <?php if(isset($availableFeatures['sizes']) && !empty($availableFeatures['sizes'])): ?>
                                <li class="mb-2">
                                    <i class="fas fa-ruler-combined me-2"></i>
                                    <?php echo e(count($availableFeatures['sizes'])); ?> مقاس متاح للاختيار
                                </li>
                                <?php endif; ?>

                                <?php if($availableFeatures['has_discount']): ?>
                                <li class="mb-2">
                                    <i class="fas fa-tags me-2"></i>
                                    خصومات متاحة على هذا المنتج
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- اختيار المقاس -->
                                <?php if(isset($sizeColorMatrix) && !empty($sizeColorMatrix)): ?>
                        <div class="size-selection mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-ruler me-2"></i>
                                اختر المقاس
                            </h5>
                            <div class="size-options-container">
                                    <?php $__currentLoopData = $sizeColorMatrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sizeId => $sizeData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $totalStockForSize = collect($sizeData['colors'] ?? [])->sum('available_stock');
                                            $isAvailable = $totalStockForSize > 0;
                                        $sizeName = (isset($sizeData['size']) && $sizeData['size']) ? $sizeData['size']->name : "مقاس {$sizeId}";
                                        ?>
                                    <?php if(isset($sizeData['size']) && $sizeData['size']): ?>
                                        <div class="size-radio-wrapper">
                                            <input type="radio" 
                                                   id="size_<?php echo e($sizeId); ?>" 
                                                   name="product_size" 
                                                   value="<?php echo e($sizeId); ?>"
                                                   data-size-name="<?php echo e($sizeName); ?>"
                                                   data-colors="<?php echo e(json_encode($sizeData['colors'] ?? [])); ?>"
                                                   <?php echo e(!$isAvailable ? 'disabled' : ''); ?>

                                                   onchange="handleSizeSelection(this)">
                                            <label for="size_<?php echo e($sizeId); ?>" 
                                                   class="size-radio-label <?php echo e(!$isAvailable ? 'disabled' : ''); ?>">
                                                <?php echo e($sizeName); ?>

                                                <?php if(!$isAvailable): ?>
                                                    <small class="text-muted d-block">(غير متوفر)</small>
                                                <?php else: ?>
                                                    <small class="text-success d-block">(<?php echo e($totalStockForSize); ?> قطعة)</small>
                                                <?php endif; ?>
                                        </label>
                                        </div>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- اختيار اللون - يظهر بعد اختيار المقاس -->
                    <div id="colorSelection" class="color-selection mb-4" style="display: none;">
                        <h5 class="section-title">
                            <i class="fas fa-palette me-2"></i>
                            اختر اللون
                        </h5>
                        <div class="color-options-container" id="colorOptionsContainer">
                            <!-- سيتم ملء الألوان هنا ديناميكياً -->
                        </div>
                    </div>

                    <!-- معلومات المخزون المحدث -->
                    <div id="stockInfo" class="alert alert-info mb-4" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="stockMessage">معلومات المخزون</span>
                        </div>
                    </div>

                    <!-- Hidden inputs لحفظ الاختيارات -->
                    <input type="hidden" id="selected_size_id" name="size_id" value="">
                    <input type="hidden" id="selected_color_id" name="color_id" value="">

                    <!-- Custom Size Input -->
                    <?php if($product->enable_custom_size): ?>
                        <div class="custom-size-input mb-4">
                            <h5 class="section-title">
                                <i class="fas fa-ruler me-2"></i>
                                المقاس المطلوب
                            </h5>
                            <div class="input-group">
                                <input type="text" class="form-control" id="customSize" placeholder="اكتب المقاس المطلوب">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Quantity Selector -->
                    <div class="quantity-selector mb-4">
                        <h5 class="section-title">
                            <i class="fas fa-cubes me-2"></i>
                            الكمية
                        </h5>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="decreaseQuantity">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="productQuantity" value="1" min="1">
                            <button class="btn btn-outline-secondary" type="button" id="increaseQuantity">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <?php if(auth()->guard()->check()): ?>
                    <!-- Add to Cart Button -->
                    <button class="btn btn-primary btn-lg w-100 mb-3" onclick="addToCart()">
                        <i class="fas fa-shopping-cart me-2"></i>
                        أضف إلى السلة
                    </button>
                    

                    <?php else: ?>
                        <!-- Login to Order Button -->
                        <button class="btn btn-primary btn-lg w-100 mb-4"
                                data-login-url="<?php echo e(route('login')); ?>"
                                onclick="showLoginPrompt('<?php echo e(route('login')); ?>')"
                                type="button">
                            <i class="fas fa-shopping-cart me-2"></i>
                            تسجيل الدخول للطلب
                        </button>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('parts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Login Prompt Modal -->
    <div class="modal fade" id="loginPromptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تسجيل الدخول مطلوب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <i class="fas fa-user-lock fa-3x mb-3 text-primary"></i>
                    <p>يجب عليك تسجيل الدخول أولاً لتتمكن من طلب المنتج</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        إلغاء
                    </button>
                    <a href="" class="btn btn-primary" id="loginButton">
                        تسجيل الدخول
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this hidden input for product ID -->
    <input type="hidden" id="product-id" value="<?php echo e($product->id); ?>">

    <!-- Add this hidden input for original product price -->
    <input type="hidden" id="original-price" value="<?php echo e($product->min_price_from_inventory); ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo e(asset('assets/js/index.js')); ?>?v=<?php echo e(time()); ?>"></script>

    
    <script>
        // متغيرات عامة
        let selectedColorId = null;
        let selectedSizeId = null;
        let selectedVariantId = null;
        let selectedColorName = '';
        let selectedSizeName = '';
        let currentPrice = <?php echo e($minPrice ?? $product->price ?? 0); ?>;
        
        // متغير لتتبع المخزون المحلي (المخزون - ما تم إضافته للسلة محلياً)
        let localInventoryReduction = {};  // {colorId_sizeId: quantity}
        
        // تحميل المخزون المحلي من Local Storage عند تحميل الصفحة
        function loadLocalInventory() {
            const productId = <?php echo e($product->id); ?>;
            const storageKey = `inventory_reduction_${productId}`;
            const savedData = localStorage.getItem(storageKey);
            
            if (savedData) {
                try {
                    localInventoryReduction = JSON.parse(savedData);
                    console.log('Loaded local inventory from storage:', localInventoryReduction);
                    
                    // تحديث العرض بعد تحميل البيانات
                    setTimeout(() => {
                        refreshInventoryDisplay();
                    }, 500);
                    
                } catch (e) {
                    console.error('Error parsing stored inventory data:', e);
                    localInventoryReduction = {};
                }
            }
        }
        
        // دالة لتحديث عرض المخزون لجميع الألوان بناء على البيانات المحلية
        function refreshInventoryDisplay() {
            console.log('Refreshing inventory display with local data...');
            
            // تحديث عرض الألوان في جميع المقاسات
            document.querySelectorAll('.color-radio-wrapper').forEach(wrapper => {
                const colorInput = wrapper.querySelector('input[name="product_color"]');
                if (colorInput) {
                    const colorId = colorInput.value;
                    const stockDisplay = wrapper.querySelector('.color-stock');
                    const colorVisual = wrapper.querySelector('.color-visual');
                    
                    if (selectedSizeId) {
                        const currentStock = getCurrentStock(colorId, selectedSizeId);
                        
                        if (stockDisplay) {
                            if (currentStock > 0) {
                                stockDisplay.textContent = `${currentStock} قطعة`;
                                stockDisplay.className = 'color-stock text-success';
                            } else {
                                stockDisplay.textContent = 'نفد المخزون';
                                stockDisplay.className = 'color-stock text-danger';
                            }
                        }
                        
                        // تحديث حالة اللون البصرية
                        if (colorVisual) {
                            if (currentStock <= 0) {
                                colorInput.disabled = true;
                                colorVisual.classList.add('disabled');
                            } else {
                                colorInput.disabled = false;
                                colorVisual.classList.remove('disabled');
                            }
                        }
                    }
                }
            });
        }
        
        // حفظ المخزون المحلي في Local Storage
        function saveLocalInventory() {
            const productId = <?php echo e($product->id); ?>;
            const storageKey = `inventory_reduction_${productId}`;
            
            try {
                localStorage.setItem(storageKey, JSON.stringify(localInventoryReduction));
                console.log('Saved local inventory to storage:', localInventoryReduction);
            } catch (e) {
                console.error('Error saving inventory data:', e);
            }
        }
        
        // دالة تحديث الصورة الرئيسية
        function updateMainImage(src, thumbnail) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail-wrapper').forEach(thumb => {
                thumb.classList.remove('active');
            });
            if (thumbnail) {
                thumbnail.classList.add('active');
            }
        }

        // تحديث السعر المعروض
        function updateDisplayedPrice() {
            const priceElement = document.querySelector('.product-price .amount');
            if (priceElement && currentPrice !== null && currentPrice !== undefined) {
                priceElement.textContent = currentPrice.toFixed(2);
            }
        }

        // فلترة المقاسات حسب اللون المختار
        function filterSizesByColor(colorId) {
            if (!colorId) {
                // إظهار جميع المقاسات إذا لم يتم اختيار لون
                const sizesContainer = document.getElementById('sizesContainer');
                const dynamicContainer = document.getElementById('dynamicSizesContainer');
                if (sizesContainer) sizesContainer.style.display = 'flex';
                if (dynamicContainer) dynamicContainer.style.display = 'none';
                return;
            }

            fetch(`/products/<?php echo e($product->slug); ?>/sizes-for-color?color_id=${colorId}`)
                .then(response => response.json())
                .then(sizesData => {
                    if (sizesData.success) {
                        const container = document.getElementById('dynamicSizesContainer');
                        const originalContainer = document.getElementById('sizesContainer');
                        
                        // إخفاء المقاسات الأصلية
                        if (originalContainer) {
                        originalContainer.style.display = 'none';
                        }
                        
                        // ملء المقاسات الديناميكية
                        if (container) {
                        container.innerHTML = '';
                        sizesData.sizes.forEach(size => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'size-option btn';
                            button.setAttribute('data-size-id', size.id);
                            button.setAttribute('data-price', size.price);
                            button.setAttribute('data-variant-id', size.variant_id);
                            button.onclick = function() { selectSize(this); };
                            
                            button.innerHTML = `
                                <div class="fw-bold">${size.name}</div>
                                <span class="ms-2 badge bg-primary">${size.price.toFixed(2)} ر.س</span>
                                <small class="d-block text-muted">المتوفر: ${size.available_stock}</small>
                            `;
                            
                            container.appendChild(button);
                        });
                        container.style.display = 'flex';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching sizes:', error);
                });
        }

        // دالة معالجة اختيار المقاس - مبسطة
        function handleSizeSelection(radio) {
            if (!radio || !radio.checked) return;
            
            const sizeId = radio.value;
            const sizeName = radio.getAttribute('data-size-name') || 'مقاس غير محدد';
            let colorsData = {};
            
            try {
                const colorsJson = radio.getAttribute('data-colors');
                colorsData = colorsJson ? JSON.parse(colorsJson) : {};
            } catch (e) {
                console.error('Error parsing colors data:', e);
                colorsData = {};
            }
            
            console.log('Size selected:', sizeName, 'ID:', sizeId);
            
            // تحديث المتغيرات العامة
            selectedSizeId = sizeId;
            selectedSizeName = sizeName;
            
            // حفظ في hidden input
            const sizeInput = document.getElementById('selected_size_id');
            if (sizeInput) {
                sizeInput.value = sizeId;
            }
            
            // إعادة تعيين اللون المختار
            selectedColorId = null;
            selectedColorName = '';
            const colorInput = document.getElementById('selected_color_id');
            if (colorInput) {
                colorInput.value = '';
            }
            
            // عرض الألوان المتاحة لهذا المقاس
            displayColorsForSize(colorsData);
            
            // إظهار قسم اختيار الألوان
            const colorSection = document.getElementById('colorSelection');
            if (colorSection) {
                colorSection.style.display = 'block';
            }
            
            // تحديث عرض المخزون فوراً
            setTimeout(() => {
                refreshInventoryDisplay();
            }, 100);
        }

        // دالة عرض الألوان للمقاس المختار
        function displayColorsForSize(colorsData) {
            const container = document.getElementById('colorOptionsContainer');
                            container.innerHTML = '';
                            
            if (!colorsData || typeof colorsData !== 'object' || Object.keys(colorsData).length === 0) {
                            container.innerHTML = '<p class="text-muted">لا توجد ألوان متاحة لهذا المقاس</p>';
                return;
            }
            
            // تحويل object إلى array إذا لزم الأمر
            const colorsArray = Array.isArray(colorsData) ? colorsData : Object.values(colorsData);
            
            colorsArray.forEach((colorData, index) => {
                if (!colorData || !colorData.color) return;
                
                const color = colorData.color;
                const colorName = color.name || 'لون غير محدد';
                const colorCode = color.code || '#007bff';
                const colorPrice = colorData.price || 0;
                const originalStock = colorData.available_stock || 0;
                
                // حساب المخزون الفعلي بعد خصم ما تم إضافته محلياً
                const actualStock = getCurrentStock(color.id, selectedSizeId);
                const isAvailable = actualStock > 0;
                
                const wrapper = document.createElement('div');
                wrapper.className = 'color-radio-wrapper';
                
                const colorId = color.id || index;
                
                wrapper.innerHTML = `
                    <input type="radio" 
                           id="color_${colorId}" 
                           name="product_color" 
                           value="${colorId}"
                           data-color-name="${colorName}"
                           data-color-code="${colorCode}"
                           data-price="${colorPrice}"
                           data-stock="${originalStock}"
                           data-actual-stock="${actualStock}"
                           ${!isAvailable ? 'disabled' : ''}>
                    <div class="color-visual ${!isAvailable ? 'disabled' : ''}" 
                         style="background-color: ${colorCode}"
                         title="${colorName} - ${actualStock} متاحة من أصل ${originalStock}"
                         onclick="selectColorByClick('${colorId}')"></div>
                    <div class="color-info">
                        <div class="color-name">${colorName}</div>
                        <div class="color-stock ${isAvailable ? 'text-success' : 'text-danger'}">
                            ${isAvailable ? actualStock + ' قطعة متاحة' : 'نفد المخزون'}
                        </div>
                        <div class="color-price">${parseFloat(colorPrice).toFixed(2)} ر.س</div>
                    </div>
                `;
                
                // إضافة event listener للـ radio button
                const radioInput = wrapper.querySelector('input[type="radio"]');
                if (radioInput) {
                    radioInput.addEventListener('change', function() {
                        if (this.checked) {
                            handleColorSelection(this);
                        }
                    });
                }
                
                container.appendChild(wrapper);
            });
        }

        // دالة لاختيار اللون بالنقر على الدائرة
        function selectColorByClick(colorId) {
            const radioButton = document.getElementById(`color_${colorId}`);
            if (radioButton && !radioButton.disabled) {
                radioButton.checked = true;
                handleColorSelection(radioButton);
            }
        }

        // دالة معالجة اختيار اللون - مبسطة
        function handleColorSelection(radio) {
            if (!radio || !radio.checked) return;
            
            const colorId = radio.value;
            const colorName = radio.getAttribute('data-color-name') || 'لون غير محدد';
            const colorCode = radio.getAttribute('data-color-code') || '#007bff';
            const price = parseFloat(radio.getAttribute('data-price')) || 0;
            const originalStock = parseInt(radio.getAttribute('data-stock')) || 0;
            
            console.log('Color selected:', colorName, 'ID:', colorId, 'Price:', price, 'Original Stock:', originalStock);
            
            // تحديث المتغيرات العامة
            selectedColorId = colorId;
            selectedColorName = colorName;
            
            // حفظ في hidden input
            const colorInput = document.getElementById('selected_color_id');
            if (colorInput) {
                colorInput.value = colorId;
            }
            
            // تحديث السعر المعروض
            if (price > 0) {
                currentPrice = price;
                updateDisplayedPrice();
            }
            
            // حساب المخزون الفعلي (بعد خصم ما تم إضافته محلياً)
            const currentStock = getCurrentStock(selectedColorId, selectedSizeId);
            
            // تحديث الحد الأقصى للكمية حسب المخزون الفعلي
            const quantityInput = document.getElementById('productQuantity');
            if (quantityInput) {
                quantityInput.max = Math.max(0, currentStock);
                if (parseInt(quantityInput.value) > currentStock) {
                    quantityInput.value = Math.max(1, currentStock);
                }
                
                // إضافة تحذير إذا كان المخزون منخفض
                if (currentStock <= 2 && currentStock > 0) {
                    showToast(`تحذير: يتبقى فقط ${currentStock} قطعة من هذا اللون والمقاس`, 'warning');
                } else if (currentStock <= 0) {
                    showToast('هذا اللون والمقاس غير متوفر حالياً', 'error');
                }
            }
            
            // تحديث عرض المخزون في الواجهة
            updateStockDisplay();
            
            // إظهار رسالة تأكيد فقط إذا كان المخزون متوفر
            if (currentStock > 0) {
                showToast(`تم اختيار اللون: ${colorName} (${currentStock} قطعة متاحة)`, 'success');
            }
        }

        // دالة تحديث السعر المعروض
        function updateDisplayedPrice() {
            const priceElement = document.querySelector('.product-price .amount');
            if (priceElement && currentPrice !== null && currentPrice !== undefined) {
                priceElement.textContent = parseFloat(currentPrice).toFixed(2);
            }
        }

        // دالة إظهار الرسائل المؤقتة
        function showToast(message, type = 'info') {
            // إنشاء عنصر التوست
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'warning' ? 'warning' : type === 'error' ? 'danger' : 'success'} position-fixed`;
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.minWidth = '300px';
            toast.innerHTML = `
                <i class="fas fa-${type === 'warning' ? 'exclamation-triangle' : type === 'error' ? 'times-circle' : 'check-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            document.body.appendChild(toast);
            
            // إزالة تلقائية بعد 3 ثوان
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }

        // دالة تحديث عدد العناصر في السلة
        function updateCartCount(count) {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = count;
            }
        }
        
        // دالة للحصول على المخزون الحالي (بعد خصم ما تم إضافته للسلة محلياً)
        function getCurrentStock(colorId, sizeId) {
            const key = `${colorId}_${sizeId}`;
            const selectedRadio = document.querySelector(`input[name="product_color"][value="${colorId}"]`);
            let actualStock = 0;
            
            if (selectedRadio) {
                // استخدام المخزون الفعلي الحالي بدلاً من المخزون الأصلي
                actualStock = parseInt(selectedRadio.getAttribute('data-actual-stock')) || 0;
                
                // إذا لم يوجد data-actual-stock، استخدم data-stock كبديل
                if (actualStock === 0) {
                    actualStock = parseInt(selectedRadio.getAttribute('data-stock')) || 0;
                }
            }
            
            // خصم ما تم إضافته محلياً
            const reduction = localInventoryReduction[key] || 0;
            const finalStock = Math.max(0, actualStock - reduction);
            
            // تسجيل للتشخيص (سيتم إزالته لاحقاً)
            if (window.location.href.includes('localhost') || window.location.href.includes('127.0.0.1')) {
                console.log(`Stock for color ${colorId}, size ${sizeId}: actual=${actualStock}, reduction=${reduction}, final=${finalStock}`);
            }
            
            return finalStock;
        }

        // دالة للحصول على مخزون اللون (مجموع جميع المقاسات)
        function getCurrentColorStock(colorId) {
            const colorRadios = document.querySelectorAll(`input[name="product_color"][value="${colorId}"]`);
            let totalStock = 0;
            
            colorRadios.forEach(radio => {
                // استخدام المخزون الفعلي بدلاً من المخزون الأصلي
                let actualStock = parseInt(radio.getAttribute('data-actual-stock')) || 0;
                if (actualStock === 0) {
                    actualStock = parseInt(radio.getAttribute('data-stock')) || 0;
                }
                
                const key = `${colorId}_${selectedSizeId || 'all'}`;
                const reduction = localInventoryReduction[key] || 0;
                totalStock += Math.max(0, actualStock - reduction);
            });
            
            return totalStock;
        }

        // دالة لتحديث المخزون محلياً بعد الإضافة للسلة
        function updateLocalStock(colorId, sizeId, quantity) {
            if (!colorId || !sizeId) return;
            
            const key = `${colorId}_${sizeId}`;
            localInventoryReduction[key] = (localInventoryReduction[key] || 0) + quantity;
            
            // حفظ في Local Storage
            saveLocalInventory();
            
            console.log('Local inventory updated:', key, 'reduced by:', quantity);
            console.log('Current local reductions:', localInventoryReduction);
        }

        // دالة لحساب المخزون الكلي للمقاس (بعد خصم ما تم إضافته محلياً)
        function calculateSizeStock(sizeId) {
            let totalStock = 0;
            
            // البحث عن المقاس والحصول على بيانات الألوان المرتبطة به
            const sizeRadio = document.querySelector(`input[name="product_size"][value="${sizeId}"]`);
            if (!sizeRadio) return 0;
            
            try {
                const colorsDataJson = sizeRadio.getAttribute('data-colors');
                const colorsData = colorsDataJson ? JSON.parse(colorsDataJson) : {};
                
                // حساب المخزون لكل لون في هذا المقاس
                Object.keys(colorsData).forEach(colorId => {
                    const colorData = colorsData[colorId];
                    if (colorData && colorData.available_stock) {
                        // الحصول على المخزون الفعلي بعد خصم ما تم إضافته محلياً
                        const key = `${colorId}_${sizeId}`;
                        const reduction = localInventoryReduction[key] || 0;
                        const actualStock = Math.max(0, colorData.available_stock - reduction);
                        totalStock += actualStock;
                    }
                });
            } catch (e) {
                console.error('Error parsing colors data for size:', sizeId, e);
            }
            
            return totalStock;
        }

        // دالة لتحديث عرض المقاسات
        function updateSizeDisplay() {
            const sizeRadios = document.querySelectorAll('input[name="product_size"]');
            
            sizeRadios.forEach(radio => {
                const sizeId = radio.value;
                const sizeWrapper = radio.closest('.size-radio-wrapper');
                
                if (sizeWrapper) {
                    const label = sizeWrapper.querySelector('label');
                    const stockInfo = label ? label.querySelector('small') : null;
                    
                    if (stockInfo) {
                        const totalSizeStock = calculateSizeStock(sizeId);
                        
                        if (totalSizeStock > 0) {
                            stockInfo.textContent = `(${totalSizeStock} قطعة)`;
                            stockInfo.className = 'text-success d-block';
                            radio.disabled = false;
                            label.classList.remove('disabled');
                        } else {
                            stockInfo.textContent = '(غير متوفر)';
                            stockInfo.className = 'text-muted d-block';
                            radio.disabled = true;
                            label.classList.add('disabled');
                        }
                    }
                }
            });
        }

        // دالة لتحديث عرض المخزون في الواجهة
        function updateStockDisplay() {
            const stockInfoDiv = document.getElementById('stockInfo');
            const stockMessage = document.getElementById('stockMessage');
            
            // تحديث عرض المقاسات
            updateSizeDisplay();
            
            // تحديث عرض الألوان المتاحة
            if (selectedColorId && selectedSizeId) {
                const currentStock = getCurrentStock(selectedColorId, selectedSizeId);
                const selectedColorRadio = document.querySelector(`input[name="product_color"][value="${selectedColorId}"]`);
                
                if (selectedColorRadio) {
                    // تحديث عرض المخزون في معلومات اللون
                    const colorWrapper = selectedColorRadio.closest('.color-radio-wrapper');
                    if (colorWrapper) {
                        const stockDisplay = colorWrapper.querySelector('.color-stock');
                        if (stockDisplay) {
                            if (currentStock > 0) {
                                stockDisplay.textContent = `${currentStock} قطعة`;
                                stockDisplay.className = 'color-stock text-success';
                        } else {
                                stockDisplay.textContent = 'نفد المخزون';
                                stockDisplay.className = 'color-stock text-danger';
                                
                                // تعطيل اللون
                                selectedColorRadio.disabled = true;
                                const colorVisual = colorWrapper.querySelector('.color-visual');
                                if (colorVisual) {
                                    colorVisual.classList.add('disabled');
                                }
                            }
                        }
                    }
                    
                    // تحديث الحد الأقصى لحقل الكمية
            const quantityInput = document.getElementById('productQuantity');
                    if (quantityInput) {
                        quantityInput.max = currentStock;
                        if (parseInt(quantityInput.value) > currentStock) {
                            quantityInput.value = Math.max(1, currentStock);
                        }
                    }
                }
                
                // عرض معلومات المخزون المحدثة
                if (stockInfoDiv && stockMessage) {
                    let alertClass = 'alert-info';
                    let message = '';
                    
                    if (currentStock <= 0) {
                        alertClass = 'alert-danger';
                        message = `❌ نفد المخزون لهذا اللون والمقاس`;
                    } else if (currentStock <= 2) {
                        alertClass = 'alert-warning';
                        message = `⚠️ تحذير: يتبقى فقط ${currentStock} قطعة من ${selectedColorName} مقاس ${selectedSizeName}`;
                    } else if (currentStock <= 5) {
                        alertClass = 'alert-info';
                        message = `📦 متوفر: ${currentStock} قطعة من ${selectedColorName} مقاس ${selectedSizeName}`;
            } else {
                        alertClass = 'alert-success';
                        message = `✅ متوفر بكثرة: ${currentStock} قطعة من ${selectedColorName} مقاس ${selectedSizeName}`;
                    }
                    
                    // تحديث الشكل والرسالة
                    stockInfoDiv.className = `alert ${alertClass} mb-4`;
                    stockMessage.textContent = message;
                    stockInfoDiv.style.display = 'block';
                    
                    // إخفاء تلقائي للرسائل الإيجابية بعد 5 ثوان
                    if (alertClass === 'alert-success' || alertClass === 'alert-info') {
                        setTimeout(() => {
                            if (stockInfoDiv.style.display === 'block') {
                                stockInfoDiv.style.display = 'none';
                            }
                        }, 5000);
                    }
                }
                    } else {
                // إخفاء معلومات المخزون إذا لم يتم اختيار لون ومقاس
                if (stockInfoDiv) {
                    stockInfoDiv.style.display = 'none';
                }
            }
        }



        // دالة تحديث الصورة الرئيسية
        function updateMainImage(src, thumbnail) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail-wrapper').forEach(thumb => {
                thumb.classList.remove('active');
            });
            if (thumbnail) {
                thumbnail.classList.add('active');
            }
        }

        // إعداد الصفحة عند التحميل
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Product page loaded - Advanced inventory system initialized');
            console.log('Local inventory tracking active');
            
            // تحميل المخزون المحلي المحفوظ
            loadLocalInventory();
            
            // إضافة event listeners لأزرار الكمية
            const decreaseBtn = document.getElementById('decreaseQuantity');
            const increaseBtn = document.getElementById('increaseQuantity');
            const quantityInput = document.getElementById('productQuantity');

            if (decreaseBtn) {
                decreaseBtn.addEventListener('click', function() {
                    let currentValue = parseInt(quantityInput.value) || 1;
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });
            }

            if (increaseBtn) {
                increaseBtn.addEventListener('click', function() {
                    let currentValue = parseInt(quantityInput.value) || 1;
                    let maxValue = parseInt(quantityInput.max) || 999;
                    
                    // التحقق من المخزون الفعلي إذا كان هناك لون ومقاس محددان
                    if (selectedColorId && selectedSizeId) {
                        const currentStock = getCurrentStock(selectedColorId, selectedSizeId);
                        maxValue = Math.min(maxValue, currentStock);
                    }
                    
                    if (currentValue < maxValue) {
                        quantityInput.value = currentValue + 1;
                    } else {
                        showToast(`الحد الأقصى المتاح: ${maxValue} قطعة`, 'warning');
                    }
                });
            }

            // التأكد من صحة قيم الكمية مع التحقق من المخزون
            if (quantityInput) {
                quantityInput.addEventListener('change', function() {
                    let value = parseInt(this.value) || 1;
                    let min = parseInt(this.min) || 1;
                    let max = parseInt(this.max) || 999;
                    
                    // التحقق من المخزون الفعلي
                    if (selectedColorId && selectedSizeId) {
                        const currentStock = getCurrentStock(selectedColorId, selectedSizeId);
                        max = Math.min(max, currentStock);
                    }
                    
                    if (value < min) {
                        this.value = min;
                    } else if (value > max) {
                        this.value = max;
                        if (max < value) {
                            showToast(`الكمية القصوى المتاحة: ${max}`, 'warning');
                        }
                    }
                });
            }
            
            // تسجيل معلومات المخزون للتطوير
            if (window.location.href.includes('localhost') || window.location.href.includes('127.0.0.1')) {
                console.log('Development mode - Inventory debugging enabled');
                
                // إظهار أدوات التطوير
                const devTools = document.querySelector('.dev-tools');
                if (devTools) {
                    devTools.style.display = 'block';
                }
                


                // دالة للتحقق من صلاحية البيانات المحفوظة (بدون مسح تلقائي)
                function validateLocalInventory() {
                    const productId = <?php echo e($product->id); ?>;
                    const storageKey = `inventory_reduction_${productId}`;
                    const savedData = localStorage.getItem(storageKey);
                    
                    if (savedData) {
                        try {
                            const parsedData = JSON.parse(savedData);
                            console.log('تم العثور على بيانات مخزون محفوظة:', parsedData);
                            
                            // التحقق من صحة البيانات
                            if (typeof parsedData === 'object' && parsedData !== null) {
                                localInventoryReduction = parsedData;
                                console.log('تم استرداد بيانات المخزون المحفوظة');
                                return true;
                            }
                        } catch (e) {
                            console.error('خطأ في قراءة بيانات المخزون المحفوظة:', e);
                            localStorage.removeItem(storageKey);
                        }
                    }
                    
                    return false;
                }

                // استدعاء دالة التحقق من البيانات (بدون مسح)
                const hasValidData = validateLocalInventory();
                if (hasValidData) {
                    console.log('تم استرداد بيانات المخزون من الجلسة السابقة');
                    
                    // تحديث العرض بناء على البيانات المحفوظة
                    setTimeout(() => {
                        updateSizeDisplay();
                        refreshInventoryDisplay();
                        console.log('تم تحديث عرض المخزون بناء على البيانات المحفوظة');
                    }, 500);
                }

                // دالة لمسح بيانات المخزون عند إتمام الطلب فعلياً
                // يجب استدعاؤها من صفحة إتمام الطلب أو بعد تأكيد الشراء
                window.clearInventoryOnOrderComplete = function(productId = null) {
                    const targetProductId = productId || <?php echo e($product->id); ?>;
                    const storageKey = `inventory_reduction_${targetProductId}`;
                    
                    console.log('مسح بيانات المخزون للمنتج:', targetProductId);
                    localStorage.removeItem(storageKey);
                    localStorage.removeItem(`${storageKey}_last_reset`);
                    localStorage.removeItem(`${storageKey}_session`);
                    
                    // إذا كان هذا هو المنتج الحالي، قم بإعادة تعيين المتغير
                    if (targetProductId == <?php echo e($product->id); ?>) {
                        localInventoryReduction = {};
                        console.log('تم مسح بيانات المخزون المحلي للمنتج الحالي');
                    }
                };

                // دالة لمسح بيانات جميع المنتجات عند إتمام طلب يحتوي على عدة منتجات
                window.clearAllInventoryOnOrderComplete = function() {
                    console.log('مسح بيانات المخزون لجميع المنتجات');
                    
                    // البحث عن جميع مفاتيح المخزون في localStorage
                    const keysToRemove = [];
                    for (let i = 0; i < localStorage.length; i++) {
                        const key = localStorage.key(i);
                        if (key && key.startsWith('inventory_reduction_')) {
                            keysToRemove.push(key);
                        }
                    }
                    
                    // حذف جميع مفاتيح المخزون
                    keysToRemove.forEach(key => {
                        localStorage.removeItem(key);
                        localStorage.removeItem(`${key}_last_reset`);
                        localStorage.removeItem(`${key}_session`);
                    });
                    
                    // إعادة تعيين المتغير للمنتج الحالي
                    localInventoryReduction = {};
                    console.log('تم مسح بيانات المخزون لجميع المنتجات');
                };

                // دالة للتحقق من بيانات المخزون للمنتج الحالي (للتشخيص)
                window.checkCurrentInventoryStatus = function() {
                    console.log('=== حالة المخزون الحالي ===');
                    console.log('البيانات المحلية:', localInventoryReduction);
                    console.log('المنتج المختار - اللون:', selectedColorId, 'المقاس:', selectedSizeId);
                    if (selectedColorId && selectedSizeId) {
                        const currentStock = getCurrentStock(selectedColorId, selectedSizeId);
                        console.log('المخزون المتاح:', currentStock);
                    }
                    console.log('========================');
                };


            }
        });













        // دالة إضافة للسلة - مع تحديث المخزون المحلي
        function addToCart() {
            console.log('=== Add to Cart Called ===');
            
            // التحقق من تسجيل الدخول
            <?php if(auth()->guard()->guest()): ?>
                showToast('يجب تسجيل الدخول أولاً لإضافة المنتج للسلة', 'warning');
                setTimeout(() => {
                    window.location.href = '<?php echo e(route("login")); ?>';
                }, 2000);
                return;
            <?php endif; ?>
            
            const quantity = parseInt(document.getElementById('productQuantity').value) || 1;
            const customColor = document.getElementById('customColor')?.value || '';
            const customSize = document.getElementById('customSize')?.value || '';
            
            console.log('Quantity:', quantity);
            console.log('Custom Color:', customColor);
            console.log('Custom Size:', customSize);
            console.log('Selected Color ID:', selectedColorId, 'Name:', selectedColorName);
            console.log('Selected Size ID:', selectedSizeId, 'Name:', selectedSizeName);
            
            // التحقق من الاختيارات المطلوبة
            const hasSizes = document.querySelector('input[name="product_size"]') !== null;
            if (hasSizes && !selectedSizeId && !customSize) {
                showToast('يرجى اختيار المقاس أولاً', 'warning');
                return;
            }
            
            const hasColors = document.querySelector('input[name="product_color"]') !== null;
            if (hasColors && !selectedColorId && !customColor) {
                showToast('يرجى اختيار اللون أولاً', 'warning');
                return;
            }
            
            // التحقق من المخزون المتاح محلياً
            if (selectedColorId && selectedSizeId) {
                const currentStock = getCurrentStock(selectedColorId, selectedSizeId);
                if (quantity > currentStock) {
                    showToast(`الكمية المطلوبة (${quantity}) تتجاوز المخزون المتاح (${currentStock})`, 'warning');
                    return;
                }
            } else if (selectedColorId) {
                const currentStock = getCurrentColorStock(selectedColorId);
                if (quantity > currentStock) {
                    showToast(`الكمية المطلوبة (${quantity}) تتجاوز المخزون المتاح (${currentStock})`, 'warning');
                    return;
                }
            }
            
            // إعداد بيانات المنتج للإرسال
            const cartData = {
                product_id: <?php echo e($product->id); ?>,
                quantity: quantity,
                size: customSize || selectedSizeName || null,
                color: customColor || selectedColorName || null
            };
            
            // إضافة IDs إذا كانت متوفرة
            if (selectedSizeId) {
                cartData.size_id = selectedSizeId;
            }
            if (selectedColorId) {
                cartData.color_id = selectedColorId;
            }
            
            console.log('Adding to cart:', cartData);
            
            // تعطيل الزر أثناء المعالجة
            const addButton = document.querySelector('button[onclick="addToCart()"]');
            if (addButton) {
                addButton.disabled = true;
                addButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...';
            }
            
            // إرسال البيانات للخادم
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(cartData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // تحقق من الـ Response قبل تحويله لـ JSON
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Backend response:', data);
                
                if (data.success) {
                    // تحديث المخزون محلياً
                    updateLocalStock(selectedColorId, selectedSizeId, quantity);
                    
                    // تحديث الواجهة
                    updateStockDisplay();
                    
                    // تحديث عرض جميع المقاسات أيضاً
                    updateSizeDisplay();
                    
                    showToast(`تم إضافة ${quantity} قطعة إلى السلة بنجاح`, 'success');
                    
                    // تحديث عدد العناصر في السلة
                    if (data.cart_count !== undefined) {
                        updateCartCount(data.cart_count);
                    }
                    
                    // إعادة تعيين الكمية إلى 1
                    document.getElementById('productQuantity').value = 1;
                    
                } else {
                    console.error('Backend returned error:', data.message);
                    showToast(data.message || 'حدث خطأ أثناء إضافة المنتج', 'error');
                }
            })
            .catch(error => {
                console.error('Fetch error details:', error);
                
                // تشخيص مفصل للخطأ
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    showToast('مشكلة في الاتصال بالخادم', 'error');
                } else if (error.message.includes('HTTP error')) {
                    showToast(`خطأ في الخادم: ${error.message}`, 'error');
                } else {
                    showToast('حدث خطأ غير متوقع', 'error');
                }
            })
            .finally(() => {
                // إعادة تفعيل الزر
                if (addButton) {
                    addButton.disabled = false;
                    addButton.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>أضف إلى السلة';
                    }
                });
            }


    </script>
</body>
</html>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/products/show.blade.php ENDPATH**/ ?>