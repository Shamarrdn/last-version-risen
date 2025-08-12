@extends('layouts.app')

@section('title', 'تواصل معنا - RISEN')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/css/clothes-platform/contact.css') }}?t={{ time() }}">
@endsection

@section('content')
<!-- Hero Section -->
<section class="contact-hero-section">
    <div class="container">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="hero-title">تواصل معنا</h1>
                <p class="hero-description">
                    نحن هنا للإجابة على استفساراتك ومساعدتك في كل ما تحتاجه. يمكنك التواصل معنا من خلال النموذج أدناه أو عبر وسائل الاتصال المختلفة.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="contact-info-section fade-in">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4 class="contact-info-title">العنوان</h4>
                    <p class="contact-info-text">الرياض، المملكة العربية السعودية<br>شارع الملك فهد، برج المملكة</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h4 class="contact-info-title">اتصل بنا</h4>
                    <p class="contact-info-text">+966 11 123 4567<br>+966 11 765 4321</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 class="contact-info-title">البريد الإلكتروني</h4>
                    <p class="contact-info-text">info@risen.com.sa<br>support@risen.com.sa</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact-form-section fade-in">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-container">
                    <h2 class="form-title">أرسل لنا رسالة</h2>
                    <p class="form-subtitle">نحن نقدر تواصلك معنا ونرد عليك في أقرب وقت ممكن</p>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" id="contactForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">الاسم الكامل</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="أدخل اسمك الكامل" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="أدخل بريدك الإلكتروني" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="أدخل رقم هاتفك" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject" class="form-label">الموضوع</label>
                                    <select class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject">
                                        <option value="" selected disabled>اختر الموضوع</option>
                                        <option value="استفسار عام" {{ old('subject') == 'استفسار عام' ? 'selected' : '' }}>استفسار عام</option>
                                        <option value="استفسار عن منتج" {{ old('subject') == 'استفسار عن منتج' ? 'selected' : '' }}>استفسار عن منتج</option>
                                        <option value="طلب المساعدة" {{ old('subject') == 'طلب المساعدة' ? 'selected' : '' }}>طلب المساعدة</option>
                                        <option value="اقتراح" {{ old('subject') == 'اقتراح' ? 'selected' : '' }}>اقتراح</option>
                                        <option value="أخرى" {{ old('subject') == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="message" class="form-label">رسالتك</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="اكتب رسالتك هنا" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-modern">
                                    <i class="fas fa-paper-plane me-2"></i> إرسال الرسالة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="contact-image-container">
                    <div class="contact-image"></div>
                    <div class="contact-social">
                        <h4 class="social-title">تابعنا على منصات التواصل الاجتماعي</h4>
                        <div class="social-icons">
                            <a href="#" class="social-contact-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-contact-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-contact-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-contact-link">
                                <i class="fab fa-snapchat"></i>
                            </a>
                            <a href="#" class="social-contact-link">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section fade-in">
    <div class="container">
        <div class="newsletter-container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h3 class="newsletter-title">اشترك في نشرتنا الإخبارية</h3>
                    <p class="newsletter-text">احصل على آخر العروض والأخبار عن أحدث منتجاتنا</p>
                </div>
                <div class="col-lg-7">
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="أدخل بريدك الإلكتروني" aria-label="البريد الإلكتروني">
                            <button class="btn btn-primary-modern" type="submit">اشترك الآن</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section fade-in">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">الأسئلة الشائعة</h2>
            <p class="text-muted">إجابات على استفساراتك الشائعة</p>
        </div>

        <div class="accordion" id="faqAccordion">
            <!-- FAQ Item 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        ما هي طرق الدفع المتاحة؟
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        نوفر العديد من طرق الدفع المريحة والآمنة، بما في ذلك بطاقات الائتمان (فيزا وماستركارد)، مدى، آبل باي، والدفع عند الاستلام في بعض المناطق.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        ما هي سياسة الإرجاع والاستبدال؟
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        يمكنك إرجاع أو استبدال المنتجات غير المستخدمة في غضون 14 يومًا من تاريخ الاستلام، مع ضرورة الاحتفاظ بالفاتورة وأن تكون المنتجات بحالتها الأصلية مع جميع الملصقات والعلامات.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        كم تستغرق عملية التوصيل؟
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        يتم توصيل الطلبات داخل المدن الرئيسية خلال 2-3 أيام عمل، أما المناطق الأخرى فتستغرق 3-5 أيام عمل. يمكنك تتبع طلبك من خلال رقم التتبع الذي سيصلك عبر البريد الإلكتروني ورسائل SMS.
                    </div>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        هل توفرون خدمة الشحن الدولي؟
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        نعم، نوفر خدمة الشحن الدولي إلى معظم دول العالم. تختلف رسوم الشحن والمدة حسب الوجهة، ويمكنك معرفة التفاصيل عند إتمام عملية الشراء.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('page-scripts')
<script src="{{ asset('assets/js/contact.js') }}?t={{ time() }}"></script>
@endsection
