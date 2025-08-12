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
            // Get regular dashboard data from AdminDashboardController
            $dashboardController = new DashboardController();
            $dashboardData = $dashboardController->getDashboardData();
            
            // Get admin sales data
            $adminUsers = User::role('admin')->get();
            $adminSalesData = $this->getAdminSalesData($adminUsers);
            
            // Merge data
            $data = array_merge($dashboardData, [
                'adminUsers' => $adminUsers,
                'adminSalesData' => $adminSalesData
            ]);
            
            return view('admin.superadmin.dashboard', $data);
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
                    'completed_orders' => 0
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
            // Get orders created by this admin
            $orders = Order::where('created_by', $admin->id)->get();
            
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
