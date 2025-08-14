<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - RISEN</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-layout.css') }}">

    @yield('styles')
</head>
<body class="h-100">
    <div class="admin-layout">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar shadow-sm" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-logo" style="white-space: normal; line-height: 1.2;">
                    <div>
                        RISEN
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
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span class="nav-title">لوحة التحكم</span>
                        </a>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="nav-section">
                    <div class="nav-section-title">المنتجات</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i>
                            <span class="nav-title">التصنيفات</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span class="nav-title">المنتجات</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('admin.products.inventory') }}" class="nav-link {{ request()->routeIs('admin.products.inventory') ? 'active' : '' }}">
                            <i class="fas fa-warehouse"></i>
                            <span class="nav-title">المخزون</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.sizes-colors.index') }}" class="nav-link {{ request()->routeIs('admin.sizes-colors.*') ? 'active' : '' }}">
                            <i class="fas fa-palette"></i>
                            <span class="nav-title">الألوان والمقاسات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.sales.statistics') }}" class="nav-link {{ request()->routeIs('admin.sales.statistics') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-title">إحصائيات المبيعات</span>
                        </a>
                    </div>

                </div>

                <!-- Coupons & Discounts Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الخصومات</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                            <i class="fas fa-ticket-alt"></i>
                            <span class="nav-title">كوبونات الخصم</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.quantity-discounts.index') }}" class="nav-link {{ request()->routeIs('admin.quantity-discounts.*') ? 'active' : '' }}">
                            <i class="fas fa-percent"></i>
                            <span class="nav-title">خصومات الكمية</span>
                        </a>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الطلبات</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-title">الطلبات</span>
                        </a>
                    </div>
                                            <div class="nav-item">
                            <a href="{{ route('admin.orders.assigned') }}" class="nav-link {{ request()->routeIs('admin.orders.assigned') ? 'active' : '' }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-title">طلباتي المخصصة</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ route('admin.orders.unassigned') }}" class="nav-link {{ request()->routeIs('admin.orders.unassigned') ? 'active' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span class="nav-title">الطلبات غير المخصصة</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ route('admin.transfers.index') }}" class="nav-link {{ request()->routeIs('admin.transfers.*') ? 'active' : '' }}">
                                <i class="fas fa-exchange-alt"></i>
                                <span class="nav-title">نقل الطلبات</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ route('admin.orders.invited') }}" class="nav-link {{ request()->routeIs('admin.orders.invited*') ? 'active' : '' }}">
                                <i class="fas fa-user-friends"></i>
                                <span class="nav-title">الطلبات المدعوة</span>
                            </a>
                        </div>
                </div>

                <!-- Reports Section -->
                <div class="nav-section">
                    <div class="nav-section-title">التقارير</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span class="nav-title">التقارير</span>
                        </a>
                    </div>

                </div>

                <!-- User Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">إدارة المستخدمين</div>
                    <div class="nav-item">
                        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
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
                            <h1 class="h4 mb-0 page-title text-truncate">@yield('page_title', 'لوحة التحكم')</h1>
                            <div class="page-subtitle">@yield('page_subtitle', 'مرحباً بك في لوحة التحكم')</div>
                        </div>
                    </div>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown position-static">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <div class="dropdown-menu position-absolute" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user-cog"></i>
                                    <span>الملف الشخصي</span>
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
    
    <!-- Custom CSS for modals -->
    <style>
        .modal-backdrop {
            z-index: 1040 !important;
        }
        .modal {
            z-index: 1050 !important;
        }
        .modal-dialog {
            z-index: 1055 !important;
        }
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }
        .modal.fade .modal-dialog {
            transition: transform .3s ease-out;
            transform: translate(0,-50px);
        }
        .modal.show .modal-dialog {
            transform: none;
        }
    </style>
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
        
        // Comprehensive JavaScript test
        $(document).ready(function() {
            console.log('=== LAYOUT JAVASCRIPT LOADED ===');
            console.log('jQuery version:', $.fn.jquery);
            console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
            console.log('Modal buttons found:', $('[data-bs-toggle="modal"]').length);
            
            // Test each modal button
            $('[data-bs-toggle="modal"]').each(function(index) {
                console.log('Modal button', index + 1, ':', $(this).text().trim(), '->', $(this).data('bs-target'));
            });
            
            // Test modal button clicks
            $('[data-bs-toggle="modal"]').on('click', function(e) {
                console.log('=== LAYOUT: MODAL BUTTON CLICKED ===');
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
