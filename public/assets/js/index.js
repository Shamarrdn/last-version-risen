// Wait for document to be ready and make sure jQuery is available
document.addEventListener('DOMContentLoaded', function() {
    // Loading Screen
    setTimeout(() => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    }, 1500);

    // Navbar Scroll Effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (navbar) {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });

    // Smooth Scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Products Carousel - initialize only if jQuery and owl carousel are available
    if (typeof jQuery !== 'undefined') {
        // Use jQuery safely now
        jQuery(document).ready(function($) {
            if ($.fn.owlCarousel) {
                const $carousel = $('.products-carousel');
                if ($carousel.length) {
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
                }
            }
        });
    }

    // Scroll Animations
    const fadeObserverOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const fadeObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, fadeObserverOptions);

    document.querySelectorAll('.fade-in').forEach(el => {
        fadeObserver.observe(el);
    });

    // Enhanced Counter Animation
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2500;
        const startTime = performance.now();

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function for smooth animation
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.floor(target * easeOutQuart);

            element.textContent = current.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target.toLocaleString();
            }
        }

        requestAnimationFrame(updateCounter);
    }

    // Enhanced Stats Section Observer
    const statsObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('[data-count]');

                // Animate counters
                counters.forEach((counter, index) => {
                    setTimeout(() => {
                        animateCounter(counter);
                    }, index * 200);
                });

                // Add entrance animation to stat cards
                const statCards = entry.target.querySelectorAll('.stat-card');
                statCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(50px)';

                    setTimeout(() => {
                        card.style.transition = 'all 0.8s cubic-bezier(0.23, 1, 0.32, 1)';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 150);
                });

                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    // Product Card Hover Effects
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Mobile Menu Enhancement
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            const navbar = document.querySelector('.navbar');
            setTimeout(() => {
                if (document.querySelector('.navbar-collapse').classList.contains('show')) {
                    navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                } else {
                    navbar.style.background = '';
                }
            }, 100);
        });
    }

    // Parallax Effect for Hero Section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroBg = document.querySelector('.hero-bg');
        if (heroBg) {
            heroBg.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });

    // Product Image Hover Effect
    document.querySelectorAll('.product-image').forEach(image => {
        image.addEventListener('mouseenter', function() {
            const overlay = this.querySelector('.product-overlay');
            const actions = this.querySelectorAll('.action-btn');

            actions.forEach((btn, index) => {
                setTimeout(() => {
                    btn.style.transform = 'translateY(0) scale(1)';
                }, index * 100);
            });
        });

        image.addEventListener('mouseleave', function() {
            const actions = this.querySelectorAll('.action-btn');
            actions.forEach(btn => {
                btn.style.transform = 'translateY(20px) scale(0.8)';
            });
        });
    });

    // Add some interactive particles to hero section
    function createParticle() {
        const particle = document.createElement('div');
        particle.style.cssText = `
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(212, 184, 150, 0.3);
            border-radius: 50%;
            pointer-events: none;
            animation: float 15s linear infinite;
        `;

        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';

        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            heroSection.appendChild(particle);

            setTimeout(() => {
                particle.remove();
            }, 15000);
        }
    }

    // Create particles periodically
    setInterval(createParticle, 3000);

    console.log('RISEN E-commerce Website Loaded Successfully! 🚀');
});



