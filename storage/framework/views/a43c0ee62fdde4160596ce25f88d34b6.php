<?php $__env->startSection('title', $product->name); ?>
<?php $__env->startSection('page_title', $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="products-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-box text-primary me-2"></i>
                                            تفاصيل المنتج
                                        </h5>
                                        <div class="actions">
                                            <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-light-primary me-2">
                                                <i class="fas fa-edit me-1"></i>
                                                تعديل المنتج
                                            </a>
                                            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للمنتجات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Product Images -->
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-images text-primary me-2"></i>
                                            صور المنتج
                                        </h5>
                                        <img src="<?php echo e(url('storage/' . $product->primary_image->image_path)); ?>"
                                             alt="<?php echo e($product->name); ?>"
                                             class="product-image mb-3"
                                             id="mainImage">

                                        <?php if($product->images->count() > 1): ?>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <img src="<?php echo e(url('storage/' . $image->image_path)); ?>"
                                                 alt="صورة المنتج"
                                                 class="thumbnail <?php echo e($image->is_primary ? 'active' : ''); ?>"
                                                 onclick="updateMainImage(this, '<?php echo e(url('storage/' . $image->image_path)); ?>')">
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            معلومات المنتج
                                        </h5>
                                        <div class="mb-4">
                                            <span class="status-badge <?php echo e($product->is_available ? 'in-stock' : 'out-of-stock'); ?>">
                                                <?php if($product->is_available): ?>
                                                    متوفر للبيع
                                                <?php else: ?>
                                                    غير متوفر
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-tag text-primary"></i> التصنيف الرئيسي</dt>
                                                    <dd><?php echo e($product->category->name); ?></dd>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-money-bill text-primary"></i> السعر</dt>
                                                    <dd class="text-primary fw-bold">
                                                        <?php if($product->min_price == $product->max_price): ?>
                                                            <?php echo e(number_format($product->min_price, 0)); ?> ريال
                                                        <?php else: ?>
                                                            <?php echo e(number_format($product->min_price, 0)); ?> - <?php echo e(number_format($product->max_price, 0)); ?> ريال
                                                        <?php endif; ?>
                                                    </dd>
                                                </div>
                                            </div>

                                            <?php if($product->categories && $product->categories->count() > 0): ?>
                                            <div class="col-12 mt-2">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-tags text-primary"></i> التصنيفات الإضافية</dt>
                                                    <dd>
                                                        <div class="category-badges mt-2">
                                                            <?php $__currentLoopData = $product->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if($category->id != $product->category_id): ?>
                                                                    <span class="category-badge">
                                                                        <i class="fas fa-tag"></i>
                                                                        <?php echo e($category->name); ?>

                                                                    </span>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    </dd>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-money-bill-wave text-primary"></i> السعر الأساسي</dt>
                                                    <dd>
                                                        <?php if($product->base_price): ?>
                                                            <span class="badge bg-success"><?php echo e(number_format($product->base_price, 2)); ?> ر.س</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">غير محدد</span>
                                                        <?php endif; ?>
                                                    </dd>
                                                </div>
                                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <dt><i class="fas fa-boxes text-primary"></i> إجمالي المخزون</dt>
                                    <dd>
                                        <?php
                                            $totalStock = $product->inventory->sum('stock');
                                            $totalConsumed = $product->inventory->sum('consumed_stock');
                                            $availableStock = $totalStock - $totalConsumed;
                                        ?>
                                        <span class="badge bg-info"><?php echo e(number_format($availableStock)); ?> قطعة</span>
                                        <?php if($totalConsumed > 0): ?>
                                            <small class="text-muted ms-2">(مستهلك: <?php echo e(number_format($totalConsumed)); ?>)</small>
                                        <?php endif; ?>
                                        <small class="text-muted d-block">من <?php echo e($product->inventory->count()); ?> خيار</small>
                                    </dd>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <dt><i class="fas fa-calendar-plus text-primary"></i> تاريخ الإنشاء</dt>
                                    <dd><?php echo e($product->created_at->format('Y-m-d H:i')); ?></dd>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <dt><i class="fas fa-calendar-edit text-primary"></i> آخر تحديث</dt>
                                    <dd><?php echo e($product->updated_at->format('Y-m-d H:i')); ?></dd>
                                </div>
                            </div>

                                            <div class="col-12">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-align-left text-primary"></i> الوصف</dt>
                                                    <dd><?php echo e($product->description); ?></dd>
                                                </div>
                                            </div>

                                            <?php if($product->details && count($product->details) > 0): ?>
                                            <div class="col-12 mt-3">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-list-ul text-primary"></i> تفاصيل المنتج</dt>
                                                    <dd>
                                                        <div class="table-responsive mt-2">
                                                            <table class="table table-sm table-bordered">
                                                                <tbody>
                                                                    <?php $__currentLoopData = $product->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <tr>
                                                                        <th class="bg-light" style="width: 40%"><?php echo e($key); ?></th>
                                                                        <td><?php echo e($value); ?></td>
                                                                    </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </dd>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <!-- Detailed Inventory -->
                            <?php if($product->inventory && $product->inventory->isNotEmpty()): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-warehouse text-primary me-2"></i>
                                            المخزون التفصيلي (المقاسات والألوان)
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th><i class="fas fa-ruler me-1"></i> المقاس</th>
                                                        <th><i class="fas fa-palette me-1"></i> اللون</th>
                                                        <th><i class="fas fa-boxes me-1"></i> المخزون</th>
                                                        <th><i class="fas fa-money-bill me-1"></i> السعر</th>
                                                        <th><i class="fas fa-chart-line me-1"></i> المستهلك</th>
                                                        <th><i class="fas fa-toggle-on me-1"></i> الحالة</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $product->inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td>
                                                            <?php if($inventory->size): ?>
                                                                <span class="badge bg-secondary"><?php echo e($inventory->size->name); ?></span>
                                                                <?php if($inventory->size->description): ?>
                                                                    <small class="text-muted d-block"><?php echo e($inventory->size->description); ?></small>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">افتراضي</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if($inventory->color): ?>
                                                                <div class="d-flex align-items-center">
                                                                    <?php if($inventory->color->code): ?>
                                                                        <span class="color-preview me-2" style="width: 20px; height: 20px; border-radius: 50%; background-color: <?php echo e($inventory->color->code); ?>; border: 1px solid #ddd;"></span>
                                                                    <?php endif; ?>
                                                                    <span class="badge bg-info"><?php echo e($inventory->color->name); ?></span>
                                                                </div>
                                                                <?php if($inventory->color->description): ?>
                                                                    <small class="text-muted d-block"><?php echo e($inventory->color->description); ?></small>
                                                                <?php endif; ?>
                                                        <?php else: ?>
                                                                <span class="text-muted">افتراضي</span>
                                                        <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php echo e($inventory->stock > 10 ? 'success' : ($inventory->stock > 0 ? 'warning' : 'danger')); ?>">
                                                                <?php echo e(number_format($inventory->stock)); ?> قطعة
                                                    </span>
                                                        </td>
                                                        <td>
                                                            <?php if($inventory->price): ?>
                                                                <strong class="text-primary"><?php echo e(number_format($inventory->price, 2)); ?> ر.س</strong>
                                                            <?php else: ?>
                                                                <span class="text-muted">سعر أساسي</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if($inventory->consumed_stock && $inventory->consumed_stock > 0): ?>
                                                                <span class="badge bg-warning"><?php echo e(number_format($inventory->consumed_stock)); ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted">0</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if($inventory->is_available): ?>
                                                                <span class="badge bg-success">متاح</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">غير متاح</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- إحصائيات سريعة -->
                                        <div class="row mt-4">
                                            <div class="col-md-3">
                                                <div class="stat-card">
                                                    <div class="stat-value"><?php echo e($product->inventory->count()); ?></div>
                                                    <div class="stat-label">مجموع المتغيرات</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-card">
                                                    <div class="stat-value"><?php echo e(number_format($product->inventory->sum('stock'))); ?></div>
                                                    <div class="stat-label">إجمالي المخزون</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-card">
                                                    <div class="stat-value"><?php echo e(number_format($product->inventory->sum('consumed_stock'))); ?></div>
                                                    <div class="stat-label">إجمالي المستهلك</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-card">
                                                    <div class="stat-value"><?php echo e($product->inventory->where('is_available', true)->count()); ?></div>
                                                    <div class="stat-label">المتغيرات المتاحة</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>



                            <!-- Product Options -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-cog text-primary me-2"></i>
                                            خيارات المنتج
                                        </h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-palette text-primary me-2"></i>
                                                    <span>السماح باختيار الألوان</span>
                                                    <span class="ms-auto">
                                                        <?php if($product->enable_color_selection): ?>
                                                            <i class="fas fa-check text-success"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-paint-brush text-primary me-2"></i>
                                                    <span>إضافة لون مخصص</span>
                                                    <span class="ms-auto">
                                                        <?php if($product->enable_custom_color): ?>
                                                            <i class="fas fa-check text-success"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-ruler text-primary me-2"></i>
                                                    <span>اختيار المقاسات المحددة</span>
                                                    <span class="ms-auto">
                                                        <?php if($product->enable_size_selection): ?>
                                                            <i class="fas fa-check text-success"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-ruler-combined text-primary me-2"></i>
                                                    <span>إضافة مقاس مخصص</span>
                                                    <span class="ms-auto">
                                                        <?php if($product->enable_custom_size): ?>
                                                            <i class="fas fa-check text-success"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">معلومات التصنيف</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">التصنيف الرئيسي</label>
                                        <div><?php echo e($product->category->name ?? 'غير محدد'); ?></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">التصنيفات الإضافية</label>
                                        <?php if($product->categories->count() > 0): ?>
                                            <div>
                                                <?php $__currentLoopData = $product->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-info me-1"><?php echo e($category->name); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-muted">لا توجد تصنيفات إضافية</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.products-container {
    padding: 1.5rem;
    width: 100%;
}

.product-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 0.5rem;
}

.thumbnail {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0.5rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail.active {
    border-color: var(--primary);
}

.detail-item {
    margin-bottom: 1rem;
}

.detail-item dt {
    font-size: 0.875rem;
    color: var(--text-medium);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-item dd {
    font-size: 1rem;
    margin: 0;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge.in-stock {
    background: #ECFDF5;
    color: var(--success);
}

.status-badge.low-stock {
    background: #FFFBEB;
    color: var(--warning);
}

.status-badge.out-of-stock {
    background: #FEF2F2;
    color: var(--danger);
}

.color-item,
.size-item {
    background: white;
    transition: all 0.3s ease;
}

.color-item:hover,
.size-item:hover {
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .products-container {
        padding: 0.75rem;
    }

    .product-image {
        height: 300px;
    }

    .thumbnail {
        width: 60px;
        height: 60px;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function updateMainImage(thumbnail, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    thumbnail.classList.add('active');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($adminLayout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/admin/products/show.blade.php ENDPATH**/ ?>