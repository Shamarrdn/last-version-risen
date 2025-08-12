<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم السوبر أدمن') - RISEN</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-layout.css') }}">

    <style>
        /* Custom styles for superadmin */
        .sidebar-logo {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent !important;
            font-weight: 700;
        }
        
        .sidebar {
            border-right: 4px solid #6a11cb;
        }
        
        .nav-link.active {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
        }
        
        .superadmin-badge {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
            margin-right: 5px;
        }
    </style>

    @yield('styles')
</head>
<body class="h-100">
    <div class="admin-layout">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar shadow-sm" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ url('/superadmin/dashboard') }}" class="sidebar-logo" style="white-space: normal; line-height: 1.2;">
                    <div>
                        RISEN <span class="superadmin-badge">SUPER</span>
                        <small class="d-block" style="font-size: 0.4em; letter-spacing: 4px;">BORN IN KSA</small>
                    </div>
                </a>
                <button class="d-lg-none btn btn-close" id="closeSidebar" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الرئيسية</div>
                    <div class="nav-item">
                        <a href="{{ url('/superadmin/dashboard') }}" class="nav-link {{ request()->is('superadmin/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-title">لوحة التحكم</span>
                        </a>
                    </div>
                </div>

                <!-- Admin Sales Reports Section -->
                <div class="nav-section">
                    <div class="nav-section-title">تقارير المشرفين</div>
                    <div class="nav-item">
                        <a href="{{ url('/superadmin/admin-sales-report') }}" class="nav-link {{ request()->is('superadmin/admin-sales-report') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i>
                            <span class="nav-title">تقارير مبيعات المشرفين</span>
                        </a>
                    </div>
                </div>

                <!-- User Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">إدارة المستخدمين</div>
                    <div class="nav-item">
                        <a href="{{ url('/superadmin/users') }}" class="nav-link {{ request()->is('superadmin/users*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span class="nav-title">إدارة المستخدمين</span>
                        </a>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="nav-section">
                    <div class="nav-section-title">المنتجات</div>
                    <div class="nav-item">
                        <a href="{{ route('superadmin.categories.index') }}" class="nav-link {{ request()->routeIs('superadmin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i>
                            <span class="nav-title">التصنيفات</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('superadmin.products.index') }}" class="nav-link {{ request()->routeIs('superadmin.products.index') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span class="nav-title">المنتجات</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('superadmin.products.inventory') }}" class="nav-link {{ request()->routeIs('superadmin.products.inventory') ? 'active' : '' }}">
                            <i class="fas fa-warehouse"></i>
                            <span class="nav-title">المخزون</span>
                        </a>
                    </div>
                </div>

                <!-- Coupons & Discounts Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الخصومات</div>
                    <div class="nav-item">
                        <a href="{{ route('superadmin.coupons.index') }}" class="nav-link {{ request()->routeIs('superadmin.coupons.*') ? 'active' : '' }}">
                            <i class="fas fa-ticket-alt"></i>
                            <span class="nav-title">كوبونات الخصم</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('superadmin.quantity-discounts.index') }}" class="nav-link {{ request()->routeIs('superadmin.quantity-discounts.*') ? 'active' : '' }}">
                            <i class="fas fa-percent"></i>
                            <span class="nav-title">خصومات الكمية</span>
                        </a>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الطلبات</div>
                    <div class="nav-item">
                        <a href="{{ route('superadmin.orders.index') }}" class="nav-link {{ request()->routeIs('superadmin.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-title">الطلبات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('superadmin.sales.statistics') }}" class="nav-link {{ request()->routeIs('superadmin.sales.statistics') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-title">إحصائيات المبيعات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('superadmin.tracking') }}" class="nav-link {{ request()->routeIs('superadmin.tracking*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-title">تتبع الأدمنز</span>
                        </a>
                    </div>
                </div>

                <!-- Reports Section -->
                <div class="nav-section">
                    <div class="nav-section-title">التقارير</div>
                    <div class="nav-item">
                        <a href="{{ route('superadmin.reports.index') }}" class="nav-link {{ request()->routeIs('superadmin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span class="nav-title">التقارير</span>
                        </a>
                    </div>
                </div>

                <!-- User Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">إدارة المستخدمين</div>
                    <div class="nav-item">
                        <a href="/user/profile" class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span class="nav-title">الملف الشخصي</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Mobile Toggle Button -->
        <button class="sidebar-toggle d-lg-none" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <main class="main-content-wrapper">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light glass-effect">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <div>
                            <h1 class="h4 mb-0 page-title text-truncate">@yield('page_title', 'لوحة تحكم السوبر أدمن')</h1>
                            <div class="page-subtitle">@yield('page_subtitle', 'مرحباً بك في لوحة تحكم السوبر أدمن')</div>
                        </div>
                    </div>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar" style="background: linear-gradient(45deg, #6a11cb, #2575fc);">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                                <span class="superadmin-badge ms-2">سوبر أدمن</span>
                            </a>
                            <div class="dropdown-menu position-absolute" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user-cog"></i>
                                    <span>الملف الشخصي</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-home"></i>
                                    <span>لوحة تحكم الأدمن</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>تسجيل الخروج</span>
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid fade-in">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- jQuery for AJAX functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fallback for jQuery if CDN fails
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
        }
        // Second fallback to local jQuery
        if (typeof jQuery === 'undefined') {
            document.write('<script src="{{ asset("js/jquery-3.6.0.min.js") }}"><\/script>');
        }
    </script>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fallback for Bootstrap if CDN fails
        if (typeof bootstrap === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"><\/script>');
        }
    </script>
    <script>
        // Initialize all dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl, {
                offset: [0, 10],
                placement: 'bottom-start'
            });
        });

        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const closeSidebar = document.getElementById('closeSidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        function closeSidebarFunc() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', closeSidebarFunc);
        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarFunc);
        }

        // Close sidebar on window resize if in mobile view
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992 && sidebar.classList.contains('active')) {
                closeSidebarFunc();
            }
        });

        // Add animation to dropdown items
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });

            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });

        // Add animation to cards
        document.querySelectorAll('.card').forEach(card => {
            card.classList.add('hover-card');
        });

        // Add animation to buttons
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.classList.add('btn-modern');
        });
        
        // Comprehensive JavaScript test for superadmin layout
        $(document).ready(function() {
            console.log('=== SUPERADMIN LAYOUT JAVASCRIPT LOADED ===');
            console.log('jQuery version:', $.fn.jquery);
            console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
            console.log('Modal buttons found:', $('[data-bs-toggle="modal"]').length);
            
            // Test each modal button
            $('[data-bs-toggle="modal"]').each(function(index) {
                console.log('Superadmin modal button', index + 1, ':', $(this).text().trim(), '->', $(this).data('bs-target'));
            });
            
            // Test modal button clicks
            $('[data-bs-toggle="modal"]').on('click', function(e) {
                console.log('=== SUPERADMIN: MODAL BUTTON CLICKED ===');
                console.log('Button text:', $(this).text().trim());
                console.log('Target modal:', $(this).data('bs-target'));
                console.log('Event type:', e.type);
                
                // Test if modal element exists
                const target = $(this).data('bs-target');
                const modalElement = document.querySelector(target);
                console.log('Modal element found:', modalElement !== null);
                
                if (modalElement) {
                    console.log('Modal element ID:', modalElement.id);
                    console.log('Modal classes:', modalElement.className);
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
