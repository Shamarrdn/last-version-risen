@extends('layouts.app')

@section('title', 'RISEN - Born in KSA')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/css/clothes-platform/index.css') }}?t={{ time() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
@endsection

@section('content')
<body class="{{ auth()->check() ? 'user-logged-in' : '' }}">
<!-- Hero Section -->
<section class="hero-section" id="home">
    <div class="hero-bg"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">RISEN</h1>
                    <p class="hero-subtitle">BORN IN KSA</p>
                    <p class="hero-description">
                        اكتشف مجموعة متميزة من الأزياء العصرية والأناقة الفريدة.
                        منتجات عالية الجودة تعكس الذوق السعودي الأصيل مع لمسة عالمية.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('products.index') }}" class="btn btn-modern btn-primary-modern">
                            تسوق الآن
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-modern btn-outline-modern">
                            استكشف المجموعات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section fade-in" id="categories">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">أقسام المتجر</h2>
            <p class="text-muted">اكتشف مجموعاتنا المتنوعة</p>
        </div>

        <div class="row g-4">
            @foreach($topCategories as $category)
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-decoration-none text-dark">
                    <div class="category-card">
                        <div class="category-image" style="background-image: url('{{ $category->image_url }}');">
                        </div>
                        <div class="category-content">
                            <h4 class="category-title">{{ $category->name }}</h4>
                            <p class="category-subtitle">{{ $category->products_count }} منتج</p>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- All Products Carousel -->
<section class="products-carousel-section fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-title-container">
                <h2 class="section-title section-title-slide">جميع منتجاتنا</h2>
                <div class="title-underline"></div>
            </div>
            <p class="text-muted">تصفح كل منتجاتنا المميزة</p>
        </div>
    </div>
    <div class="products-carousel owl-carousel owl-theme">
        @foreach($allProducts as $product)
        <div class="item">
            <div class="product-card">
                @php
                    $imageUrl = $product->images->isNotEmpty() ? url('storage/' . $product->images->first()->image_path) : 'https://via.placeholder.com/300';
                @endphp
                <a href="{{ route('products.show', $product->slug) }}">
                    <div class="product-image" style="background-image: url('{{ $imageUrl }}');">
                        <div class="product-overlay">
                            <div class="product-actions">
                                <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="عرض المنتج">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="product-info">
                    <h5 class="product-title"><a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">{{ $product->name }}</a></h5>
                    <p class="product-price">
                        @if($product->min_price == $product->max_price)
                            {{ number_format($product->min_price, 2) }} ريال
                        @else
                            {{ number_format($product->min_price, 2) }} - {{ number_format($product->max_price, 2) }} ريال
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Featured Products -->
<section class="products-section fade-in" id="products">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">المنتجات المميزة</h2>
            <p class="text-muted">اختارنا لك أفضل منتجاتنا</p>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $product)
            <div class="col-lg-3 col-md-6">
                <div class="product-card">
                    @php
                        $imageUrl = $product->images->isNotEmpty() ? url('storage/' . $product->images->first()->image_path) : 'https://via.placeholder.com/300';
                    @endphp
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div class="product-image" style="background-image: url('{{ $imageUrl }}');">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="عرض المنتج">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="product-info">
                        <h5 class="product-title"><a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">{{ $product->name }}</a></h5>
                        <p class="product-price">
                            @if($product->min_price == $product->max_price)
                                {{ number_format($product->min_price, 2) }} ريال
                            @else
                                {{ number_format($product->min_price, 2) }} - {{ number_format($product->max_price, 2) }} ريال
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- New Products Section -->
<section class="products-section fade-in" style="background: var(--accent-color);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">أحدث المنتجات</h2>
            <p class="text-muted">اكتشف مجموعتنا الجديدة</p>
        </div>

        <div class="row g-4">
            @foreach($newProducts as $product)
            <div class="col-lg-3 col-md-6">
                <div class="product-card">
                    @php
                        $imageUrl = $product->images->isNotEmpty() ? url('storage/' . $product->images->first()->image_path) : 'https://via.placeholder.com/300';
                    @endphp
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div class="product-image" style="background-image: url('{{ $imageUrl }}');">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="عرض المنتج">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="product-info">
                        <h5 class="product-title"><a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">{{ $product->name }}</a></h5>
                        <p class="product-price">
                            @if($product->min_price == $product->max_price)
                                {{ number_format($product->min_price, 2) }} ريال
                            @else
                                {{ number_format($product->min_price, 2) }} - {{ number_format($product->max_price, 2) }} ريال
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Discounted Products Section -->
<section class="products-section fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">منتجات عليها خصم</h2>
            <p class="text-muted">عروض خاصة لفترة محدودة</p>
        </div>

        <div class="row g-4">
            @foreach($discountedProducts as $product)
            <div class="col-lg-3 col-md-6">
                <div class="product-card">
                    @php
                        $imageUrl = $product->images->isNotEmpty() ? url('storage/' . $product->images->first()->image_path) : 'https://via.placeholder.com/300';
                    @endphp
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div class="product-image" style="background-image: url('{{ $imageUrl }}');">
                            <div class="product-overlay">
                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->slug) }}" class="action-btn" title="عرض المنتج">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="product-info">
                        <h5 class="product-title"><a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">{{ $product->name }}</a></h5>
                        <p class="product-price">
                            @if($product->min_price == $product->max_price)
                                {{ number_format($product->min_price, 2) }} ريال
                            @else
                                {{ number_format($product->min_price, 2) }} - {{ number_format($product->max_price, 2) }} ريال
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section fade-in">
    <div class="stats-background">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
    </div>
    <div class="container">
        <div class="stats-header text-center mb-5">
            <h2 class="stats-title">إنجازاتنا في أرقام</h2>
            <p class="stats-subtitle">نفخر بخدمة عملائنا الكرام</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="10000">0</div>
                            <div class="stat-label">عميل سعيد</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="500">0</div>
                            <div class="stat-label">منتج متميز</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="50">0</div>
                            <div class="stat-label">مدينة نخدمها</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-card-inner">
                        <div class="stat-icon-wrapper">
                            <div class="stat-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="stat-glow"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number" data-count="5">0</div>
                            <div class="stat-label">سنوات خبرة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
@endsection

@section('page-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="{{ asset('assets/js/index.js') }}?t={{ time() }}"></script>
<script>
    $(document).ready(function(){

        const $carousel = $('.products-carousel');
        if ($carousel.length > 0) {
            console.log('Carousel found, initializing...');
            $carousel.owlCarousel({
                rtl: true,
                loop: true,
                margin: 20,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                smartSpeed: 600,
                fluidSpeed: 600,
                autoplaySpeed: 600,
                navSpeed: 600,
                dotsSpeed: 600,
                dragEndSpeed: 600,
                responsive: {
                    0: {
                        items: 1,
                        margin: 10
                    },
                    576: {
                        items: 2,
                        margin: 15
                    },
                    992: {
                        items: 3,
                        margin: 20
                    },
                    1200: {
                        items: 4,
                        margin: 20
                    }
                },
                navText: [
                    "<i class='fas fa-chevron-right'></i>",
                    "<i class='fas fa-chevron-left'></i>"
                ]
            });

            console.log('Carousel initialized successfully');
        } else {
            console.log('Carousel not found in the page');
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    $('.products-carousel-section').addClass('active');
                }
            });
        }, { threshold: 0.1 });

        const carouselSection = document.querySelector('.products-carousel-section');
        if (carouselSection) {
            observer.observe(carouselSection);
        }
    });
</script>
@endsection
