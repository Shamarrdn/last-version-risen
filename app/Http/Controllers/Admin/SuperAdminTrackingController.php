<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderTransferRequest;
use App\Models\OrderFriend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperAdminTrackingController extends Controller
{
    /**
     * Show the admin tracking dashboard.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $adminId = $request->get('admin_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $tab = $request->get('tab', 'overview');

        // Get all admins
        $admins = User::role('admin')->get();

        // Get admin statistics
        $adminStats = $this->getAdminStatistics($adminId, $dateFrom, $dateTo);

        // Get transfer history
        $transferHistory = $this->getTransferHistory($adminId, $dateFrom, $dateTo);

        // Get friend addition history
        $friendHistory = $this->getFriendHistory($adminId, $dateFrom, $dateTo);

        // Get overall statistics
        $overallStats = $this->getOverallStatistics($dateFrom, $dateTo);

        return view('admin.superadmin.tracking', compact(
            'admins',
            'adminStats',
            'transferHistory',
            'friendHistory',
            'overallStats',
            'adminId',
            'dateFrom',
            'dateTo',
            'tab'
        ));
    }

    /**
     * Get admin statistics.
     */
    private function getAdminStatistics($adminId = null, $dateFrom = null, $dateTo = null)
    {
        $query = User::role('admin')
            ->withCount([
                'assignedOrders as total_orders',
                'assignedOrders as completed_orders' => function ($query) {
                    $query->whereIn('order_status', ['completed', 'delivered']);
                },
                'assignedOrders as paid_orders' => function ($query) {
                    $query->where('payment_status', 'paid');
                },
                'assignedOrders as pending_orders' => function ($query) {
                    $query->whereIn('order_status', ['pending', 'processing']);
                }
            ])
            ->withSum('assignedOrders as total_revenue', 'total_amount');

        if ($adminId) {
            $query->where('id', $adminId);
        }

        $admins = $query->get();

        // Add additional statistics
        foreach ($admins as $admin) {
            // Get orders within date range if specified
            $ordersQuery = $admin->assignedOrders();
            
            if ($dateFrom) {
                $ordersQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $ordersQuery->whereDate('created_at', '<=', $dateTo);
            }

            $admin->filtered_orders = $ordersQuery->count();
            $admin->filtered_completed = $ordersQuery->whereIn('order_status', ['completed', 'delivered'])->count();
            $admin->filtered_paid = $ordersQuery->where('payment_status', 'paid')->count();
            $admin->filtered_revenue = $ordersQuery->sum('total_amount');

            // Get recent activity
            $admin->recent_activity = $this->getRecentActivity($admin->id, $dateFrom, $dateTo);
        }

        return $admins;
    }

    /**
     * Get transfer history.
     */
    private function getTransferHistory($adminId = null, $dateFrom = null, $dateTo = null)
    {
        $query = OrderTransferRequest::with(['order', 'fromAdmin', 'toAdmin'])
            ->orderBy('created_at', 'desc');

        if ($adminId) {
            $query->where(function ($q) use ($adminId) {
                $q->where('from_admin_id', $adminId)
                  ->orWhere('to_admin_id', $adminId);
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->paginate(20);
    }

    /**
     * Get friend addition history.
     */
    private function getFriendHistory($adminId = null, $dateFrom = null, $dateTo = null)
    {
        $query = OrderFriend::with(['order', 'user'])
            ->orderBy('created_at', 'desc');

        if ($adminId) {
            $query->where('user_id', $adminId);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->paginate(20);
    }

    /**
     * Get overall statistics.
     */
    private function getOverallStatistics($dateFrom = null, $dateTo = null)
    {
        $query = Order::query();

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total_orders' => $query->count(),
            'assigned_orders' => (clone $query)->whereNotNull('assigned_admin_id')->count(),
            'unassigned_orders' => (clone $query)->whereNull('assigned_admin_id')->count(),
            'completed_orders' => (clone $query)->whereIn('order_status', ['completed', 'delivered'])->count(),
            'paid_orders' => (clone $query)->where('payment_status', 'paid')->count(),
            'total_revenue' => (clone $query)->sum('total_amount'),
            'total_transfers' => $this->getTransferCount($dateFrom, $dateTo),
            'total_friends_added' => $this->getFriendCount($dateFrom, $dateTo),
        ];
    }

    /**
     * Get recent activity for an admin.
     */
    private function getRecentActivity($adminId, $dateFrom = null, $dateTo = null)
    {
        $activities = collect();

        // Recent orders assigned
        $recentOrders = Order::where('assigned_admin_id', $adminId)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order_assigned',
                'message' => 'تم تخصيص الطلب ' . $order->order_number,
                'date' => $order->created_at,
                'order' => $order
            ]);
        }

        // Recent transfers
        $recentTransfers = OrderTransferRequest::where('from_admin_id', $adminId)
            ->orWhere('to_admin_id', $adminId)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentTransfers as $transfer) {
            $activities->push([
                'type' => 'transfer',
                'message' => $transfer->from_admin_id == $adminId 
                    ? 'طلب نقل الطلب ' . $transfer->order->order_number . ' إلى ' . $transfer->toAdmin->name
                    : 'تم استلام طلب النقل ' . $transfer->order->order_number . ' من ' . $transfer->fromAdmin->name,
                'date' => $transfer->created_at,
                'transfer' => $transfer
            ]);
        }

        // Recent friend additions
        $recentFriends = OrderFriend::where('user_id', $adminId)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($recentFriends as $friend) {
            $activities->push([
                'type' => 'friend_added',
                'message' => 'تم إضافة ' . $friend->friend_name . ' كصديق للطلب ' . $friend->order->order_number,
                'date' => $friend->created_at,
                'friend' => $friend
            ]);
        }

        return $activities->sortByDesc('date')->take(10);
    }

    /**
     * Get transfer count.
     */
    private function getTransferCount($dateFrom = null, $dateTo = null)
    {
        $query = OrderTransferRequest::query();
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->count();
    }

    /**
     * Get friend count.
     */
    private function getFriendCount($dateFrom = null, $dateTo = null)
    {
        $query = OrderFriend::query();
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query->count();
    }

    /**
     * Get admin performance data for charts.
     */
    public function getAdminPerformance(Request $request)
    {
        $adminId = $request->get('admin_id');
        $days = $request->get('days', 30);

        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('Y-m-d');

            $orders = Order::where('assigned_admin_id', $adminId)
                ->whereDate('created_at', $date)
                ->count();

            $completed = Order::where('assigned_admin_id', $adminId)
                ->whereDate('created_at', $date)
                ->whereIn('order_status', ['completed', 'delivered'])
                ->count();

            $data['orders'][] = $orders;
            $data['completed'][] = $completed;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
