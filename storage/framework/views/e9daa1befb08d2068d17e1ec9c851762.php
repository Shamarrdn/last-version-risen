<!-- Loading Screen -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-logo">RISEN</div>
</div>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(url('/')); ?>" style="white-space: normal; line-height: 1.2;">
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
                    <a class="nav-link <?php echo e(request()->is('/') ? 'active' : ''); ?>" href="<?php echo e(url('/')); ?>">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('products*') ? 'active' : ''); ?>" href="<?php echo e(url('/products')); ?>">المنتجات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('services*') ? 'active' : ''); ?>" href="<?php echo e(url('services')); ?>">الخدمات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('about*') ? 'active' : ''); ?>" href="<?php echo e(url('about')); ?>">من نحن</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('contact*') ? 'active' : ''); ?>" href="<?php echo e(url('contact')); ?>">تواصل معنا</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <!-- Profile Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-icon dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <?php if(auth()->guard()->guest()): ?>
                            <li><a class="dropdown-item" href="<?php echo e(route('login')); ?>"><i class="fas fa-sign-in-alt fa-fw me-2"></i>تسجيل دخول</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('register')); ?>"><i class="fas fa-user-plus fa-fw me-2"></i>تسجيل حساب</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="<?php echo e(url('/dashboard')); ?>"><i class="fas fa-tachometer-alt fa-fw me-2"></i>لوحة التحكم</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(url('/user/profile')); ?>"><i class="fas fa-user-circle fa-fw me-2"></i>الملف الشخصي</a></li>
                            <li><a class="dropdown-item" href="<?php echo e(route('orders.index')); ?>"><i class="fas fa-clipboard-list fa-fw me-2"></i>طلباتي</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>" id="logout-form-nav">
                                    <?php echo csrf_field(); ?>
                                    <a class="dropdown-item text-danger" href="<?php echo e(route('logout')); ?>"
                                       onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                                        <i class="fas fa-sign-out-alt fa-fw me-2"></i>تسجيل الخروج
                                    </a>
                                </form>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <a href="<?php echo e(url('/cart')); ?>" class="nav-icon cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(auth()->guard()->check()): ?>
                        <?php
                            $cartItemCount = \App\Models\CartItem::whereHas('cart', function ($query) {
                                $query->where('user_id', Auth::id());
                            })->sum('quantity');
                        ?>
                        <span class="cart-badge"><?php echo e((int) $cartItemCount); ?></span>
                    <?php else: ?>
                        <span class="cart-badge">0</span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/parts/navbar.blade.php ENDPATH**/ ?>