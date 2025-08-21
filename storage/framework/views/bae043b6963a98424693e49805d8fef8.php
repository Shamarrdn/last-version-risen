<!DOCTYPE html>
<html lang="ar" dir="rtl">

<?php if(request()->is('products*')): ?>
    
    <?php echo $__env->yieldContent('content'); ?>
<?php else: ?>
    
    <head>
        <?php echo $__env->make('parts.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->yieldContent('page-meta'); ?>
    </head>

    <body class="<?php echo $__env->yieldContent('body-class'); ?>">
        <?php echo $__env->make('parts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Content -->
        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <?php echo $__env->make('parts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('parts.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </body>
<?php endif; ?>

</html>
<?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/layouts/app.blade.php ENDPATH**/ ?>