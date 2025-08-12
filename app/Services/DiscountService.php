<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\QuantityDiscount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DiscountService
{
    /**
     * Calculate discount based on coupon code
     *
     * @param string $code
     * @param float $amount
     * @param array $cartItems
     * @return array
     */
    public function applyCoupon(string $code, float $amount, array $cartItems = [])
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'كود الخصم غير صالح',
                'discount' => 0
            ];
        }

        if (!$coupon->isValid()) {
            return [
                'success' => false,
                'message' => 'كود الخصم غير صالح أو منتهي الصلاحية',
                'discount' => 0
            ];
        }

        // التحقق من الحد الأدنى للطلب
        if ($coupon->min_order_amount > 0 && $amount < $coupon->min_order_amount) {
            return [
                'success' => false,
                'message' => "يجب أن يكون مجموع طلبك {$coupon->min_order_amount} ريال أو أكثر لاستخدام هذا الكوبون",
                'discount' => 0
            ];
        }

        // التحقق مما إذا كان المستخدم قد استخدم الكوبون من قبل
        if (Auth::check() && $coupon->hasBeenUsedByUser(Auth::id())) {
            return [
                'success' => false,
                'message' => 'لقد استخدمت هذا الكوبون من قبل',
                'discount' => 0
            ];
        }

        // التحقق مما إذا كان الكوبون مقيدًا بمنتجات محددة
        if (!$coupon->applies_to_all_products && !empty($cartItems)) {
            $validItems = [];
            $invalidItems = [];
            $validSubtotal = 0;

            foreach ($cartItems as $item) {
                // الحصول على كائن المنتج أولاً بدلاً من استخدام المعرف مباشرة
                $product = Product::find($item['product_id']);

                // التأكد من أن المنتج موجود
                if ($product && $coupon->isValidForProduct($product)) {
                    $validItems[] = $item;
                    $validSubtotal += $item['subtotal'];
                } else {
                    $invalidItems[] = $item;
                }
            }

            // إذا لم تكن هناك منتجات صالحة للخصم
            if (empty($validItems)) {
                return [
                    'success' => false,
                    'message' => 'كود الخصم غير صالح للمنتجات في سلة التسوق الخاصة بك',
                    'discount' => 0
                ];
            }

            // يتم حساب الخصم على المنتجات الصالحة فقط
            $discount = $coupon->calculateDiscount($validSubtotal);

            if ($discount <= 0) {
                return [
                    'success' => false,
                    'message' => 'لا يمكن تطبيق كود الخصم على طلبك الحالي',
                    'discount' => 0
                ];
            }

            return [
                'success' => true,
                'message' => 'تم تطبيق كود الخصم بنجاح على المنتجات المحددة',
                'discount' => $discount,
                'coupon' => $coupon,
                'valid_items' => $validItems,
                'invalid_items' => $invalidItems,
                'partial_discount' => true
            ];
        }

        // إذا كان الكوبون ينطبق على جميع المنتجات، أو لم يتم تقديم معلومات عن المنتجات
        $discount = $coupon->calculateDiscount($amount);

        if ($discount <= 0) {
            return [
                'success' => false,
                'message' => 'لا يمكن تطبيق كود الخصم على طلبك الحالي',
                'discount' => 0
            ];
        }

        return [
            'success' => true,
            'message' => 'تم تطبيق كود الخصم بنجاح',
            'discount' => $discount,
            'coupon' => $coupon,
            'partial_discount' => false
        ];
    }

    /**
     * Apply coupon discount to get final amount
     *
     * @param Cart $cart
     * @param string|null $couponCode
     * @return array
     */
    public function calculateFinalAmount(Cart $cart, ?string $couponCode = null)
    {
        $originalAmount = $cart->total_amount;
        $result = [
            'original_amount' => $originalAmount,
            'final_amount' => $originalAmount,
            'coupon_applied' => false,
            'coupon_discount' => 0,
            'coupon_name' => null,
            'messages' => []
        ];

        // تطبيق الكوبون إذا تم توفيره وكان نظام الخصومات مفعلاً
        if ($couponCode && $this->isDiscountEnabled('discount.enabled')) {
            // تحويل عناصر السلة إلى تنسيق مناسب لدالة applyCoupon
            $cartItems = $cart->items->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'unit_price' => $item->unit_price,
                    'name' => $item->product->name
                ];
            })->toArray();

            // تمرير عناصر السلة لتطبيق الكوبون على المنتجات المحددة فقط
            $couponResult = $this->applyCoupon($couponCode, $originalAmount, $cartItems);

            if ($couponResult['success']) {
                $result['coupon_applied'] = true;
                $result['coupon_discount'] = $couponResult['discount'];
                $result['coupon_name'] = $couponResult['coupon']->name;
                $result['messages'][] = $couponResult['message'];
                $result['final_amount'] -= $couponResult['discount'];

                // Store coupon for later use when order is created
                $result['coupon'] = $couponResult['coupon'];

                // إذا كان الخصم جزئي (يطبق على منتجات محددة فقط)
                if (isset($couponResult['partial_discount']) && $couponResult['partial_discount']) {
                    $result['partial_discount'] = true;
                    $result['valid_items'] = $couponResult['valid_items'];
                    $result['invalid_items'] = $couponResult['invalid_items'];
                }
            } else {
                $result['messages'][] = $couponResult['message'];
            }
        }

        // Ensure final amount is not negative
        $result['final_amount'] = max(0, $result['final_amount']);

        return $result;
    }

    /**
     * التحقق مما إذا كان الخصم مفعل
     *
     * @param string $key
     * @return bool
     */
    private function isDiscountEnabled($key)
    {
        // دائماً مفعل بدلاً من التحقق من جدول غير موجود
        return true;
    }

    public function calculateProductDiscount(Product $product, int $quantity = 1): float
    {
        $discount = 0;

        // Calculate quantity-based discount
        $quantityDiscount = $this->getQuantityDiscount($product, $quantity);
        if ($quantityDiscount) {
            $discount += $quantityDiscount->calculateDiscount($product->price, $quantity);
        }

        // Calculate regular discount if exists
        if ($product->discount_percentage > 0) {
            $discount += $product->price * ($product->discount_percentage / 100);
        }

        return $discount;
    }

    public function getQuantityDiscount(Product $product, int $quantity): ?QuantityDiscount
    {
        // البحث عن جميع خصومات الكمية النشطة للمنتج
        $discounts = QuantityDiscount::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc')
            ->get();

        if ($discounts->isEmpty()) {
            return null;
        }

        // إذا كانت الكمية أكبر من الحد الأقصى لجميع الخصومات، نأخذ الخصم ذو الحد الأقصى الأكبر
        $maxQuantityDiscount = $discounts->first(function ($discount) use ($quantity) {
            return $discount->max_quantity === null || $discount->max_quantity >= $quantity;
        });

        if ($maxQuantityDiscount) {
            return $maxQuantityDiscount;
        }

        // إذا لم نجد خصماً يناسب الكمية بالضبط، نأخذ الخصم ذو الحد الأدنى الأقرب للكمية
        return $discounts->first();
    }

    public function calculateCartDiscount(Collection $cartItems): float
    {
        $totalDiscount = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            $quantity = $item->quantity;

            $discount = $this->calculateProductDiscount($product, $quantity);
            $totalDiscount += $discount * $quantity;
        }

        return $totalDiscount;
    }

    public function getFinalPrice(Product $product, int $quantity = 1): float
    {
        $discount = $this->calculateProductDiscount($product, $quantity);
        return max(0, $product->price - $discount);
    }
}
