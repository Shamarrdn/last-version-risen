@extends('layouts.app')

@section('title', 'risen born in ksa | ملابس أطفال عالية الجودة')

@section('content')

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
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="risen born in ksa | ملابس أطفال عالية الجودة">
    <meta name="twitter:description" content="تسوق منتجات risen born in ksa - ملابس أطفال عالية الجودة، تصاميم عصرية، وخامات ممتازة.">
    <meta name="twitter:image" content="/assets/images/logo.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>risen born in ksa | ملابس أطفال عالية الجودة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/clothes-platform/style.css') }}?t={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customer/products.css') }}?t={{ time() }}">
     <link rel="stylesheet" href="{{ asset('assets/kids/css/common.css') }}?t={{ time() }}">
</head>
<body class="{{ auth()->check() ? 'user-logged-in' : '' }}" >
    @include('parts.navbar')

    <!-- Fixed Buttons Group -->
    <div class="fixed-buttons-group">
        <button class="fixed-cart-btn" id="fixedCartBtn">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                0
            </span>
        </button>
        @auth
        <a href="/dashboard" class="fixed-dashboard-btn">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
        @endauth
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
                        @foreach($categories as $category)
                        <div class="category-item mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    value="{{ $category->slug }}"
                                    id="category-{{ $category->id }}"
                                    name="categories[]"
                                    {{ request('category') == $category->slug ? 'checked' : '' }}>
                                <label class="form-check-label d-flex justify-content-between align-items-center"
                                    for="category-{{ $category->id }}">
                                    {{ $category->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $category->products_count }}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="filter-section">
                        <h4>نطاق السعر</h4>
                        <div class="form-group mb-4">
                            <label for="priceRange" class="form-label">السعر</label>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ number_format($priceRange['min']) }} ر.س</span>
                                <span id="priceValue">{{ number_format($priceRange['max']) }} ر.س</span>
                            </div>
                            <input type="range" class="form-range" id="priceRange"
                                min="{{ $priceRange['min'] }}"
                                max="{{ $priceRange['max'] }}"
                                value="{{ $priceRange['max'] }}">
                        </div>
                    </div>

                    <div class="filter-section">
                        <h4>الخصومات</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                value="1"
                                id="discountFilter"
                                name="has_discounts"
                                {{ request('has_discounts') ? 'checked' : '' }}>
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
                    @foreach($products as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="product-card">
                            <a href="{{ route('products.show', $product->slug) }}" class="product-image-wrapper">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ url('storage/' . $product->images->first()->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="product-image">
                                @else
                                    <img src="{{ url('images/placeholder.jpg') }}"
                                         alt="{{ $product->name }}"
                                         class="product-image">
                                @endif

                                @php
                                    $couponBadge = app(\App\Services\Customer\Products\ProductService::class)->getProductCouponBadge($product);
                                @endphp

                                @if($couponBadge)
                                    <div class="coupon-badge position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-danger">
                                            <i class="fas fa-tag me-1"></i>{{ $couponBadge['discount_text'] }}
                                        </span>
                                        <small class="d-block mt-1 text-white bg-dark px-1 rounded text-center">كود: {{ $couponBadge['code'] }}</small>
                                    </div>
                                @endif
                            </a>
                            <div class="product-details">
                                <div class="product-category d-flex flex-wrap gap-1 align-items-center mb-2">
                                    <a href="?category={{ $product->category->slug }}" class="text-decoration-none">
                                        <span class="badge rounded-pill bg-primary">{{ $product->category->name }}</span>
                                    </a>
                                    @if($product->categories->isNotEmpty())
                                        @foreach($product->categories as $additionalCategory)
                                            @if($additionalCategory->id != $product->category_id)
                                                <a href="?category={{ $additionalCategory->slug }}" class="text-decoration-none">
                                                    <span class="badge rounded-pill bg-light text-dark border">{{ $additionalCategory->name }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <a href="{{ route('products.show', $product->slug) }}" class="product-title text-decoration-none">
                                    <h3>{{ $product->name }}</h3>
                                </a>
                                <div class="product-rating">
                                    <div class="stars" style="--rating: {{ $product['rating'] }}"></div>
                                </div>
                                <p class="product-price">
                                    @if($product->current_min_price == $product->current_max_price)
                                        {{ number_format($product->current_min_price, 2) }} ر.س
                                    @else
                                        {{ number_format($product->current_min_price, 2) }} - {{ number_format($product->current_max_price, 2) }} ر.س
                                    @endif
                                </p>
                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->slug) }}" class="order-product-btn">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        طلب المنتج
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
            <a href="{{ route('checkout.index') }}" class="checkout-btn">
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
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">إنشاء حساب جديد</a>
                </div>
            </div>
        </div>
    </div>

    @include('parts.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/index.js') }}?t={{ time() }}"></script>


    <script>
        window.appConfig = {
            routes: {
                products: {
                    filter: '{{ route("products.filter") }}',
                    details: '{{ route("products.details", ["product" => "__id__"]) }}'
                }
            }
        };
    </script>

    <!-- Products JavaScript - Load after appConfig -->
    <script src="{{ asset('assets/js/customer/products.js') }}?t={{ time() }}"></script>
</body>

@endsection

