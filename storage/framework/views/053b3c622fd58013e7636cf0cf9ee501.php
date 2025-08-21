<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'إشعار'); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1E2A38;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
            background-color: #f8f9fa;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .email-header {
            background: linear-gradient(135deg, #000000, #333333);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .greeting {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #000000;
        }
        .intro {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.25);
            padding-bottom: 15px;
        }
        .section:last-child {
            border-bottom: none;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000000;
        }
        .section-item {
            padding: 5px 0;
        }
        .divider {
            height: 1px;
            background-color: rgba(0, 0, 0, 0.25);
            margin: 15px 0;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #000000, #333333);
            color: white !important;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            margin: 15px 0;
            text-align: center;
            transition: all 0.3s ease;
        }
        .action-button:hover {
            background: linear-gradient(135deg, #333333, #000000);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .outro {
            margin-top: 20px;
            font-size: 14px;
            color: #2C3E50;
        }
        .footer {
            background-color: rgba(0, 0, 0, 0.05);
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #2C3E50;
        }
        .payment-info {
            background-color: rgba(0, 0, 0, 0.05);
            border-right: 4px solid #000000;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .bank-details {
            background-color: rgba(255, 255, 255, 0.75);
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
           <h1>RISEN</h1>
            <h1><?php echo e($title); ?></h1>
        </div>
        <div class="email-body">
            <div class="greeting"><?php echo e($greeting); ?></div>

            <p class="intro"><?php echo e($intro); ?></p>

            <div class="divider"></div>

            <?php $__currentLoopData = $content['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="section <?php echo e($section['title'] === 'معلومات الدفع' ? 'payment-info' : ''); ?>">
                    <div class="section-title"><?php echo e($section['title']); ?></div>
                    <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($item): ?>
                            <div class="section-item"><?php echo e($item); ?></div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if(isset($content['action'])): ?>
                <div style="text-align: center;">
                    <a href="<?php echo e($content['action']['url']); ?>" class="action-button">
                        <?php echo e($content['action']['text']); ?>

                    </a>
                </div>
            <?php endif; ?>

            <div class="outro">
                <?php $__currentLoopData = $content['outro']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($line); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="footer">
            © <?php echo e(date('Y')); ?> RISEN - جميع الحقوق محفوظة
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/emails/notifications.blade.php ENDPATH**/ ?>