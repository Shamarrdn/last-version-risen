<?php $__env->startSection('title', 'لوحة التحكم'); ?>

<?php $__env->startSection('page_title', 'لوحة التحكم'); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Grid -->
<div class="row g-3 mb-4">
    <!-- Orders Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper bg-primary me-3">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value h4 mb-1"><?php echo e($stats['orders'] ?? 0); ?></div>
                    <div class="stat-title text-muted">إجمالي الطلبات</div>
                    <div class="trend small mt-2">
                        <span class="me-2">اليوم: <?php echo e($stats['today_orders'] ?? 0); ?></span>
                        <span>الشهر: <?php echo e($stats['month_orders'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper bg-success me-3">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value h4 mb-1"><?php echo e($stats['revenue']); ?> ريال</div>
                    <div class="stat-title text-muted">إجمالي الإيرادات</div>
                    <div class="trend small mt-2">
                        <span>اليوم: <?php echo e($stats['today_revenue']); ?> ريال</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper bg-info me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value h4 mb-1"><?php echo e($stats['users']); ?></div>
                    <div class="stat-title text-muted">إجمالي المستخدمين</div>
                    <div class="trend small mt-2">
                        <span>المستخدمين النشطين</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper bg-warning me-3">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value h4 mb-1"><?php echo e($stats['pending_orders'] ?? 0); ?></div>
                    <div class="stat-title text-muted">الطلبات المعلقة</div>
                    <div class="trend small mt-2">
                        <span class="me-2">قيد المعالجة: <?php echo e($stats['processing_orders'] ?? 0); ?></span>
                        <span>مكتملة: <?php echo e($stats['completed_orders'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Status Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper bg-primary me-3">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value h4 mb-1"><?php echo e($stats['out_for_delivery_orders'] ?? 0); ?></div>
                    <div class="stat-title text-muted">قيد التوصيل</div>
                    <div class="trend small mt-2">
                        <span class="me-2">في الطريق: <?php echo e($stats['on_the_way_orders'] ?? 0); ?></span>
                        <span>تم التوصيل: <?php echo e($stats['delivered_orders'] ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Product::class)): ?>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="action-card bg-white rounded-3 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center justify-content-end">
                <div class="action-icon bg-primary ms-3">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="text-end">
                    <h5 class="mb-1">إضافة منتج</h5>
                    <p class="mb-0 text-muted small">إضافة منتجات جديدة للمتجر</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Order::class)): ?>
    <div class="col-12 col-md-6 col-lg-4">
        <div class="action-card bg-white rounded-3 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center justify-content-end">
                <div class="action-icon bg-info ms-3">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="text-end">
                    <h5 class="mb-1">إدارة الطلبات</h5>
                    <p class="mb-0 text-muted small">عرض وإدارة طلبات العملاء</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage products')): ?>
    <div class="col-12 col-md-6 col-lg-4">
        <a href="<?php echo e(route('admin.coupons.index')); ?>" class="action-card bg-white rounded-3 shadow-sm p-3 h-100 d-block text-decoration-none">
            <div class="d-flex align-items-center justify-content-end">
                <div class="action-icon bg-warning ms-3">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="text-end">
                    <h5 class="mb-1">كوبونات الخصم</h5>
                    <p class="mb-0 text-muted small">إدارة كوبونات الخصم للعملاء</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a href="<?php echo e(route('admin.quantity-discounts.index')); ?>" class="action-card bg-white rounded-3 shadow-sm p-3 h-100 d-block text-decoration-none">
            <div class="d-flex align-items-center justify-content-end">
                <div class="action-icon bg-success ms-3">
                    <i class="fas fa-percent"></i>
                </div>
                <div class="text-end">
                    <h5 class="mb-1">خصومات الكمية</h5>
                    <p class="mb-0 text-muted small">إدارة خصومات الكميات الكبيرة</p>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Charts & Tables -->
<div class="row g-4">
    <!-- Sales Chart -->
    <div class="col-12 col-lg-8">
        <div class="chart-container bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom">
                <h5 class="activity-title">نظرة عامة على المبيعات</h5>
            </div>
            <div class="chart-wrapper position-relative" style="height: 300px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Order Status Chart -->
    <div class="col-12 col-lg-4">
        <div class="chart-container bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom">
                <h5 class="activity-title">توزيع حالات الطلبات</h5>
            </div>
            <div class="chart-wrapper position-relative" style="height: 300px;">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="activity-section bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="activity-title mb-0">آخر الطلبات</h5>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', App\Models\Order::class)): ?>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-sm btn-primary">عرض الكل</a>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 recent-orders-table">
                    <thead>
                        <tr>
                            <th style="min-width: 70px">الطلب</th>
                            <th style="min-width: 120px">العميل</th>
                            <th style="min-width: 250px">المنتجات</th>
                            <th style="min-width: 120px">حالة الطلب</th>
                            <th style="min-width: 120px">حالة الدفع</th>
                            <th style="min-width: 100px">السعر الأصلي</th>
                            <th style="min-width: 100px">خصم الكوبون</th>
                            <th style="min-width: 100px">خصم الكمية</th>
                            <th style="min-width: 100px">المبلغ الإجمالي</th>
                            <th style="min-width: 120px">التاريخ</th>
                            <th style="min-width: 80px">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td data-label="الطلب">#<?php echo e($order['order_number']); ?></td>
                            <td data-label="العميل"><?php echo e($order['user_name']); ?></td>
                            <td data-label="المنتجات">
                                <div class="small products-list">
                                    <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="mb-1">
                                            <?php echo e($item['product_name']); ?>

                                            <span class="text-muted d-block d-md-inline">
                                                <?php if(isset($item['unit_price']) && isset($item['total_price'])): ?>
                                                    (<?php echo e($item['quantity']); ?> × <?php echo e(number_format($item['unit_price'])); ?> ريال = <?php echo e(number_format($item['total_price'])); ?> ريال)
                                                <?php else: ?>
                                                    (<?php echo e($item['quantity']); ?> قطعة)
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </td>
                            <td data-label="حالة الطلب">
                                <span class="badge bg-<?php echo e($order['status_color']); ?>">
                                    <?php echo e($order['status_text']); ?>

                                </span>
                            </td>
                            <td data-label="حالة الدفع">
                                <span class="badge bg-<?php echo e($order['payment_status_color']); ?>">
                                    <?php echo e($order['payment_status_text']); ?>

                                </span>
                            </td>
                            <td data-label="السعر الأصلي"><?php echo e(number_format($order['original_amount'] ?? $order['total'])); ?> ريال</td>
                            <td data-label="خصم الكوبون"><?php echo e(number_format($order['coupon_discount'] ?? 0)); ?> ريال</td>
                            <td data-label="خصم الكمية"><?php echo e(number_format($order['quantity_discount'] ?? 0)); ?> ريال</td>
                            <td data-label="المبلغ الإجمالي"><?php echo e(number_format($order['total_amount'] ?? $order['total'])); ?> ريال</td>
                            <td data-label="التاريخ"><?php echo e($order['created_at']); ?></td>
                            <td data-label="الإجراءات">
                                <a href="<?php echo e(route('admin.orders.show', $order['uuid'])); ?>"
                                   class="btn btn-sm btn-primary"
                                   title="عرض تفاصيل الطلب">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="text-center py-3">لا توجد طلبات حديثة</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>
<script>
    try {
        firebase.initializeApp({
            apiKey: "<?php echo e(config('services.firebase.api_key')); ?>",
            authDomain: "<?php echo e(config('services.firebase.auth_domain')); ?>",
            projectId: "<?php echo e(config('services.firebase.project_id')); ?>",
            storageBucket: "<?php echo e(config('services.firebase.storage_bucket')); ?>",
            messagingSenderId: "<?php echo e(config('services.firebase.messaging_sender_id')); ?>",
            appId: "<?php echo e(config('services.firebase.app_id')); ?>"
        });
    } catch (error) {
        // Silent error in production
    }

    const messaging = firebase.messaging();

    async function requestPermissionAndToken() {
        try {
            if (!('serviceWorker' in navigator)) {
                throw new Error('Service Worker not supported');
            }
            if (!('PushManager' in window)) {
                throw new Error('Push notifications not supported');
            }

            const permission = await Notification.requestPermission();

            if (permission === 'granted') {
                const registration = await navigator.serviceWorker.register('/admin/firebase-messaging-sw.js');

                try {
                    messaging.useServiceWorker(registration);

                    const currentToken = await messaging.getToken();

                    if (currentToken) {
                        updateFcmToken(currentToken);
                        return currentToken;
                    } else {
                        return null;
                    }
                } catch (tokenError) {
                    return null;
                }
            }
        } catch (err) {
            return null;
        }
    }

    // التعامل مع الرسائل في الواجهة الأمامية
    messaging.onMessage((payload) => {
        try {
            const notification = new Notification(payload.notification.title, {
                body: payload.notification.body,
                vibrate: [100, 50, 100],
                requireInteraction: true,
                dir: 'rtl',
                lang: 'ar',
                tag: Date.now().toString(),
                data: payload.data
            });

            notification.onclick = function(event) {
                event.preventDefault();
                window.focus();
                notification.close();

                // فتح صفحة الطلب في نفس النافذة
                if (payload.data && payload.data.link) {
                    window.location.href = payload.data.link;
                }

                // تحديث الصفحة إذا كنا بالفعل في صفحة الطلبات
                if (window.location.pathname.includes('/admin/orders')) {
                    window.location.reload();
                }
            };
        } catch (error) {
            // محاولة استخدام Service Worker كخطة بديلة
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                navigator.serviceWorker.ready.then(registration => {
                    return registration.showNotification(payload.notification.title, {
                        body: payload.notification.body,
                        vibrate: [100, 50, 100],
                        requireInteraction: true,
                        dir: 'rtl',
                        lang: 'ar',
                        tag: Date.now().toString(),
                        data: payload.data
                    });
                }).catch(error => {
                    // Silent error in production
                });
            }
        }
    });

    // تحديث FCM token في قاعدة البيانات
    function updateFcmToken(token) {
        fetch('/admin/update-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ token })
        })
        .then(response => response.json())
        .then(data => {
            // Success, no logging needed
        })
        .catch(error => {
            // Silent error in production
        });
    }

    // بدء العملية عند تحميل الصفحة
    requestPermissionAndToken();

    // كود الرسوم البيانية
    const chartConfig = {
        labels: JSON.parse('<?php echo json_encode($chartLabels, 15, 512) ?>'),
        data: JSON.parse('<?php echo json_encode($chartData, 15, 512) ?>'),
        orderStats: JSON.parse('<?php echo json_encode($orderStats, 15, 512) ?>')
    };

    // رسم البياني للمبيعات
    const salesChart = new Chart(
        document.getElementById('salesChart').getContext('2d'),
        {
            type: 'bar',
            data: {
                labels: chartConfig.labels,
                datasets: [{
                    label: 'المبيعات الشهرية (ريال)',
                    data: chartConfig.data,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgb(13, 110, 253)',
                    borderWidth: 2,
                    borderRadius: 5,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' ريال';
                            },
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' ريال';
                            }
                        }
                    }
                }
            }
        }
    );

    // رسم بياني لحالات الطلبات
    const orderStatusChart = new Chart(
        document.getElementById('orderStatusChart').getContext('2d'),
        {
            type: 'doughnut',
            data: {
                labels: ['مكتمل', 'قيد المعالجة', 'معلق', 'قيد التوصيل', 'في الطريق', 'تم التوصيل', 'مرتجع', 'ملغي'],
                datasets: [{
                    data: [
                        chartConfig.orderStats.completed || 0,
                        chartConfig.orderStats.processing || 0,
                        chartConfig.orderStats.pending || 0,
                        chartConfig.orderStats.out_for_delivery || 0,
                        chartConfig.orderStats.on_the_way || 0,
                        chartConfig.orderStats.delivered || 0,
                        chartConfig.orderStats.returned || 0,
                        chartConfig.orderStats.cancelled || 0
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.9)',    // مكتمل
                        'rgba(13, 202, 240, 0.9)',   // قيد المعالجة
                        'rgba(255, 193, 7, 0.9)',    // معلق
                        'rgba(13, 110, 253, 0.9)',   // قيد التوصيل
                        'rgba(108, 117, 125, 0.9)',  // في الطريق
                        'rgba(32, 201, 151, 0.9)',   // تم التوصيل
                        'rgba(255, 128, 0, 0.9)',    // مرتجع
                        'rgba(220, 53, 69, 0.9)'     // ملغي
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',    // مكتمل
                        'rgb(13, 202, 240)',   // قيد المعالجة
                        'rgb(255, 193, 7)',    // معلق
                        'rgb(13, 110, 253)',   // قيد التوصيل
                        'rgb(108, 117, 125)',  // في الطريق
                        'rgb(32, 201, 151)',   // تم التوصيل
                        'rgb(255, 128, 0)',    // مرتجع
                        'rgb(220, 53, 69)'     // ملغي
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 13
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${value} طلب (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        }
    );
</script>

<?php if(isset($error)): ?>
<script>
    // عرض رسالة الخطأ إذا وجدت
    Swal.fire({
        title: 'خطأ!',
        text: '<?php echo e($error); ?>',
        icon: 'error',
        confirmButtonText: 'حسناً'
    });
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/admin-dashboard.css')); ?>">
<style>
    /* Dashboard Cards */
    .stat-card {
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .icon-wrapper {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        color: white;
        font-size: 1.25rem;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: white;
        font-size: 1.1rem;
    }

    .action-card {
        transition: transform 0.2s;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }

    .action-card:hover {
        transform: translateY(-3px);
    }

    /* Chart Styles */
    .chart-container {
        width: 100%;
        height: 100%;
        min-height: 350px;
    }

    .chart-wrapper {
        width: 100%;
        padding: 1rem;
        height: 100%;
    }

    /* Table Styles */
    .recent-orders-table {
        width: 100%;
    }

    .products-list {
        max-width: 100%;
    }

    /* Responsive Styles */
    @media (max-width: 767.98px) {
        .icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .action-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .trend {
            font-size: 0.75rem;
        }

        .chart-container {
            min-height: 300px;
        }

        .table-responsive {
            border: 0;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .products-list {
            max-width: 250px;
        }

        .products-list .text-muted {
            font-size: 0.85em;
        }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
        .chart-container {
            min-height: 325px;
        }
    }

    @media (min-width: 992px) {
        .chart-container {
            min-height: 350px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($adminLayout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>