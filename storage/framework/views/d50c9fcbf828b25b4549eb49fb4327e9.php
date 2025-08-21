<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

<!-- Additional Libraries for Products Pages -->
<?php if(request()->is('products*')): ?>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php endif; ?>

<!-- Base JavaScript -->
<script src="<?php echo e(asset('assets/js/index.js')); ?>?t=<?php echo e(time()); ?>"></script>

<!-- Products Pages JavaScript -->
<?php if(request()->is('products*')): ?>
<script>
    window.appConfig = {
        routes: {
            products: {
                filter: '<?php echo e(route("products.filter")); ?>',
                details: '<?php echo e(route("products.details", ["product" => "__id__"])); ?>'
            }
        }
    };
</script>
<script src="<?php echo e(asset('assets/js/customer/products.js')); ?>?t=<?php echo e(time()); ?>"></script>
<?php endif; ?>

<!-- Page Specific Scripts -->
<?php echo $__env->yieldContent('page-scripts'); ?>
<?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/parts/scripts.blade.php ENDPATH**/ ?>