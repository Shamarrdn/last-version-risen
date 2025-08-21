<?php $__env->startSection('title', 'risen born in ksa | ملابس أطفال عالية الجودة'); ?>

<?php $__env->startSection('content'); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="تسوق منتجات risen born in ksa - ملابس أطفال عالية الجودة، تصاميم عصرية، وخامات ممتازة.">
    <meta name="keywords" content="risen born in ksa، ملابس أطفال، مصنع ملابس، ملابس عصرية، ملابس أطفال جودة عالية، السعودية">
    <meta name="author" content="risen born in ksa">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="risen born in ksa">
    <meta property="og:title" content="risen born in ksa | ملابس أطفال عالية الجودة">
    <meta property="og:description" content="تسوق منتجات risen born in ksa - ملابس أطفال عالية الجودة، تصاميم عصرية، وخامات ممتازة.">
    <meta property="og:image" content="/assets/images/logo.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="risen born in ksa | ملابس أطفال عالية الجودة">
    <meta name="twitter:description" content="تسوق منتجات risen born in ksa - ملابس أطفال عالية الجودة، تصاميم عصرية، وخامات ممتازة.">
    <meta name="twitter:image" content="/assets/images/logo.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo e(url()->current()); ?>">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>risen born in ksa | ملابس أطفال عالية الجودة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/clothes-platform/style.css')); ?>?t=<?php echo e(time()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/products.css')); ?>?t=<?php echo e(time()); ?>">
     <link rel="stylesheet" href="<?php echo e(asset('assets/kids/css/common.css')); ?>?t=<?php echo e(time()); ?>">
