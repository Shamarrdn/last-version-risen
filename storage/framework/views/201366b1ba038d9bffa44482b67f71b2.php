<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product', 'size' => '16']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['product', 'size' => '16']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php if($product->primary_image): ?>
    <img src="<?php echo e(Storage::url($product->primary_image->image_path)); ?>"
        alt="<?php echo e($product->name); ?>"
        <?php echo e($attributes->merge(['class' => "w-{$size} h-{$size} object-cover rounded"])); ?>>
<?php else: ?>
    <div class="w-<?php echo e($size); ?> h-<?php echo e($size); ?> bg-gray-200 rounded flex items-center justify-center">
        <svg class="w-<?php echo e((int)($size/2)); ?> h-<?php echo e((int)($size/2)); ?> text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\ADMIN\Desktop\projects\risenn\RISEN\resources\views/components/product-image.blade.php ENDPATH**/ ?>