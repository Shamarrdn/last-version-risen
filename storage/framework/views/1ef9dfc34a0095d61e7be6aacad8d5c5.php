<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'RISEN'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/dashboard.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/layout.css')); ?>">
    <?php echo $__env->yieldContent('styles'); ?>
    <style>

    </style>
</head>

<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" type="button" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg glass-navbar sticky-top">
        <div class="container-fluid">
            <!-- Logo positioned at the beginning -->
            <a class="navbar-brand logo-container" href="/" style="white-space: normal; line-height: 1.2;">
                <div>
                    RISEN
                    <small class="d-block" style="font-size: 0.4em; letter-spacing: 4px;">BORN IN KSA</small>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-light btn-sm" href="#" id="mainMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-th-large ms-1"></i>القائمة الرئيسية
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="mainMenuDropdown">
                            <li><a class="dropdown-item <?php echo e(request()->is('/') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>">
                                <i class="fas fa-home ms-1"></i>الرئيسية
                            </a></li>
                            <li><a class="dropdown-item <?php echo e(request()->is('services*') ? 'active' : ''); ?>" href="/services">
                                <i class="fas fa-cogs ms-1"></i>خدماتنا
                            </a></li>
                            <li><a class="dropdown-item <?php echo e(request()->is('products*') ? 'active' : ''); ?>" href="/products">
                                <i class="fas fa-tshirt ms-1"></i>المنتجات
                            </a></li>
                            <li><a class="dropdown-item <?php echo e(request()->is('about*') ? 'active' : ''); ?>" href="/about">
                                <i class="fas fa-info-circle ms-1"></i>من نحن
                            </a></li>
                            <li><a class="dropdown-item <?php echo e(request()->is('contact*') ? 'active' : ''); ?>" href="/contact">
                                <i class="fas fa-envelope ms-1"></i>تواصل معنا
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-light btn-sm" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user ms-1"></i>حسابي
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item <?php echo e(request()->is('dashboard*') ? 'active' : ''); ?>" href="/dashboard">
                                <i class="fas fa-tachometer-alt ms-1"></i>لوحة التحكم
                            </a></li>
                            <li><a class="dropdown-item <?php echo e(request()->is('profile*') ? 'active' : ''); ?>" href="/user/profile">
                                <i class="fas fa-user-circle ms-1"></i>الملف الشخصي
                            </a></li>
                            <li><a class="dropdown-item <?php echo e(request()->is('orders*') ? 'active' : ''); ?>" href="/orders">
                                <i class="fas fa-clipboard-list ms-1"></i>طلباتي
                            </a></li>
                        </ul>
                    </li>
                </ul>
                <div class="nav-buttons d-flex align-items-center">
                    <a href="/cart" class="btn btn-link position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if($stats['cart_items_count'] > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo e($stats['cart_items_count']); ?>

                        </span>
                        <?php endif; ?>
                    </a>
                    <a href="/notifications" class="btn btn-link position-relative ms-2">
                        <i class="fas fa-bell"></i>
                        <?php if($stats['unread_notifications'] > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo e($stats['unread_notifications']); ?>

                        </span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo e(route('logout')); ?>" class="btn btn-outline-primary ms-3"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt ms-1"></i>تسجيل الخروج
                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-user-info">
            <h5 class="mb-2"><?php echo e(Auth::user()->name); ?></h5>
            <span class="badge bg-primary"><?php echo e(Auth::user()->role === 'admin' ? 'مدير' : 'عميل'); ?></span>
        </div>
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>" href="/dashboard">
                        <i class="fas fa-home"></i>
                        لوحة التحكم
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('products*') ? 'active' : ''); ?>" href="/products">
                        <i class="fas fa-shopping-bag"></i>
                        المنتجات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('orders*') ? 'active' : ''); ?>" href="/orders">
                        <i class="fas fa-clipboard-list"></i>
                        الطلبات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('cart*') ? 'active' : ''); ?>" href="/cart">
                        <i class="fas fa-shopping-cart"></i>
                        السلة
                        <?php if($stats['cart_items_count'] > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo e($stats['cart_items_count']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('notifications*') ? 'active' : ''); ?>" href="/notifications">
                        <i class="fas fa-bell"></i>
                        الإشعارات
                        <?php if($stats['unread_notifications'] > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo e($stats['unread_notifications']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('profile*') ? 'active' : ''); ?>" href="/user/profile">
                        <i class="fas fa-user"></i>
                        الملف الشخصي
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="<?php echo e(route('logout')); ?>"
                        onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        تسجيل الخروج
                    </a>
                    <form id="logout-form-sidebar" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                        <?php echo csrf_field(); ?>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // CSRF token setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize Bootstrap components and functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Enable all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            // Add touch-friendly behavior
            if ('ontouchstart' in document.documentElement) {
                $('.dropdown-toggle').on('click', function(e) {
                    // For touch devices, prevent clicks from navigating
                    // Allow the dropdown to toggle instead
                    e.preventDefault();
                });
            }

            // Sidebar functionality
            const sidebar = $('.sidebar');
            const sidebarToggle = $('.sidebar-toggle');
            const mainContent = $('.main-content');
            const navbarHeight = $('.glass-navbar').outerHeight();

            // Set initial sidebar position
            sidebar.css('top', navbarHeight + 'px');
            sidebar.css('height', 'calc(100vh - ' + navbarHeight + 'px)');

            // Toggle sidebar
            sidebarToggle.on('click', function(e) {
                e.stopPropagation();
                sidebar.toggleClass('show');

                // Adjust main content margin for desktop
                if ($(window).width() >= 992) {
                    if (sidebar.hasClass('show')) {
                        mainContent.css('margin-right', '280px');
                    } else {
                        mainContent.css('margin-right', '0');
                    }
                }
            });

            // Close sidebar when clicking outside on mobile
            $(document).on('click', function(e) {
                if ($(window).width() < 992) {
                    if (!$(e.target).closest('.sidebar').length && !$(e.target).closest('.sidebar-toggle').length) {
                        sidebar.removeClass('show');
                    }
                }
            });

            // Handle window resize
            $(window).resize(function() {
                const newNavbarHeight = $('.glass-navbar').outerHeight();
                sidebar.css('top', newNavbarHeight + 'px');
                sidebar.css('height', 'calc(100vh - ' + newNavbarHeight + 'px)');

                if ($(window).width() >= 992) {
                    sidebar.removeClass('show');
                    mainContent.css('margin-right', '280px');
                } else {
                    mainContent.css('margin-right', '0');
                }
            });
        });
    </script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/layouts/customer.blade.php ENDPATH**/ ?>