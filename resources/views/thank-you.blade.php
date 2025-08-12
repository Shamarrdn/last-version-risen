@extends('layouts.app')

@section('title', 'شكراً لك - RISEN')

@section('page-css')
<style>
    .thank-you-hero {
        min-height: 100vh;
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .thank-you-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .thank-you-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .success-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 3rem;
        color: white;
        animation: pulse 2s infinite;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(40, 167, 69, 0.5);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        }
    }

    .thank-you-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 700;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #ffffff, #e9ecef);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .thank-you-subtitle {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
    }

    .thank-you-description {
        font-size: 1.1rem;
        margin-bottom: 3rem;
        opacity: 0.8;
        line-height: 1.8;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }

    .btn-modern {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #000000, #333333);
        color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        color: white;
    }

    .btn-outline-modern {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-outline-modern:hover {
        background: white;
        color: #000000;
        transform: translateY(-2px);
    }

    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 4rem;
    }

    .info-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: transform 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-5px);
    }

    .info-card-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .info-card-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: white;
    }

    .info-card-text {
        font-size: 0.9rem;
        opacity: 0.8;
        line-height: 1.5;
    }

    .floating-elements {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 1;
    }

    .floating-element {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .floating-element:nth-child(1) {
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .floating-element:nth-child(2) {
        top: 60%;
        right: 15%;
        animation-delay: 2s;
    }

    .floating-element:nth-child(3) {
        bottom: 30%;
        left: 20%;
        animation-delay: 4s;
    }

    .floating-element:nth-child(4) {
        top: 40%;
        right: 30%;
        animation-delay: 1s;
    }

    .floating-element:nth-child(5) {
        bottom: 20%;
        right: 10%;
        animation-delay: 3s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
            opacity: 0.3;
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
            opacity: 0.8;
        }
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }

        .btn-modern {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }

        .info-cards {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<section class="thank-you-hero">
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="container">
        <div class="thank-you-content " style="margin-top: 100px;">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>

            <h1 class="thank-you-title">شكراً لك!</h1>
            <h2 class="thank-you-subtitle">تم إرسال رسالتك بنجاح</h2>
            <p class="thank-you-description">
                نعتذر عن رسالتك ونقدر ثقتك فينا. سيقوم فريقنا المختص بالرد عليك في أقرب وقت ممكن.
                نحرص على تقديم أفضل خدمة لعملائنا الكرام.
            </p>

            <div class="action-buttons">
                <a href="{{ url('/') }}" class="btn-modern btn-primary-modern">
                    <i class="fas fa-home"></i>
                    العودة للرئيسية
                </a>
                <a href="{{ url('/products') }}" class="btn-modern btn-outline-modern">
                    <i class="fas fa-shopping-bag"></i>
                    تصفح المنتجات
                </a>
            </div>

            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="info-card-title">وقت الاستجابة</h3>
                    <p class="info-card-text">نرد على جميع الرسائل خلال 24 ساعة عمل</p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="info-card-title">خدمة العملاء</h3>
                    <p class="info-card-text">فريق متخصص لمساعدتك في أي استفسار</p>
                </div>


            </div>
        </div>
    </div>
</section>
@endsection

@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add entrance animation
    const elements = document.querySelectorAll('.success-icon, .thank-you-title, .thank-you-subtitle, .thank-you-description, .action-buttons, .info-cards');

    elements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';

        setTimeout(() => {
            element.style.transition = 'all 0.8s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 200);
    });

    // Add confetti effect
    createConfetti();
});

function createConfetti() {
    const colors = ['#28a745', '#20c997', '#ffffff', '#e9ecef'];
    const confettiCount = 50;

    for (let i = 0; i < confettiCount; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.borderRadius = '50%';
            confetti.style.pointerEvents = 'none';
            confetti.style.zIndex = '9999';
            confetti.style.animation = 'fall 3s linear forwards';

            document.body.appendChild(confetti);

            setTimeout(() => {
                confetti.remove();
            }, 3000);
        }, i * 100);
    }
}

// Add CSS for confetti animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fall {
        to {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection
