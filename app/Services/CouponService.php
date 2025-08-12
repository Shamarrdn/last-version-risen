<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;

class CouponService
{
    public function applyCoupon($code, $cart)
    {
        // البحث عن الكوبون باستخدام الرمز
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'الكوبون غير موجود.'
            ];
        }

        // التحقق من صلاحية الكوبون
        if (!$coupon->isValid()) {
            return [
                'success' => false,
                'message' => 'الكوبون غير صالح أو منتهي الصلاحية.'
            ];
        }

        // التحقق من إمكانية تطبيق الكوبون على المنتجات في سلة التسوق
        $cartItems = $cart->items;
        $totalValidItemsPrice = 0;
        $validItemsCount = 0;
        $invalidItems = [];
        $validItems = [];

        foreach ($cartItems as $item) {
            $product = $item->product;

            if ($coupon->isValidForProduct($product)) {
                $validItems[] = [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $product->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->price * $item->quantity
                ];

                $totalValidItemsPrice += $item->price * $item->quantity;
                $validItemsCount++;
            } else {
                $invalidItems[] = [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $product->name
                ];
            }
        }

        // التحقق من الحد الأدنى للطلب إذا كان موجودًا
        if ($coupon->min_order_amount > 0 && $totalValidItemsPrice < $coupon->min_order_amount) {
            return [
                'success' => false,
                'message' => "يجب أن يكون إجمالي المشتريات {$coupon->min_order_amount} على الأقل لاستخدام هذا الكوبون."
            ];
        }

        // لا يوجد عناصر صالحة لتطبيق الكوبون عليها
        if ($validItemsCount === 0) {
            return [
                'success' => false,
                'message' => 'لا يمكن تطبيق الكوبون على أي من المنتجات في سلة التسوق الخاصة بك.'
            ];
        }

        // حساب الخصم باستخدام دالة calculateDiscount
        $discount = $coupon->calculateDiscount($totalValidItemsPrice);
        $discountDetails = [];

        if ($discount > 0) {
            if ($coupon->type === 'percentage') {
                foreach ($validItems as $item) {
                    $itemDiscount = $item['subtotal'] * ($coupon->value / 100);

                    $discountDetails[] = [
                        'item_id' => $item['id'],
                        'name' => $item['name'],
                        'discount' => $itemDiscount
                    ];
                }
            } else { // fixed amount
                // توزيع الخصم الثابت بشكل متناسب
                $discountRatio = $discount / $totalValidItemsPrice;

                foreach ($validItems as $item) {
                    $itemDiscount = $item['subtotal'] * $discountRatio;

                    $discountDetails[] = [
                        'item_id' => $item['id'],
                        'name' => $item['name'],
                        'discount' => $itemDiscount
                    ];
                }
            }
        }

        return [
            'success' => true,
            'message' => 'تم تطبيق الكوبون بنجاح!',
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ],
            'discount' => $discount,
            'discount_details' => $discountDetails,
            'valid_items' => $validItems,
            'invalid_items' => $invalidItems
        ];
    }
}
