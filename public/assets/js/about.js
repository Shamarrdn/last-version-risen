// Animate elements when they come into view
document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe value cards
    document.querySelectorAll('.value-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        observer.observe(card);
    });

    // Observe team cards
    document.querySelectorAll('.team-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        observer.observe(card);
    });

    // Observe stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        observer.observe(card);
    });

    // Add new effects
    createMouseTrail();
    createScrollAnimations();
    createParallaxEffect();
    createTiltEffect();

    // Fix stats observer
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }
});

// Animate stats counter
const animateValue = (element, start, end, duration) => {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.innerHTML = value + (element.dataset.suffix || '');
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
};

// Start counter animation when stats section comes into view
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            document.querySelectorAll('.stat-number').forEach(stat => {
                const endValue = parseInt(stat.textContent);
                animateValue(stat, 0, endValue, 2000);
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

document.querySelector('.stats-section').forEach(section => {
    statsObserver.observe(section);
});

// Smooth scroll for anchor links
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

// Add parallax effect to hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroSection = document.querySelector('.about-hero');
    if (heroSection) {
        heroSection.style.backgroundPositionY = scrolled * 0.5 + 'px';
    }
});

// Mouse trail effect
const createMouseTrail = () => {
    const trails = Array.from({ length: 10 }, (_, i) => {
        const trail = document.createElement('div');
        trail.className = 'mouse-trail';
        trail.style.opacity = (1 - i * 0.1).toString();
        document.body.appendChild(trail);
        return trail;
    });

    let mouseX = 0;
    let mouseY = 0;
    let trailPositions = trails.map(() => ({ x: 0, y: 0 }));

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });

    function updateTrails() {
        trails.forEach((trail, index) => {
            const target = index === 0 ?
                { x: mouseX, y: mouseY } :
                trailPositions[index - 1];

            trailPositions[index].x += (target.x - trailPositions[index].x) * 0.2;
            trailPositions[index].y += (target.y - trailPositions[index].y) * 0.2;

            trail.style.transform = `translate(${trailPositions[index].x}px, ${trailPositions[index].y}px)`;
        });
        requestAnimationFrame(updateTrails);
    }

    trailPositions = trails.map(() => ({ x: mouseX, y: mouseY }));
    updateTrails();
};

// Enhanced scroll animations
const createScrollAnimations = () => {
    const elements = document.querySelectorAll('.value-card, .team-card, .stat-card');

    elements.forEach(element => {
        element.addEventListener('mouseenter', () => {
            element.style.transform = 'scale(1.02) translateY(-5px)';
            element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'scale(1) translateY(0)';
        });
    });
};

// Parallax effect for hero section
const createParallaxEffect = () => {
    const hero = document.querySelector('.about-hero');
    const heroContent = document.querySelector('.hero-content');
    const heroImage = document.querySelector('.hero-image');

    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * 0.35;

        if (hero && heroContent && heroImage) {
            heroContent.style.transform = `translateY(${rate}px)`;
            heroImage.style.transform = `translateY(${rate * 0.8}px)`;
        }
    });
};

// Tilt effect for cards
const createTiltEffect = () => {
    const cards = document.querySelectorAll('.value-card, .team-card');

    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
        });
    });
};

// Image loading handler
document.addEventListener('DOMContentLoaded', function() {
    const heroImage = document.querySelector('.hero-image img');

    if (heroImage) {
        // Add loaded class immediately if image is cached
        if (heroImage.complete) {
            heroImage.classList.add('loaded');
        }

        // Add loaded class when image loads
        heroImage.addEventListener('load', function() {
            heroImage.classList.add('loaded');
        });
    }
});
