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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color_id' => 'nullable|exists:product_colors,id',
            'size_id' => 'nullable|exists:product_sizes,id',
            'variant_id' => 'nullable|exists:product_size_color_inventory,id'
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_available) {
            return [
                'success' => false,
                'message' => 'عذراً، هذا المنتج غير متاح حالياً',
                'status' => 422
            ];
        }

        // البحث عن أو إنشاء variant
        $variant = $this->findOrCreateVariant($product, $request->color_id, $request->size_id, $request->variant_id);
        
        if (!$variant) {
            return [
                'success' => false,
                'message' => 'الخيار المحدد غير متوفر',
                'status' => 422
            ];
        }

        // التحقق من المخزون
        if ($request->quantity > $variant->available_stock) {
            return [
                'success' => false,
                'message' => 'الكمية المطلوبة غير متوفرة. المتوفر: ' . $variant->available_stock,
                'status' => 422
            ];
        }

        $cart = $this->getOrCreateCart($request);
        $cartItem = $this->findOrCreateCartItem($cart, $product, $variant, $request);

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

    public function findOrCreateCartItem($cart, $product, $variant, $request)
    {
        // البحث عن العنصر في السلة مع مراعاة الـ variant
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant->id)
            ->first();

        $itemPrice = $variant->price ?? $product->price;

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            // التحقق من المخزون مرة أخرى
            if ($newQuantity > $variant->available_stock) {
                throw new \Exception('الكمية الإجمالية تتجاوز المخزون المتوفر');
            }
            
            $cartItem->quantity = $newQuantity;
            $cartItem->unit_price = $itemPrice;
            $cartItem->subtotal = $cartItem->quantity * $itemPrice;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'quantity' => $request->quantity,
                'unit_price' => $itemPrice,
                'subtotal' => $request->quantity * $itemPrice,
                'color_id' => $request->color_id,
                'size_id' => $request->size_id
            ]);
        }

        $cart->total_amount = $cart->items()->sum('subtotal');
        $cart->save();

        return $cartItem;
    }

    /**
     * البحث عن أو إنشاء variant للمنتج
     */
    protected function findOrCreateVariant($product, $colorId, $sizeId, $variantId = null)
    {
        // إذا تم تمرير variant_id مباشرة
        if ($variantId) {
            return \App\Models\ProductSizeColorInventory::find($variantId);
        }

        // البحث عن variant موجود
        $variant = \App\Models\ProductSizeColorInventory::where('product_id', $product->id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->first();

        if ($variant) {
            return $variant;
        }

        // إنشاء variant جديد إذا لم يكن موجوداً
        if ($colorId || $sizeId) {
            return \App\Models\ProductSizeColorInventory::create([
                'product_id' => $product->id,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'stock' => 0,
                'consumed_stock' => 0,
                'price' => $product->price,
                'is_available' => true
            ]);
        }

        // للمنتجات العادية بدون مقاسات/ألوان
        return \App\Models\ProductSizeColorInventory::firstOrCreate([
            'product_id' => $product->id,
            'color_id' => null,
            'size_id' => null
        ], [
            'stock' => $product->stock ?? 0,
            'consumed_stock' => $product->consumed_stock ?? 0,
            'price' => $product->price,
            'is_available' => $product->is_available
        ]);
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
