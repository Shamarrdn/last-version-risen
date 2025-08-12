<!-- Loading Screen -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-logo">RISEN</div>
</div>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}" style="white-space: normal; line-height: 1.2;">
            <div>
                RISEN
                <small class="d-block" style="font-size: 0.4em; letter-spacing: 4px;">BORN IN KSA</small>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}" href="{{ url('/products') }}">المنتجات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('services*') ? 'active' : '' }}" href="{{ url('services') }}">الخدمات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('about*') ? 'active' : '' }}" href="{{ url('about') }}">من نحن</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact*') ? 'active' : '' }}" href="{{ url('contact') }}">تواصل معنا</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <!-- Profile Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-icon dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        @guest
                            <li><a class="dropdown-item" href="{{ route('login') }}"><i class="fas fa-sign-in-alt fa-fw me-2"></i>تسجيل دخول</a></li>
                            <li><a class="dropdown-item" href="{{ route('register') }}"><i class="fas fa-user-plus fa-fw me-2"></i>تسجيل حساب</a></li>
                        @else
                            <li><a class="dropdown-item" href="{{ url('/dashboard') }}"><i class="fas fa-tachometer-alt fa-fw me-2"></i>لوحة التحكم</a></li>
                            <li><a class="dropdown-item" href="{{ url('/user/profile') }}"><i class="fas fa-user-circle fa-fw me-2"></i>الملف الشخصي</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="fas fa-clipboard-list fa-fw me-2"></i>طلباتي</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form-nav">
                                    @csrf
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                                        <i class="fas fa-sign-out-alt fa-fw me-2"></i>تسجيل الخروج
                                    </a>
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>

                <a href="{{ url('/cart') }}" class="nav-icon cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    @auth
                        @php
                            $cartItemCount = \App\Models\CartItem::whereHas('cart', function ($query) {
                                $query->where('user_id', Auth::id());
                            })->sum('quantity');
                        @endphp
                        <span class="cart-badge">{{ (int) $cartItemCount }}</span>
                    @else
                        <span class="cart-badge">0</span>
                    @endauth
                </a>
            </div>
        </div>
    </div>
</nav>
