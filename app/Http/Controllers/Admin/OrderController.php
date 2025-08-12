<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
  public function __construct()
  {
    $this->middleware(function ($request, $next) {
      if (!auth()->user()->hasRole(['admin', 'superadmin'])) {
        return redirect('/dashboard');
      }
      return $next($request);
    });
  }

  public function index(Request $request)
  {
    try {
        $query = Order::with(['user', 'items.product', 'assignedAdmin'])
            ->latest();

        $hasFilters = false;
        
        // Filter by assignment tab
        $tab = $request->get('tab', 'all'); // all, assigned, unassigned
        if ($tab === 'assigned') {
            $query->where('assigned_admin_id', auth()->id());
            $hasFilters = true;
        } elseif ($tab === 'unassigned') {
            $query->whereNull('assigned_admin_id');
            $hasFilters = true;
        }

        if ($request->order_number) {
            $query->where('order_number', 'like', "%{$request->order_number}%");
            $hasFilters = true;
        }

        if ($request->order_status) {
            $query->where('order_status', $request->order_status);
            $hasFilters = true;
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
            $hasFilters = true;
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
            $hasFilters = true;
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
            $hasFilters = true;
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', "%{$request->search}%")
                               ->orWhere('email', 'like', "%{$request->search}%");
                  });
            });
            $hasFilters = true;
        }

        $statsQuery = clone $query;
        $revenueQuery = Order::query();
        if ($request->date_from) {
            $revenueQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $revenueQuery->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->search) {
            $revenueQuery->where(function($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', "%{$request->search}%")
                               ->orWhere('email', 'like', "%{$request->search}%");
                  });
            });
        }
        $totalRevenue = $revenueQuery->where('order_status', Order::ORDER_STATUS_COMPLETED)
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->sum('total_amount');

        $stats = [
            'total_orders' => $statsQuery->count(),
            'completed_orders' => $statsQuery->where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
            'processing_orders' => $statsQuery->where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
            'total_revenue' => $totalRevenue
        ];

        if ($hasFilters) {
            $orders = $query->get();
        } else {
            $orders = $query->paginate(10);
        }

        $transformedCollection = collect(is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->items() : $orders)->map(function ($order) {
            return [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'customer_phone' => $order->user->phone ?? '-',
                'items_count' => $order->items->count(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'total' => $item->quantity * $item->product->price
                    ];
                }),
                'original_amount' => $order->original_amount,
                'quantity_discount' => $order->quantity_discount,
                'coupon_discount' => $order->coupon_discount,
                'total' => $order->total_amount,
                'status' => $order->order_status,
                'is_assigned' => $order->isAssigned(),
                'assigned_admin_name' => $order->assignedAdmin?->name,
                'assigned_at' => $order->assigned_at?->format('Y-m-d H:i'),
                'is_assigned_to_me' => $order->isAssignedTo(auth()->id()),
                'status_text' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                    Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                    Order::ORDER_STATUS_PENDING => 'معلق',
                    Order::ORDER_STATUS_CANCELLED => 'ملغي',
                    Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'جاري التوصيل',
                    Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
                    Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
                    Order::ORDER_STATUS_RETURNED => 'مرتجع',
                    default => 'غير معروف'
                },
                'status_color' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'success',
                    Order::ORDER_STATUS_PROCESSING => 'info',
                    Order::ORDER_STATUS_PENDING => 'warning',
                    Order::ORDER_STATUS_CANCELLED => 'danger',
                    Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'primary',
                    Order::ORDER_STATUS_ON_THE_WAY => 'info',
                    Order::ORDER_STATUS_DELIVERED => 'success',
                    Order::ORDER_STATUS_RETURNED => 'danger',
                    default => 'secondary'
                },
                'payment_status' => $order->payment_status,
                'payment_status_text' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'مدفوع',
                    Order::PAYMENT_STATUS_PENDING => 'معلق',
                    Order::PAYMENT_STATUS_FAILED => 'فشل',
                    default => 'غير معروف'
                },
                'payment_status_color' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'success',
                    Order::PAYMENT_STATUS_PENDING => 'warning',
                    Order::PAYMENT_STATUS_FAILED => 'danger',
                    default => 'secondary'
                },
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'created_at_formatted' => $order->created_at->format('Y/m/d')
            ];
        });

        if ($hasFilters) {
            $orders = $transformedCollection;
        } else {
            $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                $transformedCollection,
                $orders->total(),
                $orders->perPage(),
                $orders->currentPage(),
                ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => \Illuminate\Support\Facades\Request::query()]
            );
        }

        $orderStatuses = [
            Order::ORDER_STATUS_PENDING => 'قيد الانتظار',
            Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'جاري التوصيل',
            Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
            Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
            Order::ORDER_STATUS_COMPLETED => 'مكتمل',
            Order::ORDER_STATUS_RETURNED => 'مرتجع',
            Order::ORDER_STATUS_CANCELLED => 'ملغي'
        ];

        $paymentStatuses = [
            Order::PAYMENT_STATUS_PENDING => 'معلق',
            Order::PAYMENT_STATUS_PAID => 'مدفوع',
            Order::PAYMENT_STATUS_FAILED => 'فشل'
        ];

        // Count orders for tabs
        $tabCounts = [
            'all' => Order::count(),
            'assigned' => Order::where('assigned_admin_id', auth()->id())->count(),
            'unassigned' => Order::whereNull('assigned_admin_id')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'orderStatuses', 'paymentStatuses', 'stats', 'hasFilters', 'tab', 'tabCounts'));
    } catch (\Exception $e) {
        Log::error('Error in orders index: ' . $e->getMessage());
        return back()->with('error', 'حدث خطأ أثناء تحميل الطلبات');
    }
  }

  public function show($uuid)
  {
    $order = Order::where('uuid', $uuid)
        ->with(['user', 'items.product', 'items.product.category', 'address', 'phoneNumber'])
        ->firstOrFail();

    $formattedOrder = $this->formatOrderForDisplay($order);

    $additionalAddresses = collect([]);
    $additionalPhones = collect([]);

    if ($order->user) {
        $additionalAddresses = $order->user->addresses;
        $additionalPhones = $order->user->phoneNumbers;
    }

    return view('admin.orders.show', [
        'order' => $order,
        'formattedOrder' => $formattedOrder,
        'additionalAddresses' => $additionalAddresses,
        'additionalPhones' => $additionalPhones,
    ]);
  }

  public function updateStatus(Request $request, Order $order)
  {
    try {
        // Check if order is assigned and user is authorized to edit
        if ($order->isAssigned() && !$order->isAssignedTo(auth()->id())) {
            return back()->with('error', 'لا يمكنك تعديل هذا الطلب. الطلب مخصص لأدمن آخر.');
        }

        $validated = $request->validate([
            'order_status' => ['required', 'string', 'in:' . implode(',', [
                Order::ORDER_STATUS_PENDING,
                Order::ORDER_STATUS_PROCESSING,
                Order::ORDER_STATUS_COMPLETED,
                Order::ORDER_STATUS_CANCELLED,
                Order::ORDER_STATUS_OUT_FOR_DELIVERY,
                Order::ORDER_STATUS_ON_THE_WAY,
                Order::ORDER_STATUS_DELIVERED,
                Order::ORDER_STATUS_RETURNED,
            ])],
        ]);

        DB::beginTransaction();

        $oldStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        if ($newStatus === Order::ORDER_STATUS_COMPLETED && $oldStatus !== Order::ORDER_STATUS_COMPLETED) {
            if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $product->consumeStock($item->quantity);
                }
            }
        }

        if ($oldStatus === Order::ORDER_STATUS_COMPLETED && $newStatus !== Order::ORDER_STATUS_COMPLETED) {
            if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
                foreach ($order->items as $item) {
                    $item->product->returnStock($item->quantity);
                }
            }
        }

        $order->update([
            'order_status' => $validated['order_status']
        ]);

        DB::commit();

        if ($order->user) {
            $order->user->notify(new OrderStatusUpdated($order));
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating order status', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'فشل تحديث حالة الطلب: ' . $e->getMessage());
    }
  }

  private function formatOrderForDisplay(Order $order)
  {
    return [
        'id' => $order->id,
        'uuid' => $order->uuid,
        'order_number' => $order->order_number,
        'customer' => [
            'name' => $order->user->name ?? 'العميل غير متوفر',
            'email' => $order->user->email ?? '-',
            'phone' => $order->user->phone ?? '-',
        ],
        'address' => $order->shipping_address ?? ($order->address ? $order->address->getFullAddressAttribute() : '-'),
        'phone' => $order->phone ?? ($order->phoneNumber ? $order->phoneNumber->phone : '-'),
        'items' => $order->items->map(function ($item) {
            return [
                'product_name' => $item->product->name ?? 'المنتج غير متوفر',
                'product_code' => $item->product->code ?? '-',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->price * $item->quantity,
            ];
        }),
        'original_amount' => $order->original_amount,
        'quantity_discount' => $order->quantity_discount,
        'coupon_discount' => $order->coupon_discount,
        'total_amount' => $order->total_amount,
        'status' => [
            'code' => $order->order_status,
            'text' => match($order->order_status) {
                Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                Order::ORDER_STATUS_PENDING => 'معلق',
                Order::ORDER_STATUS_CANCELLED => 'ملغي',
                Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'جاري التوصيل',
                Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
                Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
                Order::ORDER_STATUS_RETURNED => 'مرتجع',
                default => 'غير معروف'
            }
        ],
        'payment' => [
            'status' => $order->payment_status,
            'text' => match($order->payment_status) {
                Order::PAYMENT_STATUS_PAID => 'مدفوع',
                Order::PAYMENT_STATUS_PENDING => 'معلق',
                Order::PAYMENT_STATUS_FAILED => 'فشل',
                default => 'غير معروف'
            }
        ],
        'created_at' => $order->created_at->format('Y-m-d H:i'),
        'formatted_date' => $order->created_at->format('d/m/Y'),
        'notes' => $order->notes ?? '-'
    ];
  }

  public function updatePaymentStatus(Request $request, Order $order)
  {
    // Check if order is assigned and user is authorized to edit
    if ($order->isAssigned() && !$order->isAssignedTo(auth()->id())) {
        return back()->with('error', 'لا يمكنك تعديل هذا الطلب. الطلب مخصص لأدمن آخر.');
    }

    $validated = $request->validate([
      'payment_status' => ['required', 'string', 'in:' . implode(',', [
        Order::PAYMENT_STATUS_PENDING,
        Order::PAYMENT_STATUS_PAID,
        Order::PAYMENT_STATUS_FAILED,
      ])],
    ]);

    $oldPaymentStatus = $order->payment_status;
    $newPaymentStatus = $validated['payment_status'];

    DB::beginTransaction();

    try {
        $order->update([
          'payment_status' => $validated['payment_status']
        ]);

        // إذا أصبح الدفع مدفوع والطلب مكتمل، استهلاك المخزون
        if ($newPaymentStatus === Order::PAYMENT_STATUS_PAID &&
            $oldPaymentStatus !== Order::PAYMENT_STATUS_PAID &&
            $order->order_status === Order::ORDER_STATUS_COMPLETED) {

            foreach ($order->items as $item) {
                $product = $item->product;
                $product->consumeStock($item->quantity);
            }
        }

        // إذا تم إلغاء الدفع والطلب مكتمل، إرجاع المخزون
        if ($oldPaymentStatus === Order::PAYMENT_STATUS_PAID &&
            $newPaymentStatus !== Order::PAYMENT_STATUS_PAID &&
            $order->order_status === Order::ORDER_STATUS_COMPLETED) {

            foreach ($order->items as $item) {
                $item->product->returnStock($item->quantity);
            }
        }

        DB::commit();
        return back()->with('success', 'تم تحديث حالة الدفع بنجاح.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating payment status', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'فشل تحديث حالة الدفع: ' . $e->getMessage());
    }
  }

  /**
   * عرض إحصائيات المبيعات حسب المنتج خلال فترة زمنية
   *
   * @param Request $request
   * @return \Illuminate\View\View
   */
  public function salesStatistics(Request $request)
  {
    // تحديد الفترة الزمنية للتقرير
    $startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : date('Y-m-d', strtotime('-30 days'));
    $endDate = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : date('Y-m-d');

    // الحصول على إحصائيات المبيعات: عدد القطع المباعة لكل منتج
    $salesData = OrderItem::with(['product'])
        ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_sales'))
        ->whereHas('order', function($query) {
            // فقط الطلبات المكتملة والمدفوعة
            $query->where('order_status', Order::ORDER_STATUS_COMPLETED)
                  ->where('payment_status', Order::PAYMENT_STATUS_PAID);
        })
        ->whereHas('order', function($query) use ($startDate, $endDate) {
            // فلترة بالفترة الزمنية
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        })
        ->groupBy('product_id')
        ->orderBy('total_quantity', 'desc')
        ->get();

    // تجهيز بيانات المخطط
    $chartLabels = [];
    $chartData = [];
    $salesTable = [];
    $totalItemsSold = 0;

    foreach ($salesData as $item) {
        if ($item->product) {
            $chartLabels[] = $item->product->name;
            $chartData[] = $item->total_quantity;
            $totalItemsSold += $item->total_quantity;

            $salesTable[] = [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'quantity' => $item->total_quantity,
                'total_sales' => $item->total_sales,
                'average_price' => $item->total_quantity > 0 ? $item->total_sales / $item->total_quantity : 0
            ];
        }
    }

    // إحصائيات إضافية
    $totalProducts = count($salesTable);
    $totalSales = array_sum(array_column($salesTable, 'total_sales'));

    return view('admin.sales.statistics', compact(
        'salesTable',
        'chartLabels',
        'chartData',
        'startDate',
        'endDate',
        'totalItemsSold',
        'totalProducts',
        'totalSales'
    ));
  }

  /**
   * Show assigned orders for current admin
   */
  public function assignedOrders(Request $request)
  {
    try {
        $query = Order::with(['user', 'items.product', 'assignedAdmin'])
            ->where('assigned_admin_id', auth()->id())
            ->latest();

        $hasFilters = false;

        if ($request->order_number) {
            $query->where('order_number', 'like', "%{$request->order_number}%");
            $hasFilters = true;
        }

        if ($request->order_status) {
            $query->where('order_status', $request->order_status);
            $hasFilters = true;
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
            $hasFilters = true;
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
            $hasFilters = true;
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
            $hasFilters = true;
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', "%{$request->search}%")
                               ->orWhere('email', 'like', "%{$request->search}%");
                  });
            });
            $hasFilters = true;
        }

        // Stats for assigned orders only
        $statsQuery = clone $query;
        $revenueQuery = Order::where('assigned_admin_id', auth()->id());
        if ($request->date_from) {
            $revenueQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $revenueQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        $totalRevenue = $revenueQuery->where('order_status', Order::ORDER_STATUS_COMPLETED)
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->sum('total_amount');

        $stats = [
            'total_orders' => $statsQuery->count(),
            'completed_orders' => $statsQuery->where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
            'processing_orders' => $statsQuery->where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
            'total_revenue' => $totalRevenue
        ];

        if ($hasFilters) {
            $orders = $query->get();
        } else {
            $orders = $query->paginate(10);
        }

        $transformedCollection = collect(is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->items() : $orders)->map(function ($order) {
            return [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'customer_phone' => $order->user->phone ?? '-',
                'items_count' => $order->items->count(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'total' => $item->quantity * $item->product->price
                    ];
                }),
                'original_amount' => $order->original_amount,
                'quantity_discount' => $order->quantity_discount,
                'coupon_discount' => $order->coupon_discount,
                'total' => $order->total_amount,
                'status' => $order->order_status,
                'is_assigned' => true,
                'assigned_admin_name' => $order->assignedAdmin?->name,
                'assigned_at' => $order->assigned_at?->format('Y-m-d H:i'),
                'is_assigned_to_me' => true,
                'status_text' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                    Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                    Order::ORDER_STATUS_PENDING => 'معلق',
                    Order::ORDER_STATUS_CANCELLED => 'ملغي',
                    Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'جاري التوصيل',
                    Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
                    Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
                    Order::ORDER_STATUS_RETURNED => 'مرتجع',
                    default => 'غير معروف'
                },
                'status_color' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'success',
                    Order::ORDER_STATUS_PROCESSING => 'info',
                    Order::ORDER_STATUS_PENDING => 'warning',
                    Order::ORDER_STATUS_CANCELLED => 'danger',
                    Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'primary',
                    Order::ORDER_STATUS_ON_THE_WAY => 'info',
                    Order::ORDER_STATUS_DELIVERED => 'success',
                    Order::ORDER_STATUS_RETURNED => 'danger',
                    default => 'secondary'
                },
                'payment_status' => $order->payment_status,
                'payment_status_text' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'مدفوع',
                    Order::PAYMENT_STATUS_PENDING => 'معلق',
                    Order::PAYMENT_STATUS_FAILED => 'فشل',
                    default => 'غير معروف'
                },
                'payment_status_color' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'success',
                    Order::PAYMENT_STATUS_PENDING => 'warning',
                    Order::PAYMENT_STATUS_FAILED => 'danger',
                    default => 'secondary'
                },
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'created_at_formatted' => $order->created_at->format('Y/m/d')
            ];
        });

        if ($hasFilters) {
            $orders = $transformedCollection;
        } else {
            $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                $transformedCollection,
                $orders->total(),
                $orders->perPage(),
                $orders->currentPage(),
                ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => \Illuminate\Support\Facades\Request::query()]
            );
        }

        $orderStatuses = [
            Order::ORDER_STATUS_PENDING => 'قيد الانتظار',
            Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'جاري التوصيل',
            Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
            Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
            Order::ORDER_STATUS_COMPLETED => 'مكتمل',
            Order::ORDER_STATUS_RETURNED => 'مرتجع',
            Order::ORDER_STATUS_CANCELLED => 'ملغي'
        ];

        $paymentStatuses = [
            Order::PAYMENT_STATUS_PENDING => 'معلق',
            Order::PAYMENT_STATUS_PAID => 'مدفوع',
            Order::PAYMENT_STATUS_FAILED => 'فشل'
        ];

        return view('admin.orders.assigned', compact('orders', 'orderStatuses', 'paymentStatuses', 'stats', 'hasFilters'));
    } catch (\Exception $e) {
        Log::error('Error in assigned orders: ' . $e->getMessage());
        return back()->with('error', 'حدث خطأ أثناء تحميل الطلبات المخصصة');
    }
  }

  /**
   * Show unassigned orders for current admin (orders not assigned to this admin)
   */
  public function unassignedOrders(Request $request)
  {
    try {
        $query = Order::with(['user', 'items.product', 'assignedAdmin'])
            ->where(function($q) {
                // Orders not assigned to current admin (either unassigned or assigned to others)
                $q->whereNull('assigned_admin_id')
                  ->orWhere('assigned_admin_id', '!=', auth()->id());
            })
            ->latest();

        $hasFilters = false;

        if ($request->order_number) {
            $query->where('order_number', 'like', "%{$request->order_number}%");
            $hasFilters = true;
        }

        if ($request->order_status) {
            $query->where('order_status', $request->order_status);
            $hasFilters = true;
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
            $hasFilters = true;
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
            $hasFilters = true;
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
            $hasFilters = true;
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', "%{$request->search}%")
                               ->orWhere('email', 'like', "%{$request->search}%");
                  });
            });
            $hasFilters = true;
        }

        // Stats for unassigned orders only
        $statsQuery = clone $query;
        $revenueQuery = Order::where(function($q) {
            $q->whereNull('assigned_admin_id')
              ->orWhere('assigned_admin_id', '!=', auth()->id());
        });
        
        if ($request->date_from) {
            $revenueQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $revenueQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        $totalRevenue = $revenueQuery->where('order_status', Order::ORDER_STATUS_COMPLETED)
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->sum('total_amount');

        $stats = [
            'total_orders' => $statsQuery->count(),
            'completed_orders' => $statsQuery->where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
            'processing_orders' => $statsQuery->where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
            'total_revenue' => $totalRevenue,
            'truly_unassigned' => Order::whereNull('assigned_admin_id')->count(),
            'assigned_to_others' => Order::where('assigned_admin_id', '!=', auth()->id())->whereNotNull('assigned_admin_id')->count(),
        ];

        if ($hasFilters) {
            $orders = $query->get();
        } else {
            $orders = $query->paginate(10);
        }

        $transformedCollection = collect(is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) ? $orders->items() : $orders)->map(function ($order) {
            return [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'customer_phone' => $order->user->phone ?? '-',
                'items_count' => $order->items->count(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'total' => $item->quantity * $item->product->price
                    ];
                }),
                'original_amount' => $order->original_amount,
                'quantity_discount' => $order->quantity_discount,
                'coupon_discount' => $order->coupon_discount,
                'total' => $order->total_amount,
                'status' => $order->order_status,
                'is_assigned' => $order->isAssigned(),
                'assigned_admin_name' => $order->assignedAdmin?->name,
                'assigned_at' => $order->assigned_at?->format('Y-m-d H:i'),
                'is_assigned_to_me' => $order->isAssignedTo(auth()->id()),
                'is_available_for_assignment' => !$order->isAssigned(), // Can only assign truly unassigned orders
                'status_text' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                    Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                    Order::ORDER_STATUS_PENDING => 'معلق',
                    Order::ORDER_STATUS_CANCELLED => 'ملغي',
                    Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'جاري التوصيل',
                    Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
                    Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
                    Order::ORDER_STATUS_RETURNED => 'مرتجع',
                    default => 'غير معروف'
                },
                'status_color' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'success',
                    Order::ORDER_STATUS_PROCESSING => 'info',
                    Order::ORDER_STATUS_PENDING => 'warning',
                    Order::ORDER_STATUS_CANCELLED => 'danger',
                    Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'primary',
                    Order::ORDER_STATUS_ON_THE_WAY => 'info',
                    Order::ORDER_STATUS_DELIVERED => 'success',
                    Order::ORDER_STATUS_RETURNED => 'danger',
                    default => 'secondary'
                },
                'payment_status' => $order->payment_status,
                'payment_status_text' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'مدفوع',
                    Order::PAYMENT_STATUS_PENDING => 'معلق',
                    Order::PAYMENT_STATUS_FAILED => 'فشل',
                    default => 'غير معروف'
                },
                'payment_status_color' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'success',
                    Order::PAYMENT_STATUS_PENDING => 'warning',
                    Order::PAYMENT_STATUS_FAILED => 'danger',
                    default => 'secondary'
                },
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'created_at_formatted' => $order->created_at->format('Y/m/d')
            ];
        });

        if ($hasFilters) {
            $orders = $transformedCollection;
        } else {
            $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                $transformedCollection,
                $orders->total(),
                $orders->perPage(),
                $orders->currentPage(),
                ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => \Illuminate\Support\Facades\Request::query()]
            );
        }

        $orderStatuses = [
            Order::ORDER_STATUS_PENDING => 'قيد الانتظار',
            Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'جاري التوصيل',
            Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
            Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
            Order::ORDER_STATUS_COMPLETED => 'مكتمل',
            Order::ORDER_STATUS_RETURNED => 'مرتجع',
            Order::ORDER_STATUS_CANCELLED => 'ملغي'
        ];

        $paymentStatuses = [
            Order::PAYMENT_STATUS_PENDING => 'معلق',
            Order::PAYMENT_STATUS_PAID => 'مدفوع',
            Order::PAYMENT_STATUS_FAILED => 'فشل'
        ];

        return view('admin.orders.unassigned', compact('orders', 'orderStatuses', 'paymentStatuses', 'stats', 'hasFilters'));
    } catch (\Exception $e) {
        Log::error('Error in unassigned orders: ' . $e->getMessage());
        return back()->with('error', 'حدث خطأ أثناء تحميل الطلبات غير المخصصة');
    }
  }

  /**
   * Assign order to current admin
   */
  public function assignOrder(Request $request, Order $order)
  {
    try {
        if ($order->isAssigned()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الطلب مخصص بالفعل لأدمن آخر'
            ], 422);
        }

        $success = $order->assignToAdmin(auth()->id());
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم استلام الطلب بنجاح',
                'assigned_admin' => auth()->user()->name,
                'assigned_at' => $order->fresh()->assigned_at->format('Y-m-d H:i')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'فشل في استلام الطلب'
        ], 500);

    } catch (\Exception $e) {
        Log::error('Error assigning order', [
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء استلام الطلب'
        ], 500);
    }
  }

  /**
   * Unassign order from current admin
   */
  public function unassignOrder(Request $request, Order $order)
  {
    try {
        if (!$order->isAssignedTo(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إلغاء استلام طلب غير مخصص لك'
            ], 422);
        }

        $order->unassign();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء استلام الطلب بنجاح'
        ]);

    } catch (\Exception $e) {
        Log::error('Error unassigning order', [
            'order_id' => $order->id,
            'admin_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء إلغاء استلام الطلب'
        ], 500);
    }
  }
}
