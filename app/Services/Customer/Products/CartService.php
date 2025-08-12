<?php

namespace App\Services\Customer\Products;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        if (!$product->is_available) {
            return [
                'success' => false,
                'message' => 'عذراً، هذا المنتج غير متاح حالياً',
                'status' => 422
            ];
        }

        $needsColor = $product->allow_color_selection || $product->allow_custom_color;
        $needsSize = $product->allow_size_selection || $product->allow_custom_size;

        if ($needsColor && empty($request->color)) {
            $colorMessage = '';
            if ($product->allow_color_selection && $product->allow_custom_color) {
                $colorMessage = 'يرجى اختيار لون أو كتابة اللون المطلوب';
            } else if ($product->allow_color_selection) {
                $colorMessage = 'يرجى اختيار لون للمنتج';
            } else if ($product->allow_custom_color) {
                $colorMessage = 'يرجى كتابة اللون المطلوب';
            }

            return [
                'success' => false,
                'message' => $colorMessage,
                'status' => 422
            ];
        }

        if ($needsSize && empty($request->size)) {
            $sizeMessage = '';
            if ($product->allow_size_selection && $product->allow_custom_size) {
                $sizeMessage = 'يرجى اختيار مقاس أو كتابة المقاس المطلوب';
            } else if ($product->allow_size_selection) {
                $sizeMessage = 'يرجى اختيار مقاس للمنتج';
            } else if ($product->allow_custom_size) {
                $sizeMessage = 'يرجى كتابة المقاس المطلوب';
            }

            return [
                'success' => false,
                'message' => $sizeMessage,
                'status' => 422
            ];
        }

        $cart = $this->getOrCreateCart($request);
        $cartItem = $this->findOrCreateCartItem($cart, $product, $request);

        return [
            'success' => true,
            'message' => 'تمت إضافة المنتج إلى سلة التسوق',
            'cart_count' => $cart->items()->sum('quantity'),
            'cart_total' => $cart->total_amount,
            'product_name' => $product->name,
            'product_id' => $product->id,
            'cart_item_id' => $cartItem->id,
            'status' => 200
        ];
    }

    public function getOrCreateCart(Request $request)
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => Str::random(40)]
            );
        } else {
            $sessionId = $request->session()->get('cart_session_id');
            if (!$sessionId) {
                $sessionId = Str::random(40);
                $request->session()->put('cart_session_id', $sessionId);
            }
            return Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['total_amount' => 0]
            );
        }
    }

    public function findOrCreateCartItem($cart, $product, $request)
    {
        // البحث عن العنصر في السلة مع مراعاة اللون والمقاس
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where(function($query) use ($request) {
                $query->where('color', $request->color)->orWhereNull('color');
            })
            ->where(function($query) use ($request) {
                $query->where('size', $request->size)->orWhereNull('size');
            })
            ->first();

        // تحديد السعر بناءً على المقاس
        $itemPrice = 0; // Default price is 0 if no price source is found

        // جمع سعر المقاس إذا تم اختياره
        if ($request->size && $product->enable_size_selection) {
            $size = $product->sizes->where('size', $request->size)->first();
            if ($size && $size->price) {
                $itemPrice = $size->price;
            }
        }

        // إذا لم يتم تحديد سعر من المقاس، نستخدم أقل سعر متاح
        if ($itemPrice == 0) {
            $priceRange = $product->getPriceRange();
            $itemPrice = $priceRange['min'];
        }

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->unit_price = $itemPrice;
            $cartItem->subtotal = $cartItem->quantity * $itemPrice;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $itemPrice,
                'subtotal' => $request->quantity * $itemPrice,
                'color' => $request->color,
                'size' => $request->size
            ]);
        }

        $cart->total_amount = $cart->items()->sum('subtotal');
        $cart->save();

        return $cartItem;
    }

    public function getCartItems(Request $request)
    {
        $cart = null;
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $sessionId = $request->session()->get('cart_session_id');
            if ($sessionId) {
                $cart = Cart::where('session_id', $sessionId)->first();
            }
        }

        if (!$cart) {
            return [
                'items' => [],
                'total' => 0,
                'count' => 0
            ];
        }

        $items = $cart->items()->with('product.images')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'image' => $item->product->images->first() ?
                    asset('storage/' . $item->product->images->first()->image_path) :
                    asset('images/placeholder.jpg'),
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
                'subtotal' => $item->subtotal,
                'color' => $item->color,
                'size' => $item->size
            ];
        });

        return [
            'items' => $items,
            'total' => $cart->total_amount,
            'count' => $cart->items()->sum('quantity')
        ];
    }

    public function updateCartItem(CartItem $cartItem, $quantity)
    {
        $cartItem->quantity = $quantity;
        $cartItem->subtotal = $cartItem->quantity * $cartItem->unit_price;
        $cartItem->save();

        $cart = $cartItem->cart;
        $cart->total_amount = $cart->items->sum('subtotal');
        $cart->save();

        return [
            'success' => true,
            'message' => 'تم تحديث الكمية بنجاح',
            'item_subtotal' => $cartItem->subtotal,
            'cart_total' => $cart->total_amount,
            'cart_count' => $cart->items->sum('quantity')
        ];
    }

    public function removeCartItem(CartItem $cartItem)
    {
        if (Auth::check() && $cartItem->cart->user_id !== Auth::id()) {
            return [
                'success' => false,
                'message' => 'غير مصرح بهذا الإجراء',
                'status' => 403
            ];
        }

        $cart = $cartItem->cart;

        $cartItem->delete();

        $cart->total_amount = $cart->items->sum('subtotal');
        $cart->save();

        return [
            'success' => true,
            'message' => 'تم حذف المنتج من السلة بنجاح',
            'cart_total' => $cart->total_amount,
            'cart_count' => $cart->items->sum('quantity')
        ];
    }
}
