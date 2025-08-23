<?php $__env->startSection('title', 'الطلبات غير المخصصة'); ?>
<?php $__env->startSection('page_title', 'الطلبات غير المخصصة'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="orders-container">
                        <!-- Page Header -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-1">
                                                    <i class="fas fa-clock me-2"></i>
                                                    الطلبات غير المخصصة
                                                </h4>
                                                <p class="text-muted mb-0">
                                                    الطلبات التي لم يتم تخصيصها لك أو مخصصة لأدمن آخر
                                                </p>
                                            </div>
                                            <div class="d-flex">
                                                <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-light-primary me-2">
                                                    <i class="fas fa-list me-1"></i> جميع الطلبات
                                                </a>
                                                <a href="<?php echo e(route('admin.orders.assigned')); ?>" class="btn btn-light-info">
                                                    <i class="fas fa-user-check me-1"></i> طلباتي المخصصة
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">إجمالي الطلبات</h6>
                                            <h3 class="mb-0"><?php echo e($stats['total_orders'] ?? 0); ?></h3>
                                        </div>
                                        <div class="icon-circle bg-primary-subtle text-primary">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات المكتملة</h6>
                                            <h3 class="mb-0"><?php echo e($stats['completed_orders'] ?? 0); ?></h3>
                                        </div>
                                        <div class="icon-circle bg-success-subtle text-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">قيد المعالجة</h6>
                                            <h3 class="mb-0"><?php echo e($stats['processing_orders'] ?? 0); ?></h3>
                                        </div>
                                        <div class="icon-circle bg-warning-subtle text-warning">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">إجمالي الإيرادات</h6>
                                            <h3 class="mb-0"><?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?> ر.س</h3>
                                        </div>
                                        <div class="icon-circle bg-info-subtle text-info">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Stats -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">غير مخصصة لأحد</h6>
                                            <h3 class="mb-0"><?php echo e($stats['truly_unassigned'] ?? 0); ?></h3>
                                        </div>
                                        <div class="icon-circle bg-secondary-subtle text-secondary">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">مخصصة لأدمن آخر</h6>
                                            <h3 class="mb-0"><?php echo e($stats['assigned_to_others'] ?? 0); ?></h3>
                                        </div>
                                        <div class="icon-circle bg-orange-subtle text-orange">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-filter me-2"></i>
                                    فلاتر البحث
                                </h5>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-light-primary btn-sm me-2" onclick="resetFilters()">
                                        <i class="fas fa-undo me-1"></i>
                                        إعادة تعيين
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="<?php echo e(route('admin.orders.unassigned')); ?>" class="row g-3">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="search" placeholder="بحث في رقم الطلب أو اسم العميل..." value="<?php echo e(request('search')); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="order_status">
                                            <option value="">حالة الطلب</option>
                                            <?php $__currentLoopData = $orderStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>" <?php echo e(request('order_status') == $value ? 'selected' : ''); ?>>
                                                    <?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="payment_status">
                                            <option value="">حالة الدفع</option>
                                            <?php $__currentLoopData = $paymentStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>" <?php echo e(request('payment_status') == $value ? 'selected' : ''); ?>>
                                                    <?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="date_from" placeholder="من تاريخ" value="<?php echo e(request('date_from')); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="date_to" placeholder="إلى تاريخ" value="<?php echo e(request('date_to')); ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    قائمة الطلبات غير المخصصة
                                </h5>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2"><?php echo e($orders->count() ?? 0); ?> طلب</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0">رقم الطلب</th>
                                                <th class="border-0">العميل</th>
                                                <th class="border-0">المنتجات</th>
                                                <th class="border-0">المبلغ</th>
                                                <th class="border-0">حالة الطلب</th>
                                                <th class="border-0">حالة الدفع</th>
                                                <th class="border-0">الأدمن المسؤول</th>
                                                <th class="border-0">التاريخ</th>
                                                <th class="border-0">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr class="align-middle">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <i class="fas fa-shopping-cart text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <strong><?php echo e($order['order_number']); ?></strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong><?php echo e($order['customer_name']); ?></strong>
                                                            <br>
                                                            <small class="text-muted"><?php echo e($order['customer_phone']); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-info me-2"><?php echo e($order['items_count']); ?> منتج</span>
                                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#orderItemsModal<?php echo e($order['id']); ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            <?php if($order['original_amount'] != $order['total']): ?>
                                                                <small class="text-muted text-decoration-line-through d-block"><?php echo e(number_format($order['original_amount'], 2)); ?> ر.س</small>
                                                            <?php endif; ?>
                                                            <strong class="text-success"><?php echo e(number_format($order['total'], 2)); ?> ر.س</strong>
                                                            <?php if($order['coupon_discount'] > 0): ?>
                                                                <small class="text-success d-block">خصم: <?php echo e(number_format($order['coupon_discount'], 2)); ?> ر.س</small>
                                                            <?php endif; ?>
                                                            <?php if($order['quantity_discount'] > 0): ?>
                                                                <small class="text-info d-block">خصم الكمية: <?php echo e(number_format($order['quantity_discount'], 2)); ?> ر.س</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo e($order['status_color']); ?> px-3 py-2">
                                                            <?php echo e($order['status_text']); ?>

                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo e($order['payment_status_color']); ?> px-3 py-2">
                                                            <?php echo e($order['payment_status_text']); ?>

                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if($order['is_assigned']): ?>
                                                            <div class="text-warning">
                                                                <i class="fas fa-user me-1"></i>
                                                                <?php echo e($order['assigned_admin_name']); ?>

                                                            </div>
                                                            <small class="text-muted"><?php echo e($order['assigned_at']); ?></small>
                                                        <?php else: ?>
                                                            <span class="text-secondary">
                                                                <i class="fas fa-question-circle me-1"></i>
                                                                غير مخصص
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div><strong><?php echo e($order['created_at_formatted']); ?></strong></div>
                                                        <small class="text-muted"><?php echo e($order['created_at']); ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="<?php echo e(route('admin.orders.show', $order['uuid'])); ?>" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye me-1"></i>
                                                                عرض
                                                            </a>
                                                            <?php if($order['is_available_for_assignment']): ?>
                                                                <button type="button" class="btn btn-sm btn-success assign-order-btn" data-order-uuid="<?php echo e($order['uuid']); ?>">
                                                                    <i class="fas fa-hand-paper me-1"></i>
                                                                    استلام الطلب
                                                                </button>
                                                            <?php else: ?>
                                                                <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                                    <i class="fas fa-lock me-1"></i>
                                                                    مخصص لأدمن آخر
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>

                                    <!-- Order Items Modal -->
                                    <div class="modal fade" id="orderItemsModal<?php echo e($order['id']); ?>" tabindex="-1" aria-labelledby="orderItemsModalLabel<?php echo e($order['id']); ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderItemsModalLabel<?php echo e($order['id']); ?>">
                                                        <i class="fas fa-shopping-cart me-2"></i>
                                                        تفاصيل الطلب <?php echo e($order['order_number']); ?>

                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>المنتج</th>
                                                                    <th>الكمية</th>
                                                                    <th>السعر</th>
                                                                    <th>الإجمالي</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <tr>
                                                                        <td><strong><?php echo e($item['product_name']); ?></strong></td>
                                                                        <td><span class="badge bg-primary"><?php echo e($item['quantity']); ?></span></td>
                                                                        <td><?php echo e(number_format($item['price'], 2)); ?> ر.س</td>
                                                                        <td><strong><?php echo e(number_format($item['total'], 2)); ?> ر.س</strong></td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                <h5>لا توجد طلبات غير مخصصة</h5>
                                                <p class="mb-0">جميع الطلبات مخصصة لك أو لأدمن آخر</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <?php echo e($orders->appends(request()->query())->links()); ?>

                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    console.log('Unassigned orders page loaded');
    
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Assign order button click
    $(document).on('click', '.assign-order-btn', function() {
        const orderUuid = $(this).data('order-uuid');
        const button = $(this);
        
        console.log('Assigning order:', orderUuid);
        
        // Disable button and show loading
        button.prop('disabled', true)
              .html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الاستلام...');
        
        $.ajax({
            url: `<?php echo e(url('/admin/orders')); ?>/${orderUuid}/assign`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Assignment successful:', response);
                showAlert('تم استلام الطلب بنجاح', 'success');
                
                // Update button state
                button.removeClass('btn-success assign-order-btn')
                      .addClass('btn-secondary')
                      .prop('disabled', true)
                      .html('<i class="fas fa-lock me-1"></i> تم الاستلام');
                
                // Reload page after a short delay to update stats
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr, status, error) {
                console.error('Assignment failed:', xhr.responseText);
                let errorMessage = 'حدث خطأ أثناء استلام الطلب';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showAlert(errorMessage, 'error');
                
                // Re-enable button
                button.prop('disabled', false)
                      .html('<i class="fas fa-hand-paper me-1"></i> استلام الطلب');
            }
        });
    });

    // Reset filters function
    window.resetFilters = function() {
        window.location.href = '<?php echo e(route("admin.orders.unassigned")); ?>';
    };

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the content
        $('.orders-container').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make($adminLayout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/admin/orders/unassigned.blade.php ENDPATH**/ ?>