<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Coupon;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CheckoutException;
use App\Notifications\OrderCreated;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
  protected $discountService;

  public function __construct(DiscountService $discountService)
  {
    $this->discountService = $discountService;
  }

  public function index()
  {
    if (!Auth::check()) {
      return redirect()->route('login')
        ->with('error', 'يجب تسجيل الدخول لإتمام عملية الشراء');
    }

    $cart = Cart::with(['items.product'])
      ->where('user_id', Auth::id())
      ->first();

    if (!$cart || $cart->items->isEmpty()) {
      return redirect()->route('products.index')
        ->with('error', 'السلة فارغة');
    }

    $couponCode = null;

    $discountResult = $this->calculateDiscounts($cart, $couponCode);

    return view('checkout.index', [
      'cart' => $cart,
      'quantityDiscounts' => $discountResult['quantity_discounts'],
      'quantityDiscountsTotal' => $discountResult['quantity_discount_total'],
      'couponData' => $discountResult['coupon_data'],
      'finalAmount' => $discountResult['final_amount'],
      'discountMessage' => $discountResult['message'],
      'appliedDiscountType' => $discountResult['applied_discount_type']
    ]);
  }

  public function applyCoupon(Request $request)
  {
    $request->validate([
      'coupon_code' => 'required|string|max:50'
    ]);

    $couponCode = $request->input('coupon_code');

    if (!Auth::check()) {
      return response()->json([
        'success' => false,
        'message' => 'يجب تسجيل الدخول لتطبيق كود الخصم'
      ]);
    }

    $cart = Cart::with(['items.product'])
      ->where('user_id', Auth::id())
      ->first();

    if (!$cart || $cart->items->isEmpty()) {
      return response()->json([
        'success' => false,
        'message' => 'السلة فارغة'
      ]);
    }

    $discountResult = $this->calculateDiscounts($cart, $couponCode);

    if ($discountResult['coupon_applied']) {
      $response = [
        'success' => true,
        'message' => $discountResult['message'] ?? 'تم تطبيق كود الخصم بنجاح',
        'discount_amount' => number_format($discountResult['coupon_discount'], 2),
        'final_amount' => number_format($discountResult['final_amount'], 2),
        'applied_discount_type' => $discountResult['applied_discount_type'],
        'coupon_code' => $couponCode
      ];

      if ($discountResult['coupon_data'] && isset($discountResult['coupon_data']['is_partial']) && $discountResult['coupon_data']['is_partial']) {
        $response['partial_discount'] = true;
        $response['partial_discount_message'] = $discountResult['coupon_data']['partial_discount_message'];
        $response['valid_product_ids'] = $discountResult['coupon_data']['valid_product_ids'];
      }

      return response()->json($response);
    } else {
      return response()->json([
        'success' => false,
        'message' => $discountResult['message'] ?? 'فشل تطبيق كود الخصم'
      ]);
    }
  }

  private function calculateDiscounts($cart, $couponCode = null)
  {
    $result = [
      'original_amount' => $cart->total_amount,
      'final_amount' => $cart->total_amount,
      'coupon_applied' => false,
      'coupon_discount' => 0,
      'quantity_discounts' => [],
      'quantity_discount_total' => 0,
      'coupon_data' => null,
      'message' => '',
      'applied_discount_type' => null
    ];

    foreach ($cart->items as $item) {
      $quantityDiscount = $this->discountService->getQuantityDiscount($item->product, $item->quantity);
      if ($quantityDiscount) {
        $discountAmount = $quantityDiscount->calculateDiscount($item->unit_price, $item->quantity);
        $result['quantity_discounts'][] = [
          'product_id' => $item->product_id,
          'product_name' => $item->product->name,
          'discount_amount' => $discountAmount,
          'quantity' => $item->quantity,
          'discount_type' => $quantityDiscount->type,
          'discount_value' => $quantityDiscount->value
        ];
        $result['quantity_discount_total'] += $discountAmount;
      }
    }

    $couponDiscountAmount = 0;
    $couponResult = null;
    if ($couponCode) {
      $cartItems = $cart->items->map(function($item) {
        return [
          'product_id' => $item->product_id,
          'quantity' => $item->quantity,
          'subtotal' => $item->subtotal,
          'unit_price' => $item->unit_price,
          'name' => $item->product->name
        ];
      })->toArray();

      $couponResult = $this->discountService->applyCoupon($couponCode, $result['original_amount'], $cartItems);

      if ($couponResult['success']) {
        $couponDiscountAmount = $couponResult['discount'];

        $result['coupon_data'] = [
          'code' => $couponCode,
          'name' => $couponResult['coupon']->name,
          'discount_amount' => $couponResult['discount'],
          'is_partial' => isset($couponResult['partial_discount']) && $couponResult['partial_discount'],
          'valid_product_ids' => isset($couponResult['valid_items'])
            ? collect($couponResult['valid_items'])->pluck('product_id')->toArray()
            : [],
          'partial_discount_message' => isset($couponResult['partial_discount']) && $couponResult['partial_discount']
            ? "تم تطبيق الخصم على المنتجات التالية فقط: " . collect($couponResult['valid_items'])->pluck('name')->implode('، ')
            : null
        ];
      } else {
        $result['message'] = $couponResult['message'];
      }
    }

    if ($result['quantity_discount_total'] > 0 && $couponDiscountAmount > 0) {
      if ($couponDiscountAmount >= $result['quantity_discount_total']) {
        $result['coupon_applied'] = true;
        $result['coupon_discount'] = $couponDiscountAmount;
        $result['final_amount'] = $result['original_amount'] - $couponDiscountAmount;
        $result['applied_discount_type'] = 'coupon';
        $result['message'] = 'تم تطبيق خصم الكوبون لأنه أكبر من خصم الكمية';
      } else {
        $result['final_amount'] = $result['original_amount'] - $result['quantity_discount_total'];
        $result['applied_discount_type'] = 'quantity';
        $result['message'] = 'تم تطبيق خصم الكمية لأنه أكبر من خصم الكوبون';
      }
    } else if ($couponDiscountAmount > 0) {
      $result['coupon_applied'] = true;
      $result['coupon_discount'] = $couponDiscountAmount;
      $result['final_amount'] = $result['original_amount'] - $couponDiscountAmount;
      $result['applied_discount_type'] = 'coupon';
      if (empty($result['message'])) {
        $result['message'] = 'تم تطبيق خصم الكوبون';
      }
    } else if ($result['quantity_discount_total'] > 0) {
      $result['final_amount'] = $result['original_amount'] - $result['quantity_discount_total'];
      $result['applied_discount_type'] = 'quantity';
      $result['message'] = 'تم تطبيق خصم الكمية';
    }

    $result['final_amount'] = max(0, $result['final_amount']);

    return $result;
  }

  public function store(Request $request)
  {
    try {
      if (!Auth::check()) {
        throw new CheckoutException('يجب تسجيل الدخول لإتمام عملية الشراء');
      }

      $cart = Cart::where('user_id', Auth::id())
        ->with(['items.product'])
        ->first();

      if (!$cart || $cart->items->isEmpty()) {
        throw new CheckoutException('السلة فارغة');
      }

      foreach ($cart->items as $item) {
        if (!$item->product->is_available) {
          throw new CheckoutException('المنتج "' . $item->product->name . '" غير متوفر حالياً');
        }

        // التحقق من المخزون حسب نوع المنتج
        if ($item->variant_id) {
          // للمنتجات ذات المقاسات/الألوان
          $variant = $item->variant;
          if (!$variant || !$variant->is_available) {
            throw new CheckoutException('الخيار المحدد للمنتج "' . $item->product->name . '" غير متوفر حالياً');
          }
          
          if ($item->quantity > $variant->available_stock) {
            throw new CheckoutException('المنتج "' . $item->product->name . '" غير متوفر بالكمية المطلوبة. (المتوفر حالياً: ' . $variant->available_stock . ')<br>لإكمال الطلب يجب إزالة المنتجات غير المتوفرة أو تعديل الكمية في السلة.');
          }
        } else {
          // للمنتجات العادية
          $availableStock = $item->product->available_stock;
          if ($item->quantity > $availableStock) {
            throw new CheckoutException('المنتج "' . $item->product->name . '" غير متوفر بالكمية المطلوبة. (المتوفر حالياً: ' . $availableStock . ')<br>لإكمال الطلب يجب إزالة المنتجات غير المتوفرة أو تعديل الكمية في السلة.');
          }
        }
      }

      $validated = $request->validate([
        'shipping_address' => ['required', 'string', 'max:500'],
        'phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'payment_method' => ['required', 'in:cash'],
        'coupon_code' => ['nullable', 'string', 'max:50'],
        'policy_agreement' => ['required', 'accepted']
      ]);

      return DB::transaction(function () use ($request, $validated, $cart) {
        $couponCode = $request->input('coupon_code');

        $discountResult = $this->calculateDiscounts($cart, $couponCode);

        $orderData = [
          'user_id' => Auth::id(),
          'total_amount' => $discountResult['final_amount'],
          'original_amount' => $discountResult['original_amount'],
          'coupon_discount' => $discountResult['coupon_discount'],
          'quantity_discount' => $discountResult['quantity_discount_total'],
          'coupon_code' => $discountResult['coupon_applied'] ? $couponCode : null,
          'shipping_address' => $validated['shipping_address'],
          'phone' => $validated['phone'],
          'payment_method' => $validated['payment_method'],
          'payment_status' => Order::PAYMENT_STATUS_PENDING,
          'order_status' => Order::ORDER_STATUS_PENDING,
          'notes' => $validated['notes'] ?? null,
          'policy_agreement' => true,
          'amount_paid' => 0
        ];

        $order = Order::create($orderData);

        foreach ($cart->items as $item) {
          $orderItemData = [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'subtotal' => $item->subtotal,
            'color' => $item->color,
            'size' => $item->size,
            'color_id' => $item->color_id,
            'size_id' => $item->size_id
          ];

          $orderItem = $order->items()->create($orderItemData);

          // خصم المخزون حسب نوع المنتج
          if ($item->variant_id) {
            // للمنتجات ذات المقاسات/الألوان
            $variant = $item->variant;
            if ($variant) {
              $variant->consumeStock($item->quantity);
            }
          } else {
            // للمنتجات العادية
            $item->product->consumeStock($item->quantity);
          }
        }

        if ($discountResult['coupon_applied'] && $couponCode) {
          $coupon = Coupon::where('code', $couponCode)->first();
          if ($coupon) {
            $coupon->incrementUsage();
            $coupon->markAsUsedByUser(Auth::id(), $order->id);
          }
        }

        $cart->items()->delete();
        $cart->delete();

        try {
          $order->user->notify(new OrderCreated($order));
        } catch (\Exception $e) {
        }

        return redirect()->route('orders.show', $order)
          ->with('success', 'تم إنشاء الطلب بنجاح');
      });
    } catch (ValidationException $e) {
      return back()->withErrors($e->errors())->withInput();
    } catch (CheckoutException $e) {
      return back()
        ->withInput()
        ->withErrors(['error' => $e->getMessage()]);
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->withErrors(['error' => 'حدث خطأ غير متوقع. الرجاء المحاولة مرة أخرى أو الاتصال بالدعم الفني.']);
    }
  }
}
