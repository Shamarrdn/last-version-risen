@extends('layouts.app')

@section('title', 'الخدمات - RISEN')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/css/clothes-platform/services.css') }}?t={{ time() }}">
@endsection

@section('content')
<!-- Services Hero Section -->
<section class="services-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content">
                    <h1 class="hero-title">خدماتنا المميزة</h1>
                    <p class="hero-subtitle">نقدم أفضل الخدمات لعملائنا الكرام</p>
                    <p class="hero-description">
                        اكتشف مجموعة شاملة من الخدمات المصممة خصيصاً لتلبية احتياجاتك
                        وتجربة تسوق استثنائية مع ضمان الجودة والموثوقية.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Services -->
<section class="services-cards-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">خدماتنا الأساسية</h2>
            <p class="text-muted">نقدم مجموعة متنوعة من الخدمات المتميزة</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="service-content">
                        <div>
                            <h4 class="service-title">الشحن السريع</h4>
                            <p class="service-description">
                                خدمة شحن سريعة وآمنة لجميع أنحاء المملكة مع تتبع مباشر للطلبات.
                            </p>
                        </div>
                        <ul class="service-features">
                            <li>توصيل خلال 24 ساعة</li>
                            <li>تتبع مباشر للطلب</li>
                            <li>تأمين شامل على الشحنة</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="service-content">
                        <div>
                            <h4 class="service-title">الإرجاع المجاني</h4>
                            <p class="service-description">
                                سياسة إرجاع مرنة تتيح لك إرجاع المنتجات خلال 30 يوماً من الشراء.
                            </p>
                        </div>
                        <ul class="service-features">
                            <li>إرجاع مجاني خلال 30 يوم</li>
                            <li>استرداد كامل للمبلغ</li>
                            <li>عملية إرجاع سهلة</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="service-content">
                        <div>
                            <h4 class="service-title">خدمة العملاء 24/7</h4>
                            <p class="service-description">
                                فريق دعم متخصص متاح على مدار الساعة لمساعدتك في أي استفسار.
                            </p>
                        </div>
                        <ul class="service-features">
                            <li>دعم فني متخصص</li>
                            <li>متاح 24 ساعة يومياً</li>
                            <li>رد فوري على الاستفسارات</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="service-content">
                        <div>
                            <h4 class="service-title">ضمان الجودة</h4>
                            <p class="service-description">
                                جميع منتجاتنا مضمونة الجودة مع ضمان استبدال أو إصلاح مجاني.
                            </p>
                        </div>
                        <ul class="service-features">
                            <li>ضمان الجودة لمدة سنة</li>
                            <li>استبدال مجاني</li>
                            <li>إصلاح مجاني</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">لماذا تختارنا؟</h2>
            <p class="text-muted">نتميز بالعديد من المزايا التي تجعلنا الخيار الأمثل</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h4 class="feature-title">جودة عالية</h4>
                    <p class="feature-description">
                        نختار بعناية جميع منتجاتنا لضمان أعلى معايير الجودة
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="feature-title">سرعة في التوصيل</h4>
                    <p class="feature-description">
                        نضمن وصول طلبك في أسرع وقت ممكن مع تتبع مباشر
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="feature-title">رعاية العملاء</h4>
                    <p class="feature-description">
                        فريق دعم متخصص لمساعدتك في كل خطوة من رحلة التسوق
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="feature-title">أمان تام</h4>
                    <p class="feature-description">
                        حماية كاملة لبياناتك ومدفوعاتك مع أحدث تقنيات الأمان
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="cta-title">هل تحتاج مساعدة؟</h2>
                <p class="cta-description">
                    فريقنا متاح على مدار الساعة لمساعدتك في أي استفسار أو طلب
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ url('contact') }}" class="btn btn-modern btn-primary-modern">
                        تواصل معنا
                    </a>
                    <a href="tel:+966111234567" class="btn btn-modern" style="background: transparent; color: var(--primary-color); border: 2px solid var(--primary-color);">
                        <i class="fas fa-phone me-2"></i>
                        اتصل الآن
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
