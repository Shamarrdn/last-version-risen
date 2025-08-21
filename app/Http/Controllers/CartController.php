<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للوصول إلى سلة التسوق');
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return view('cart.index', [
                'cart_items' => collect(),
                'subtotal' => 0,
                'total' => 0,
                'cart_items_count' => 0
            ]);
        }

        $cart_items = $cart->items()
            ->with(['product.images', 'product.category', 'product.sizes'])
            ->get();

        $subtotal = $cart_items->sum('subtotal');
        $total = $subtotal;

        return view('cart.index', [
            'cart_items' => $cart_items,
            'subtotal' => $subtotal,
            'total' => $total,
            'cart_items_count' => $cart_items->sum('quantity')
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'color_id' => 'nullable|exists:color_options,id',
            'size_id' => 'nullable|exists:size_options,id',
            'variant_id' => 'nullable|exists:product_size_color_inventory,id'
        ]);

        // للمستخدمين المسجلين
        if (Auth::check()) {
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            // البحث عن أو إنشاء variant
            $variant = $this->findOrCreateVariant($product, $request->color_id, $request->size_id, $request->variant_id);
            
            if (!$variant) {
                return back()->with('error', 'الخيار المحدد غير متوفر');
            }

            // التحقق من المخزون
            if ($request->quantity > $variant->available_stock) {
                return back()->with('error', 'الكمية المطلوبة غير متوفرة. المتوفر: ' . $variant->available_stock);
            }

            // البحث عن العنصر في السلة
            $cartItem = $cart->items()->where('product_id', $product->id)
                ->where('variant_id', $variant->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $request->quantity;
                if ($newQuantity > $variant->available_stock) {
                    return back()->with('error', 'الكمية الإجمالية تتجاوز المخزون المتوفر');
                }
                
                $cartItem->increment('quantity', $request->quantity);
                $cartItem->update([
                    'subtotal' => $cartItem->quantity * $cartItem->unit_price
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'unit_price' => $variant->price ?? $product->price,
                    'subtotal' => ($variant->price ?? $product->price) * $request->quantity,
                    'color_id' => $request->color_id,
                    'size_id' => $request->size_id
                ]);
            }

            // تحديث إجمالي السلة
            $cart->update([
                'total_amount' => $cart->items->sum('subtotal')
            ]);
        }
        // للزوار
        else {
            $cart = Session::get('cart', []);
            $cartKey = $product->id . '_' . ($request->variant_id ?? 'general');
            
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $request->quantity;
            } else {
                $cart[$cartKey] = [
                    'product_id' => $product->id,
                    'variant_id' => $request->variant_id,
                    'quantity' => $request->quantity,
                    'color_id' => $request->color_id,
                    'size_id' => $request->size_id
                ];
            }
            Session::put('cart', $cart);
        }

        return back()->with('success', 'تم إضافة المنتج إلى السلة بنجاح');
    }

    /**
     * البحث عن أو إنشاء variant للمنتج
     */
    protected function findOrCreateVariant($product, $colorId, $sizeId, $variantId = null)
    {
        // Debug: لوقين المعاملات المرسلة
        \Log::info('findOrCreateVariant called with:', [
            'product_id' => $product->id,
            'color_id' => $colorId,
            'size_id' => $sizeId,
            'variant_id' => $variantId
        ]);

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

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id] = $request->quantity;
            Session::put('cart', $cart);
            return back()->with('success', 'Cart updated successfully.');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    public function remove(Product $product)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            Session::put('cart', $cart);
            return back()->with('success', 'Product removed from cart.');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    public function clear()
    {
        Session::forget('cart');
        return back()->with('success', 'Cart cleared successfully.');
    }

    protected function mergeCartAfterLogin($user)
    {
        $sessionCart = Session::get('cart', []);

        if (!empty($sessionCart)) {
            $cart = Cart::firstOrCreate([
                'user_id' => $user->id
            ]);

            foreach ($sessionCart as $productId => $quantity) {
                $product = Product::find($productId);

                if ($product) {
                    $cart->items()->updateOrCreate(
                        ['product_id' => $productId],
                        [
                            'quantity' => $quantity,
                            'unit_price' => $product->price,
                            'subtotal' => $product->price * $quantity
                        ]
                    );
                }
            }

            $cart->update([
                'total_amount' => $cart->items->sum('subtotal')
            ]);

            Session::forget('cart');
        }
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول لإضافة المنتجات إلى السلة'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size_id' => 'nullable|exists:size_options,id',
            'color_id' => 'nullable|exists:color_options,id',
            'size' => 'nullable|string',
            'color' => 'nullable|string'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);

            // Debug: لوقين البيانات المستلمة
            \Log::info('Cart Add Request Data:', [
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
                'size_id' => $request->size_id,
                'color' => $request->color,
                'size' => $request->size,
                'quantity' => $request->quantity
            ]);

            if (!$product->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنتج غير متوفر حالياً'
                ], 400);
            }

            // البحث عن الـ variant المناسب
            $variant = $this->findOrCreateVariant($product, $request->color_id, $request->size_id);

            if (!$variant || !$variant->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الخيار غير متوفر حالياً'
                ], 400);
            }

            // التحقق من المخزون المتاح
            if ($variant->available_stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة. المتوفر: ' . $variant->available_stock
                ], 400);
            }

            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            // البحث عن عنصر مشابه في السلة
            $cartItem = $cart->items()
                ->where('product_id', $product->id)
                ->where('variant_id', $variant->id)
                ->first();

            if ($cartItem) {
                // التحقق من أن الكمية الإجمالية لا تتجاوز المخزون
                $totalQuantity = $cartItem->quantity + $request->quantity;
                if ($totalQuantity > $variant->available_stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'الكمية الإجمالية تتجاوز المخزون المتاح'
                    ], 400);
                }

                // تحديث الكمية إذا كان المنتج موجود
                $cartItem->quantity = $totalQuantity;
                $cartItem->subtotal = $variant->price * $cartItem->quantity;
                $cartItem->save();
            } else {
                // إنشاء عنصر جديد
                $cartItemData = [
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'size' => $request->size,
                    'color' => $request->color,
                    'size_id' => $request->size_id,
                    'color_id' => $request->color_id,
                    'unit_price' => $variant->price,
                    'subtotal' => $variant->price * $request->quantity
                ];

                // Debug: لوقين البيانات قبل الحفظ
                \Log::info('Creating CartItem with data:', $cartItemData);

                $cartItem = CartItem::create($cartItemData);

                // Debug: لوقين البيانات بعد الحفظ
                \Log::info('Created CartItem:', $cartItem->toArray());
            }

            // تحديث إجمالي السلة
            $this->updateCartTotal($cart);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المنتج إلى السلة بنجاح',
                'cart_count' => $cart->items->sum('quantity'),
                'cart_total' => number_format($cart->total_amount, 2),
                'product_name' => $product->name,
                'product_id' => $product->id,
                'cart_item_id' => $cartItem->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المنتج إلى السلة'
            ], 500);
        }
    }



    protected function updateCartTotal($cart)
    {
        $cart->refresh();
        $total = $cart->items->sum('subtotal');
        $cart->update(['total_amount' => $total]);
    }

    public function getItems()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => '0.00',
                'count' => 0
            ]);
        }

        $items = $cart->items->map(function ($item) {
            $product = $item->product;
            $image = $product->images->first() ?
                     asset('storage/' . $product->images->first()->image_path) :
                     null;

            return [
                'id' => $item->id,
                'name' => $product->name,
                'price' => number_format($item->unit_price, 2),
                'quantity' => $item->quantity,
                'subtotal' => number_format($item->subtotal, 2),
                'image' => $image,
                'color' => $item->color,
                'size' => $item->size
            ];
        });

        return response()->json([
            'items' => $items,
            'total' => number_format($cart->total_amount, 2),
            'count' => $cart->items->sum('quantity')
        ]);
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function updateItem(Request $request, CartItem $cartItem)
    {
        if (!Auth::check() || $cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بهذا الإجراء'
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $cartItem->quantity = $request->quantity;
            $cartItem->subtotal = $cartItem->unit_price * $cartItem->quantity;
            $cartItem->save();

            $cart = $cartItem->cart;
            $this->updateCartTotal($cart);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الكمية بنجاح',
                'item_subtotal' => $cartItem->subtotal,
                'cart_total' => $cart->total_amount,
                'cart_count' => $cart->items->sum('quantity')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الكمية'
            ], 500);
        }
    }

    /**
     * حذف منتج من السلة
     */
    public function removeItem(CartItem $cartItem)
    {
        if (!Auth::check() || $cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بهذا الإجراء'
            ], 403);
        }

        try {
            $cart = $cartItem->cart;

            $cartItem->delete();
            $this->updateCartTotal($cart);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج من السلة بنجاح',
                'cart_total' => $cart->total_amount,
                'cart_count' => $cart->items->sum('quantity')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج'
            ], 500);
        }
    }

    /**
     * الحصول على عدد عناصر السلة للمستخدم الحالي
     *
     * @return \Illuminate\Http\Response|int
     */
    public function getCartCount()
    {
        if (!Auth::check()) {
            return request()->ajax() ?
                response()->json(['count' => 0]) :
                0;
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        $count = 0;
        if ($cart) {
            $count = $cart->items->sum('quantity');
        }

        return request()->ajax() ?
            response()->json(['count' => $count]) :
            $count;
    }
}
