<?php $__env->startSection('title', 'لوحة التحكم'); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/customer/dashboard.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-8 mb-3 mb-md-0">
                <h1 class="h3 mb-1">مرحباً، <?php echo e(Auth::user()->name); ?></h1>
                <p class="text-muted mb-0">مرحباً بك في لوحة التحكم الخاصة بك</p>
            </div>
            <div class="col-12 col-md-4 text-center text-md-end">
                <span class="badge bg-primary"><?php echo e(Auth::user()->role === 'admin' ? 'مدير' : 'عميل'); ?></span>
            </div>
        </div>
        <!-- Guide Hint -->
        <div class="guide-hint mt-3">
            <div class="alert alert-info d-flex align-items-center border-0" role="alert">
                <i class="fas fa-lightbulb me-2 text-warning"></i>
                <span>تحتاج مساعدة؟ اضغط على زر <i class="fas fa-question-circle mx-1 text-primary"></i> في أسفل يسار الشاشة لعرض دليل استخدام لوحة التحكم</span>
            </div>
        </div>
    </div>

    <!-- Guide Toggle Button -->
    <button class="guide-toggle-btn" id="guideToggle" title="دليل الاستخدام">
        <i class="fas fa-question"></i>
    </button>

    <!-- User Guide Section -->
    <div class="user-guide-section" id="userGuide">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-book-reader me-2 text-primary"></i>
                    دليل استخدام لوحة التحكم
                </h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="guide-item">
                            <h6>
                                <i class="fas fa-phone-alt text-primary me-2"></i>
                                إدارة أرقام الهاتف
                            </h6>
                            <ul class="text-muted small">
                                <li>اضغط على "إضافة رقم" لتسجيل رقم هاتف جديد</li>
                                <li>يمكنك تعيين رقم كرقم رئيسي باستخدام أيقونة النجمة</li>
                                <li>استخدم أيقونة التعديل لتحديث رقم موجود</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="guide-item">
                            <h6>
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                إدارة العناوين
                            </h6>
                            <ul class="text-muted small">
                                <li>اضغط على "إضافة عنوان" لتسجيل عنوان جديد</li>
                                <li>أدخل تفاصيل العنوان كاملة للتوصيل السريع</li>
                                <li>يمكنك تحديد عنوان رئيسي للطلبات المستقبلية</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="guide-item">
                            <h6>
                                <i class="fas fa-shopping-bag text-primary me-2"></i>
                                متابعة الطلبات
                            </h6>
                            <ul class="text-muted small">
                                <li>راقب آخر طلباتك وحالتها في قسم "آخر الطلبات"</li>
                                <li>اضغط على أيقونة العين لعرض تفاصيل أي طلب</li>
                                <li>تابع حالة طلبك من خلال الألوان المميزة</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="dashboard-card orders">
                <div class="card-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="card-info">
                    <h3><?php echo e($stats['orders_count']); ?></h3>
                    <p>الطلبات</p>
                </div>
                <div class="card-arrow">
                    <a href="/orders" class="stretched-link">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="dashboard-card cart">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-info">
                    <h3><?php echo e($stats['cart_items_count']); ?></h3>
                    <p>منتجات في السلة</p>
                </div>
                <div class="card-arrow">
                    <a href="/cart" class="stretched-link">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="dashboard-card notifications">
                <div class="card-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="card-info">
                    <h3><?php echo e($stats['unread_notifications']); ?></h3>
                    <p>إشعارات جديدة</p>
                </div>
                <div class="card-arrow">
                    <a href="/notifications" class="stretched-link">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Phone Numbers & Addresses Section -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Phone Numbers -->
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="mb-0">أرقام الهاتف</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPhoneModal">
                        <i class="fas fa-plus ms-1"></i>إضافة رقم
                    </button>
                </div>
                <div class="card-body">
                    <?php if($phones->isEmpty()): ?>
                    <div class="empty-state">
                        <i class="fas fa-phone"></i>
                        <p>لا توجد أرقام هاتف مسجلة</p>
                    </div>
                    <?php else: ?>
                    <div class="list-group">
                        <?php $__currentLoopData = $phones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item <?php echo e($phone['is_primary'] ? 'active' : ''); ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone me-2"></i>
                                        <span class="phone-number" dir="ltr"><?php echo e(substr($phone['phone'], 0, 4)); ?> <?php echo e(substr($phone['phone'], 4, 3)); ?> <?php echo e(substr($phone['phone'], 7)); ?></span>
                                        <?php if($phone['is_primary']): ?>
                                        <span class="badge bg-warning ms-2 primary-badge">رئيسي</span>
                                        <?php endif; ?>
                                        <span class="badge bg-<?php echo e($phone['type_color']); ?> ms-2"><?php echo e($phone['type_text']); ?></span>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        تم الإضافة: <?php echo e($phone['created_at']); ?>

                                    </small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-phone"
                                        data-id="<?php echo e($phone['id']); ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editPhoneModal"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if(!$phone['is_primary']): ?>
                                    <button class="btn btn-sm btn-outline-warning make-primary-phone"
                                        data-id="<?php echo e($phone['id']); ?>"
                                        title="تعيين كرقم رئيسي">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-danger delete-phone"
                                        data-id="<?php echo e($phone['id']); ?>"
                                        title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="mb-0">العناوين</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="fas fa-plus ms-1"></i>إضافة عنوان
                    </button>
                </div>
                <div class="card-body">
                    <?php if($addresses->isEmpty()): ?>
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>لا توجد عناوين مسجلة</p>
                    </div>
                    <?php else: ?>
                    <div class="list-group">
                        <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="list-group-item <?php echo e($address['is_primary'] ? 'active' : ''); ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <span><?php echo e($address['full_address']); ?></span>
                                        <?php if($address['is_primary']): ?>
                                        <span class="badge bg-warning ms-2 primary-badge">رئيسي</span>
                                        <?php endif; ?>
                                        <span class="badge bg-<?php echo e($address['type_color']); ?> ms-2"><?php echo e($address['type_text']); ?></span>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        تم الإضافة: <?php echo e($address['created_at']); ?>

                                    </small>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary edit-address"
                                        data-id="<?php echo e($address['id']); ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editAddressModal"
                                        title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if(!$address['is_primary']): ?>
                                    <button class="btn btn-sm btn-outline-warning make-primary-address"
                                        data-id="<?php echo e($address['id']); ?>"
                                        title="تعيين كعنوان رئيسي">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-danger delete-address"
                                        data-id="<?php echo e($address['id']); ?>"
                                        title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="row g-3 g-md-4">
        <!-- Recent Orders -->
        <div class="col-12">
            <div class="section-card h-100">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
                    <h2 class="mb-0">آخر الطلبات</h2>
                    <a href="/orders" class="btn btn-outline-primary btn-sm">
                        عرض الكل <i class="fas fa-arrow-left me-1"></i>
                    </a>
                </div>
                <?php if(count($recent_orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $recent_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#<?php echo e($order['order_number']); ?></td>
                                <td><?php echo e($order['created_at']->format('Y/m/d')); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($order['status_color']); ?>">
                                        <?php echo e($order['status_text']); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('orders.show', $order['uuid'])); ?>"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <p>لا توجد طلبات حتى الآن</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Phone Modal -->
<div class="modal fade" id="addPhoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة رقم هاتف</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPhoneForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">رقم الهاتف</label>
                        <input type="tel" class="form-control" name="phone" required>
                        <div class="form-text">مثال: 0512345678</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">النوع</label>
                        <select class="form-select" name="type" required>
                            <option value="">اختر نوع الرقم</option>
                            <?php $__currentLoopData = App\Models\PhoneNumber::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($text); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Phone Modal -->
<div class="modal fade" id="editPhoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل رقم الهاتف</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPhoneForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">رقم الهاتف</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">النوع</label>
                        <select class="form-select" name="type" required>
                            <?php $__currentLoopData = App\Models\PhoneNumber::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($text); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <input type="hidden" name="phone_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة عنوان</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">النوع</label>
                        <select class="form-select" name="type" required>
                            <option value="">اختر نوع العنوان</option>
                            <?php $__currentLoopData = App\Models\Address::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($text); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">المدينة</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">المنطقة</label>
                        <input type="text" class="form-control" name="area" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">الشارع</label>
                        <input type="text" class="form-control" name="street" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم المبنى</label>
                        <input type="text" class="form-control" name="building_no">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تفاصيل إضافية</label>
                        <textarea class="form-control" name="details" rows="3"
                                  placeholder="مثال: بجوار مسجد، خلف مدرسة، الخ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل العنوان</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAddressForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">النوع</label>
                        <select class="form-select" name="type" required>
                            <?php $__currentLoopData = App\Models\Address::TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($text); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">المدينة</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">المنطقة</label>
                        <input type="text" class="form-control" name="area" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">الشارع</label>
                        <input type="text" class="form-control" name="street" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم المبنى</label>
                        <input type="text" class="form-control" name="building_no">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تفاصيل إضافية</label>
                        <textarea class="form-control" name="details" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="address_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo e(asset('js/dashboard.js')); ?>"></script>

<script>
    // تهيئة CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/dashboard.blade.php ENDPATH**/ ?>