</head>
<body class="<?php echo e(auth()->check() ? 'user-logged-in' : ''); ?>" >
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
    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-shopping-cart me-2"></i>
                <strong class="me-auto">تحديث السلة</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                تم إضافة المنتج إلى السلة بنجاح!
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container-fluid py-4 "  style="margin-top: 60px;">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 filter-sidebar">
                <div class="filter-container">
                    <h3>الفلاتر</h3>
                    <div class="filter-section">
                        <h4>الفئات</h4>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="category-item mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    value="<?php echo e($category->slug); ?>"
                                    id="category-<?php echo e($category->id); ?>"
                                    name="categories[]"
                                    <?php echo e(request('category') == $category->slug ? 'checked' : ''); ?>>
                                <label class="form-check-label d-flex justify-content-between align-items-center"
                                    for="category-<?php echo e($category->id); ?>">
                                    <?php echo e($category->name); ?>

                                    <span class="badge bg-primary rounded-pill"><?php echo e($category->products_count); ?></span>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="filter-section">
                        <h4>نطاق السعر</h4>
                        <div class="form-group mb-4">
                            <label for="priceRange" class="form-label">السعر</label>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?php echo e(number_format($priceRange['min'])); ?> ر.س</span>
                                <span id="priceValue"><?php echo e(number_format($priceRange['max'])); ?> ر.س</span>
                            </div>
                            <input type="range" class="form-range" id="priceRange"
                                min="<?php echo e($priceRange['min']); ?>"
                                max="<?php echo e($priceRange['max']); ?>"
                                value="<?php echo e($priceRange['max']); ?>">
                        </div>
                    </div>

                    <div class="filter-section">
                        <h4>الخصومات</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                value="1"
                                id="discountFilter"
                                name="has_discounts"
                                <?php echo e(request('has_discounts') ? 'checked' : ''); ?>>
                            <label class="form-check-label d-flex justify-content-between align-items-center"
                                for="discountFilter">
                                المنتجات ذات الخصومات
                                <i class="fas fa-tag text-danger ms-2"></i>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="section-header mb-4">
                    <h2>جميع المنتجات</h2>
                    <div class="d-flex gap-3 align-items-center">
                        <select class="form-select glass-select" id="sortSelect">
                            <option value="newest">الأحدث</option>
                            <option value="price-low">السعر: من الأقل للأعلى</option>
                            <option value="price-high">السعر: من الأعلى للأقل</option>
                        </select>
                        <button onclick="resetFilters()" class="btn btn-outline-primary" id="resetFiltersBtn">
                            <i class="fas fa-filter-circle-xmark me-2"></i>
                            إزالة الفلتر
                        </button>
                    </div>
                </div>
                <div class="row g-4" id="productGrid">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="product-card">
                            <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="product-image-wrapper">
                                <?php if($product->images->isNotEmpty()): ?>
                                    <img src="<?php echo e(url('storage/' . $product->images->first()->image_path)); ?>"
                                         alt="<?php echo e($product->name); ?>"
                                         class="product-image">
                                <?php else: ?>
                                    <img src="<?php echo e(url('images/placeholder.jpg')); ?>"
                                         alt="<?php echo e($product->name); ?>"
                                         class="product-image">
                                <?php endif; ?>

                                <?php
                                    $couponBadge = app(\App\Services\Customer\Products\ProductService::class)->getProductCouponBadge($product);
                                ?>

                                <?php if($couponBadge): ?>
                                    <div class="coupon-badge position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-danger">
                                            <i class="fas fa-tag me-1"></i><?php echo e($couponBadge['discount_text']); ?>

                                        </span>
                                        <small class="d-block mt-1 text-white bg-dark px-1 rounded text-center">كود: <?php echo e($couponBadge['code']); ?></small>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="product-details">
                                <div class="product-category d-flex flex-wrap gap-1 align-items-center mb-2">
                                    <a href="?category=<?php echo e($product->category->slug); ?>" class="text-decoration-none">
                                        <span class="badge rounded-pill bg-primary"><?php echo e($product->category->name); ?></span>
                                    </a>
                                    <?php if($product->categories->isNotEmpty()): ?>
                                        <?php $__currentLoopData = $product->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additionalCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($additionalCategory->id != $product->category_id): ?>
                                                <a href="?category=<?php echo e($additionalCategory->slug); ?>" class="text-decoration-none">
                                                    <span class="badge rounded-pill bg-light text-dark border"><?php echo e($additionalCategory->name); ?></span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                                <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="product-title text-decoration-none">
                                    <h3><?php echo e($product->name); ?></h3>
                                </a>
                                <div class="product-rating">
                                    <div class="stars" style="--rating: <?php echo e($product['rating']); ?>"></div>
                                </div>
                                <p class="product-price">
                                    <?php if($product->min_price == $product->max_price): ?>
                                        <?php echo e(number_format($product->min_price, 2)); ?> ر.س
                                    <?php else: ?>
                                        <?php echo e(number_format($product->min_price, 2)); ?> - <?php echo e(number_format($product->max_price, 2)); ?> ر.س
                                    <?php endif; ?>
                                </p>
                                <div class="product-actions">
                                    <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="order-product-btn">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        طلب المنتج
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Shopping Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>سلة التسوق</h3>
            <button class="close-cart" id="closeCart">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Cart Items Container with Scroll -->
        <div class="cart-items-container">
            <div class="cart-items" id="cartItems">
                <!-- Cart items will be dynamically added here -->
            </div>
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

    <!-- Cart Overlay -->
    <div class="cart-overlay"></div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title">تفاصيل المنتج</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div id="productCarousel" class="carousel slide product-carousel" data-bs-ride="carousel">
                                <div class="carousel-inner rounded-3">
                                    <!-- Carousel items will be dynamically added -->
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3 id="modalProductName" class="product-title mb-3"></h3>
                            <p id="modalProductDescription" class="product-description mb-4"></p>
                            <div class="price-section mb-4">
                                <h4 id="modalProductPrice" class="product-price"></h4>
                            </div>
                            <div class="quantity-selector mb-4">
                                <label class="form-label">الكمية:</label>
                                <div class="input-group quantity-group">
                                    <button class="btn btn-outline-primary" type="button" id="decreaseQuantity">-</button>
                                    <input type="number" class="form-control text-center" id="productQuantity" value="1" min="1">
                                    <button class="btn btn-outline-primary" type="button" id="increaseQuantity">+</button>
                                </div>
                            </div>

                            <!-- Colors Section -->
                            <div class="colors-section mb-4" id="modalProductColors">
                                <label class="form-label">الألوان المتاحة:</label>
                                <div class="colors-grid">
                                    <!-- Colors will be added dynamically -->
                                </div>
                            </div>

                            <!-- Sizes Section -->
                            <div class="sizes-section mb-4" id="modalProductSizes">
                                <label class="form-label">المقاسات المتاحة:</label>
                                <div class="sizes-grid">
                                    <!-- Sizes will be added dynamically -->
                                </div>
                            </div>

                            <button class="btn add-to-cart-btn w-100" id="modalAddToCart">
                                <i class="fas fa-shopping-cart me-2"></i>
                                أضف للسلة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Prompt Modal -->
    <div class="modal fade" id="loginPromptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تسجيل الدخول مطلوب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>يجب عليك تسجيل الدخول أو إنشاء حساب جديد لتتمكن من طلب المنتج</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary">تسجيل الدخول</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-primary">إنشاء حساب جديد</a>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('parts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo e(asset('assets/js/index.js')); ?>?t=<?php echo e(time()); ?>"></script>


    <script>
        window.appConfig = {
            routes: {
                products: {
                    filter: '<?php echo e(route("products.filter")); ?>',
                    details: '<?php echo e(route("products.details", ["product" => "__id__"])); ?>'
                }
            }
        };
    </script>

    <!-- Products JavaScript - Load after appConfig -->
    <script src="<?php echo e(asset('assets/js/customer/products.js')); ?>?t=<?php echo e(time()); ?>"></script>
</body>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/products/index.blade.php ENDPATH**/ ?>