<?php $__env->startSection('title', 'إدارة المنتجات'); ?>
<?php $__env->startSection('page_title', 'إدارة المنتجات'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="products-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">
                                            <i class="fas fa-filter text-primary me-2"></i>
                                            تصفية المنتجات
                                        </h5>
                                        <form action="<?php echo e(route('admin.products.index')); ?>" method="GET" class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-search text-primary me-2"></i>
                                                    بحث
                                                </label>
                                                <input type="text" name="search" class="form-control shadow-sm"
                                                       placeholder="ابحث عن المنتجات..." value="<?php echo e(request('search')); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-tag text-primary me-2"></i>
                                                    التصنيف
                                                </label>
                                                <select name="category" class="form-select shadow-sm">
                                                    <option value="">جميع الفئات</option>
                                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                                        <?php echo e($category->name); ?>

                                                    </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-sort text-primary me-2"></i>
                                                    الترتيب
                                                </label>
                                                <select name="sort" class="form-select shadow-sm">
                                                    <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>الأحدث أولاً</option>
                                                    <option value="oldest" <?php echo e(request('sort') == 'oldest' ? 'selected' : ''); ?>>الأقدم أولاً</option>
                                                    <option value="price_high" <?php echo e(request('sort') == 'price_high' ? 'selected' : ''); ?>>السعر من الأعلى</option>
                                                    <option value="price_low" <?php echo e(request('sort') == 'price_low' ? 'selected' : ''); ?>>السعر من الأقل</option>
                                                </select>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-filter me-2"></i>
                                                    تصفية
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">
                                            <i class="fas fa-plus text-primary me-2"></i>
                                            إضافة منتج
                                        </h5>
                                        <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-success w-100">
                                            <i class="fas fa-plus me-2"></i>
                                            إضافة منتج جديد
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-4">
                                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="product-card shadow-sm">
                                            <div class="product-image-container">
                                                <?php if($product->primary_image): ?>
                                                <img src="<?php echo e(url('storage/' . $product->primary_image->image_path)); ?>"
                                                    alt="<?php echo e($product->name); ?>"
                                                    class="product-image" />
                                                <?php else: ?>
                                                <div class="no-image">
                                                    <i class="fas fa-image"></i>
                                                    <span>لا توجد صورة</span>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="product-details p-3">
                                                <!-- Categories -->
                                                <div class="category-badges">
                                                    <!-- Main category -->
                                                    <span class="category-badge main-category">
                                                        <i class="fas fa-tag"></i>
                                                        <?php echo e($product->category->name); ?>

                                                    </span>

                                                    <!-- Additional categories -->
                                                    <?php if($product->categories->count() > 0): ?>
                                                        <?php $__currentLoopData = $product->categories->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($category->id != $product->category_id): ?>
                                                                <span class="category-badge">
                                                                    <i class="fas fa-tag"></i>
                                                                    <?php echo e($category->name); ?>

                                                                </span>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                        <?php if($product->categories->count() > 2): ?>
                                                            <span class="category-badge">
                                                                +<?php echo e($product->categories->count() - 2); ?>

                                                            </span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                                    <h6 class="product-name mb-0">
                                                        <a href="<?php echo e(route('admin.products.show', $product)); ?>" class="text-dark text-decoration-none">
                                                            <?php echo e($product->name); ?>

                                                        </a>
                                                    </h6>
                                                    <div class="product-status mt-2 d-flex flex-column">
                                                        <?php if($product->is_available): ?>
                                                            <span class="badge bg-success">متاح للبيع</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">غير متاح</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <p class="product-description text-muted">
                                                    <?php echo e(Str::limit($product->description, 100)); ?>

                                                </p>
                                                <div class="product-price fw-bold text-primary mt-2">
                                                    <?php if($product->min_price == $product->max_price): ?>
                                                        <?php echo e(number_format($product->min_price, 0)); ?> ريال
                                                    <?php else: ?>
                                                        <?php echo e(number_format($product->min_price, 0)); ?> - <?php echo e(number_format($product->max_price, 0)); ?> ريال
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="card-footer bg-light border-top p-3">
                                                <div class="d-flex gap-2">
                                                    <a href="<?php echo e(route('admin.products.show', $product)); ?>"
                                                       class="btn btn-sm btn-light-info flex-grow-1">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('admin.products.edit', $product)); ?>"
                                                       class="btn btn-sm btn-light-primary flex-grow-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('admin.products.destroy', $product)); ?>"
                                                          method="POST"
                                                          class="flex-grow-1">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit"
                                                                class="btn btn-sm btn-light-danger w-100"
                                                                onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="col-12">
                                        <div class="empty-state text-center py-5">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h4>لا توجد منتجات</h4>
                                            <p class="text-muted">لم يتم العثور على أي منتجات. يمكنك إضافة منتج جديد من خلال الزر أعلاه.</p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if($products->hasPages()): ?>
                                <div class="d-flex justify-content-center mt-4">
                                    <?php echo e($products->links()); ?>

                                </div>
                                <?php endif; ?>
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
<link rel="stylesheet" href="/assets/css/admin/products.css">
<?php $__env->stopSection(); ?>

<?php echo $__env->make($adminLayout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/admin/products/index.blade.php ENDPATH**/ ?>