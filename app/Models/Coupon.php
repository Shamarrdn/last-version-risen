<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'is_active',
        'applies_to_all_products',
        'starts_at',
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'applies_to_all_products' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2'
    ];

    /**
     * المنتجات التي يمكن تطبيق الكوبون عليها
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    /**
     * التصنيفات التي يمكن تطبيق الكوبون عليها
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    /**
     * التحقق مما إذا كان الكوبون صالحًا للتطبيق على منتج معين
     *
     * @param Product $product
     * @return bool
     */
    public function isValidForProduct(Product $product)
    {
        // If coupon applies to all products, it's valid
        if ($this->applies_to_all_products) {
            return true;
        }

        // Check if the product is directly linked to the coupon
        if ($this->products()->where('product_id', $product->id)->exists()) {
            return true;
        }

        // Check if the product's category is linked to the coupon
        $productCategoryIds = [];

        // الحصول على معرف الفئة الرئيسية للمنتج
        if ($product->category_id) {
            $productCategoryIds[] = $product->category_id;
        }

        // الحصول على معرفات الفئات المرتبطة من خلال علاقة many-to-many
        if (method_exists($product, 'categories')) {
            $productCategoryIds = array_merge(
                $productCategoryIds,
                $product->categories()->pluck('categories.id')->toArray()
            );
        }

        if (!empty($productCategoryIds) && $this->categories()->whereIn('categories.id', $productCategoryIds)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the coupon is valid
     *
     * @return bool
     */
    public function isValid()
    {
        // Check if coupon is active
        if (!$this->is_active) {
            return false;
        }

        // Check start date
        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        // Check expiration date
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        // Check usage limit
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount based on coupon type and value
     *
     * @param float $amount The order amount
     * @return float
     */
    public function calculateDiscount($amount)
    {
        // Check minimum order amount
        if ($this->min_order_amount && $amount < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        } elseif ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }

        return 0;
    }

    /**
     * Increment the used count
     */
    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    /**
     * التحقق مما إذا كان المستخدم قد استخدم الكوبون من قبل
     *
     * @param int $userId
     * @return bool
     */
    public function hasBeenUsedByUser($userId)
    {
        return CouponUsage::where('coupon_id', $this->id)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * تسجيل استخدام الكوبون بواسطة المستخدم
     *
     * @param int $userId
     * @param int $orderId
     * @return void
     */
    public function markAsUsedByUser($userId, $orderId)
    {
        CouponUsage::create([
            'coupon_id' => $this->id,
            'user_id' => $userId,
            'order_id' => $orderId,
            'used_at' => now()
        ]);
    }
}
