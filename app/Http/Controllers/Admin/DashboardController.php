<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // تحقق من أن المستخدم لديه دور admin فقط (وليس superadmin)
        $user = auth()->user();
        
        if (!$user->hasRole('admin') || $user->hasRole('superadmin')) {
            return redirect('/dashboard');
        }
        
        $dashboardData = $this->getDashboardData();
        return view('admin.dashboard', $dashboardData);
    }
    
    public function getDashboardData()
    {
        try {
            $stats = [
                'orders' => 0,
                'users' => 0,
                'products' => 0,
                'revenue' => 0,
                'pending_orders' => 0,
                'processing_orders' => 0,
                'completed_orders' => 0,
                'today_orders' => 0,
                'today_revenue' => 0,
                'month_orders' => 0,
                'month_revenue' => 0
            ];

            $stats = array_merge($stats, [
                'orders' => Order::count(),
                'users' => User::count(),
                'products' => Product::count(),
                'revenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                    ->sum('total_amount'),
                'pending_orders' => Order::where('order_status', Order::ORDER_STATUS_PENDING)->count(),
                'processing_orders' => Order::where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
                'completed_orders' => Order::where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
                'out_for_delivery_orders' => Order::where('order_status', Order::ORDER_STATUS_OUT_FOR_DELIVERY)->count(),
                'on_the_way_orders' => Order::where('order_status', Order::ORDER_STATUS_ON_THE_WAY)->count(),
                'delivered_orders' => Order::where('order_status', Order::ORDER_STATUS_DELIVERED)->count(),
                'returned_orders' => Order::where('order_status', Order::ORDER_STATUS_RETURNED)->count(),
                'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
                'today_revenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                    ->whereDate('created_at', Carbon::today())
                    ->sum('total_amount'),
                'month_orders' => Order::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'month_revenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('total_amount')
            ]);

            $salesData = Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->where('created_at', '>=', now()->subMonths(12))
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $chartData = [];
            $chartLabels = [];
            $monthlyGrowth = [];
            $previousTotal = 0;

            foreach ($salesData as $data) {
                $total = $data->total;
                $chartLabels[] = Carbon::createFromFormat('Y-m', $data->month)->translatedFormat('F Y');
                $chartData[] = $total;

                $growth = $previousTotal > 0 ? round((($total - $previousTotal) / $previousTotal) * 100, 1) : 0;
                $monthlyGrowth[] = $growth;
                $previousTotal = $total;
            }

            if (empty($chartLabels) || end($chartLabels) !== now()->translatedFormat('F Y')) {
                $chartLabels[] = now()->translatedFormat('F Y');
                $chartData[] = 0;
                $monthlyGrowth[] = 0;
            }

            $orderStats = [
                Order::ORDER_STATUS_COMPLETED => Order::where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
                Order::ORDER_STATUS_PROCESSING => Order::where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
                Order::ORDER_STATUS_PENDING => Order::where('order_status', Order::ORDER_STATUS_PENDING)->count(),
                Order::ORDER_STATUS_CANCELLED => Order::where('order_status', Order::ORDER_STATUS_CANCELLED)->count(),
                Order::ORDER_STATUS_OUT_FOR_DELIVERY => Order::where('order_status', Order::ORDER_STATUS_OUT_FOR_DELIVERY)->count(),
                Order::ORDER_STATUS_ON_THE_WAY => Order::where('order_status', Order::ORDER_STATUS_ON_THE_WAY)->count(),
                Order::ORDER_STATUS_DELIVERED => Order::where('order_status', Order::ORDER_STATUS_DELIVERED)->count(),
                Order::ORDER_STATUS_RETURNED => Order::where('order_status', Order::ORDER_STATUS_RETURNED)->count()
            ];

            $recentOrders = Order::with(['user', 'items.product'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'uuid' => $order->uuid,
                        'order_number' => $order->order_number,
                        'user_name' => $order->user->name,
                        'total' => $order->total_amount,
                        'original_amount' => $order->original_amount,
                        'coupon_discount' => $order->coupon_discount,
                        'quantity_discount' => $order->quantity_discount ?? 0,
                        'total_amount' => $order->total_amount,
                        'payment_status' => $order->payment_status,
                        'order_status' => $order->order_status,
                        'created_at' => $order->created_at->format('Y-m-d H:i'),
                        'items_count' => $order->items->count(),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'total_price' => $item->subtotal
                            ];
                        }),
                        'status_color' => match($order->order_status) {
                            Order::ORDER_STATUS_COMPLETED => 'success',
                            Order::ORDER_STATUS_PROCESSING => 'info',
                            Order::ORDER_STATUS_PENDING => 'warning',
                            Order::ORDER_STATUS_CANCELLED => 'danger',
                            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'primary',
                            Order::ORDER_STATUS_ON_THE_WAY => 'info',
                            Order::ORDER_STATUS_DELIVERED => 'success',
                            Order::ORDER_STATUS_RETURNED => 'secondary',
                            default => 'secondary'
                        },
                        'status_text' => match($order->order_status) {
                            Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                            Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                            Order::ORDER_STATUS_PENDING => 'معلق',
                            Order::ORDER_STATUS_CANCELLED => 'ملغي',
                            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'قيد التوصيل',
                            Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
                            Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
                            Order::ORDER_STATUS_RETURNED => 'مرتجع',
                            default => 'غير معروف'
                        },
                        'payment_status_color' => match($order->payment_status) {
                            Order::PAYMENT_STATUS_PAID => 'success',
                            Order::PAYMENT_STATUS_PENDING => 'warning',
                            Order::PAYMENT_STATUS_FAILED => 'danger',
                            default => 'secondary'
                        },
                        'payment_status_text' => match($order->payment_status) {
                            Order::PAYMENT_STATUS_PAID => 'مدفوع',
                            Order::PAYMENT_STATUS_PENDING => 'معلق',
                            Order::PAYMENT_STATUS_FAILED => 'فشل',
                            default => 'غير معروف'
                        }
                    ];
                });

            $topProducts = Product::withCount(['orderItems as sales_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('payment_status', Order::PAYMENT_STATUS_PAID);
                });
            }])
                ->orderByDesc('sales_count')
                ->take(5)
                ->get();

            return compact(
                'stats',
                'chartLabels',
                'chartData',
                'monthlyGrowth',
                'recentOrders',
                'orderStats',
                'topProducts'
            );
        } catch (\Exception $e) {
            Log::error('Dashboard data loading error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $defaultStatuses = [
                Order::ORDER_STATUS_PENDING => 0,
                Order::ORDER_STATUS_PROCESSING => 0,
                Order::ORDER_STATUS_COMPLETED => 0,
                Order::ORDER_STATUS_CANCELLED => 0,
                Order::ORDER_STATUS_OUT_FOR_DELIVERY => 0,
                Order::ORDER_STATUS_ON_THE_WAY => 0,
                Order::ORDER_STATUS_DELIVERED => 0,
                Order::ORDER_STATUS_RETURNED => 0
            ];

            return [
                'stats' => [
                    'orders' => 0,
                    'users' => 0,
                    'products' => 0,
                    'revenue' => 0,
                    'today_orders' => 0,
                    'month_orders' => 0,
                    'today_revenue' => 0,
                    'month_revenue' => 0,
                    'pending_orders' => 0,
                    'processing_orders' => 0,
                    'completed_orders' => 0
                ],
                'chartLabels' => [now()->format('M Y')],
                'chartData' => [0],
                'monthlyGrowth' => [0],
                'recentOrders' => collect([]),
                'orderStats' => $defaultStatuses,
                'error' => 'Error loading dashboard data: ' . $e->getMessage()
            ];
        }
    }

    public function updateFcmToken(Request $request)
    {
        try {
            Log::info('Updating FCM token for admin', [
                'admin_id' => Auth::id(),
                'token' => $request->token
            ]);

            $request->validate([
                'token' => 'required|string'
            ]);

            $user = Auth::user();
            User::where('id', $user->id)->update(['fcm_token' => $request->token]);

            Log::info('FCM token updated successfully', [
                'admin_id' => $user->id
            ]);

            return response()->json(['message' => 'Token updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error updating FCM token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'Failed to update token: ' . $e->getMessage()
            ], 500);
        }
    }
}
