@extends('layouts.app')

@section('title', 'من نحن - RISEN')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/css/clothes-platform/about.css') }}?t={{ time() }}">
@endsection

@section('content')
<!-- About Hero Section -->
<section class="about-hero fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content">
                    <h1 class="hero-title">من نحن</h1>
                    <p class="hero-subtitle">قصة نجاح سعودية</p>
                    <p class="hero-description">
                        منذ تأسيسنا في المملكة العربية السعودية، نعمل على تقديم أفضل تجربة تسوق
                        مع التركيز على الجودة والأناقة والخدمة المتميزة.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="story-section fade-in">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="story-image">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="قصة RISEN">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="story-content">
                    <h2 class="story-title">قصة نجاحنا</h2>
                    <p class="story-text">
                        تأسست شركة RISEN في المملكة العربية السعودية كعلامة تجارية متخصصة في الأزياء،
                        وتمكنت من تحقيق الريادة في مجال الأزياء والأناقة مع التركيز على الجودة والتصميم العصري.
                    </p>
                    <p class="story-text">
                        تطورت الشركة من متجر محلي صغير إلى علامة تجارية رائدة، وتمكنت من الوصول إلى
                        العملاء في جميع أنحاء المملكة من خلال خدمات التوصيل المتميزة.
                    </p>
                    <p class="story-text">
                        نفتخر بكوننا شركة سعودية 100%، نفهم احتياجات عملائنا ونعمل على تلبية
                        توقعاتهم من خلال منتجات عالية الجودة وخدمة متميزة.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values Section -->
<section class="values-section fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">قيمنا الأساسية</h2>
            <p class="text-muted">المبادئ التي تقود رحلتنا نحو التميز</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4 class="value-title">الجودة المتميزة</h4>
                    <p class="value-description">
                        نلتزم بأعلى معايير الجودة في جميع منتجاتنا وخدماتنا،
                        لضمان رضا عملائنا الكرام.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4 class="value-title">الثقة والشفافية</h4>
                    <p class="value-description">
                        نبني علاقات طويلة الأمد مع عملائنا من خلال الشفافية
                        والصدق في جميع تعاملاتنا.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4 class="value-title">الابتكار المستمر</h4>
                    <p class="value-description">
                        نسعى دائماً لتطوير منتجاتنا وخدماتنا لتلبية
                        احتياجات عملائنا المتغيرة.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="value-title">رعاية العملاء</h4>
                    <p class="value-description">
                        نضع عملائنا في قلب كل ما نقوم به، ونقدم لهم
                        أفضل تجربة تسوق ممكنة.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h4 class="value-title">المسؤولية الاجتماعية</h4>
                    <p class="value-description">
                        نؤمن بأهمية المساهمة في تطوير مجتمعنا
                        ودعم المبادرات المحلية.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="value-title">العمل الجماعي</h4>
                    <p class="value-description">
                        نعمل كفريق واحد لتحقيق أهدافنا المشتركة
                        وتقديم أفضل النتائج لعملائنا.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="mission-section fade-in">
    <div class="container" >
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="mission-title">مهمتنا</h3>
                    <p class="mission-text">
                        نسعى لتقديم أفضل تجربة تسوق لعملائنا من خلال منتجات عالية الجودة
                        وخدمة متميزة، مع الحفاظ على الهوية السعودية الأصيلة والتصميم العصري.
                        نهدف لأن نكون الخيار الأول للأزياء في المملكة العربية السعودية.
                    </p>
                    <a href="{{ url('services') }}" class="btn btn-modern btn-primary-modern">
                        اكتشف خدماتنا
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
