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
use Spatie\Permission\Models\Role;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }
        
        try {
            // Get regular dashboard data directly (same as AdminDashboardController)
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
                'month_revenue' => 0,
                'assigned_orders' => 0,
                'unassigned_orders' => 0,
                'my_assigned_orders' => 0,
                'others_assigned_orders' => 0
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
                
                // إحصائيات التخصيص
                'assigned_orders' => Order::whereNotNull('assigned_admin_id')->count(),
                'unassigned_orders' => Order::whereNull('assigned_admin_id')->count(),
                'my_assigned_orders' => Order::where('assigned_admin_id', auth()->id())->count(),
                'others_assigned_orders' => Order::where('assigned_admin_id', '!=', auth()->id())->whereNotNull('assigned_admin_id')->count(),
                
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

            // Get sales data for charts
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

            // Get recent orders
            $recentOrders = Order::with(['user', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'uuid' => $order->uuid,
                        'order_number' => $order->order_number,
                        'user_name' => $order->user ? $order->user->name : 'مستخدم محذوف',
                        'total_amount' => $order->total_amount,
                        'original_amount' => $order->original_amount ?? $order->total_amount,
                        'coupon_discount' => $order->coupon_discount ?? 0,
                        'quantity_discount' => $order->quantity_discount ?? 0,
                        'total' => $order->total_amount, // fallback for compatibility
                        'order_status' => $order->order_status,
                        'payment_status' => $order->payment_status,
                        'created_at' => $order->created_at,
                        'status_color' => match($order->order_status) {
                            Order::ORDER_STATUS_COMPLETED => 'success',
                            Order::ORDER_STATUS_PROCESSING => 'info',
                            Order::ORDER_STATUS_PENDING => 'warning',
                            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'primary',
                            Order::ORDER_STATUS_ON_THE_WAY => 'secondary',
                            Order::ORDER_STATUS_DELIVERED => 'success',
                            Order::ORDER_STATUS_RETURNED => 'danger',
                            Order::ORDER_STATUS_CANCELLED => 'dark',
                            default => 'secondary'
                        },
                        'status_text' => match($order->order_status) {
                            Order::ORDER_STATUS_PENDING => 'معلق',
                            Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                            Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'قيد التوصيل',
                            Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
                            Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
                            Order::ORDER_STATUS_CANCELLED => 'ملغي',
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
                            Order::PAYMENT_STATUS_PENDING => 'في الانتظار',
                            Order::PAYMENT_STATUS_PAID => 'مدفوع',
                            Order::PAYMENT_STATUS_FAILED => 'فشل',
                            default => 'غير معروف'
                        },
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product ? $item->product->name : 'منتج محذوف',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'total_price' => $item->total_price,
                            ];
                        })
                    ];
                });

            // Get order status distribution for chart (as object for JavaScript)
            $orderStats = [
                'completed' => $stats['completed_orders'],
                'processing' => $stats['processing_orders'],
                'pending' => $stats['pending_orders'],
                'out_for_delivery' => $stats['out_for_delivery_orders'],
                'on_the_way' => $stats['on_the_way_orders'],
                'delivered' => $stats['delivered_orders'],
                'returned' => $stats['returned_orders'],
                'cancelled' => 0 // Add if you have cancelled status
            ];
            
            // Get admin sales data
            $adminUsers = User::role('admin')->get();
            $adminSalesData = $this->getAdminSalesData($adminUsers);
            
            return view('admin.superadmin.dashboard', [
                'stats' => $stats,
                'chartData' => $chartData,
                'chartLabels' => $chartLabels,
                'monthlyGrowth' => $monthlyGrowth,
                'recentOrders' => $recentOrders,
                'orderStats' => $orderStats,
                'adminUsers' => $adminUsers,
                'adminSalesData' => $adminSalesData
            ]);
        } catch (\Exception $e) {
            Log::error('SuperAdmin Dashboard data loading error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('admin.superadmin.dashboard', [
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
                    'completed_orders' => 0,
                    'assigned_orders' => 0,
                    'unassigned_orders' => 0,
                    'my_assigned_orders' => 0,
                    'others_assigned_orders' => 0
                ],
                'chartLabels' => [now()->format('M Y')],
                'chartData' => [0],
                'monthlyGrowth' => [0],
                'recentOrders' => collect([]),
                'orderStats' => [],
                'adminUsers' => [],
                'adminSalesData' => [],
                'error' => 'Error loading dashboard data: ' . $e->getMessage()
            ]);
        }
    }
    
    private function getAdminSalesData($adminUsers)
    {
        $adminSalesData = [];
        
        foreach ($adminUsers as $admin) {
            // Get orders assigned to this admin
            $orders = Order::where('assigned_admin_id', $admin->id)->get();
            
            // Calculate stats
            $totalSales = $orders->where('payment_status', Order::PAYMENT_STATUS_PAID)->sum('total_amount');
            $pendingOrders = $orders->where('order_status', Order::ORDER_STATUS_PENDING)->count();
            $processingOrders = $orders->where('order_status', Order::ORDER_STATUS_PROCESSING)->count();
            $completedOrders = $orders->where('order_status', Order::ORDER_STATUS_COMPLETED)->count();
            $todaySales = $orders->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->where('created_at', '>=', Carbon::today())
                ->sum('total_amount');
            
            // Get monthly sales data for chart
            $monthlySales = $orders->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy(function($date) {
                    return Carbon::parse($date->created_at)->format('Y-m');
                })
                ->map(function($items) {
                    return $items->sum('total_amount');
                });
            
            // Add to admin sales data
            $adminSalesData[$admin->id] = [
                'name' => $admin->name,
                'email' => $admin->email,
                'totalSales' => $totalSales,
                'pendingOrders' => $pendingOrders,
                'processingOrders' => $processingOrders,
                'completedOrders' => $completedOrders,
                'todaySales' => $todaySales,
                'monthlySales' => $monthlySales,
            ];
        }
        
        return $adminSalesData;
    }
    
    public function adminSalesReport()
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }
        
        try {
            $adminUsers = User::role('admin')->get();
            $adminSalesData = $this->getAdminSalesData($adminUsers);
            
            return view('admin.superadmin.admin_sales_report', [
                'adminUsers' => $adminUsers,
                'adminSalesData' => $adminSalesData
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Sales Report error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.superadmin.admin_sales_report', [
                'adminUsers' => [],
                'adminSalesData' => [],
                'error' => 'Error loading sales report data: ' . $e->getMessage()
            ]);
        }
    }
    
    public function updateFcmToken(Request $request)
    {
        try {
            Log::info('Updating FCM token for superadmin', [
                'superadmin_id' => Auth::id(),
                'token' => $request->token
            ]);

            $request->validate([
                'token' => 'required|string'
            ]);

            $user = Auth::user();
            User::where('id', $user->id)->update(['fcm_token' => $request->token]);

            Log::info('FCM token updated successfully', [
                'superadmin_id' => $user->id
            ]);

            return response()->json(['message' => 'Token updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error updating FCM token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'superadmin_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'Failed to update token: ' . $e->getMessage()
            ], 500);
        }
    }
}
