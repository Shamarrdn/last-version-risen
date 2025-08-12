<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTransferRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderTransferController extends Controller
{
    /**
     * Show transfer requests page.
     */
    public function index(Request $request)
    {
        try {
            $query = OrderTransferRequest::with(['order.user', 'fromAdmin', 'toAdmin'])
                ->where(function($q) {
                    // Show requests where current admin is involved (from or to)
                    $q->where('from_admin_id', auth()->id())
                      ->orWhere('to_admin_id', auth()->id());
                })
                ->latest();

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Filter by order number
            if ($request->order_number) {
                $query->whereHas('order', function($q) use ($request) {
                    $q->where('order_number', 'like', "%{$request->order_number}%");
                });
            }

            $transferRequests = $query->paginate(15);

            // Get stats
            $stats = [
                'total_requests' => OrderTransferRequest::where(function($q) {
                    $q->where('from_admin_id', auth()->id())
                      ->orWhere('to_admin_id', auth()->id());
                })->count(),
                'pending_requests' => OrderTransferRequest::where('to_admin_id', auth()->id())
                    ->where('status', OrderTransferRequest::STATUS_PENDING)->count(),
                'sent_requests' => OrderTransferRequest::where('from_admin_id', auth()->id())
                    ->where('status', OrderTransferRequest::STATUS_PENDING)->count(),
                'approved_requests' => OrderTransferRequest::where(function($q) {
                    $q->where('from_admin_id', auth()->id())
                      ->orWhere('to_admin_id', auth()->id());
                })->where('status', OrderTransferRequest::STATUS_APPROVED)->count(),
                'rejected_requests' => OrderTransferRequest::where(function($q) {
                    $q->where('from_admin_id', auth()->id())
                      ->orWhere('to_admin_id', auth()->id());
                })->where('status', OrderTransferRequest::STATUS_REJECTED)->count(),
            ];

            return view('admin.orders.transfer', compact('transferRequests', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in transfer requests: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحميل طلبات النقل');
        }
    }

    /**
     * Show form to create transfer request.
     */
    public function create(Order $order)
    {
        // Check if user owns the order
        if (!$order->isAssignedTo(auth()->id())) {
            return back()->with('error', 'لا يمكنك نقل طلب لا يخصك');
        }

        // Get all admins except current user
        $admins = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'superadmin']);
        })->where('id', '!=', auth()->id())->get();

        return view('admin.orders.transfer-create', compact('order', 'admins'));
    }

    /**
     * Store transfer request.
     */
    public function store(Request $request, Order $order)
    {
        // Check if user owns the order
        if (!$order->isAssignedTo(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك نقل طلب لا يخصك'
            ], 403);
        }

        $request->validate([
            'to_admin_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            // Check if there's already a pending request for this order
            $existingRequest = OrderTransferRequest::where('order_id', $order->id)
                ->where('status', OrderTransferRequest::STATUS_PENDING)
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'يوجد طلب نقل معلق بالفعل لهذا الطلب'
                ], 400);
            }

            // Create transfer request
            $transferRequest = OrderTransferRequest::create([
                'order_id' => $order->id,
                'from_admin_id' => auth()->id(),
                'to_admin_id' => $request->to_admin_id,
                'reason' => $request->reason,
                'status' => OrderTransferRequest::STATUS_PENDING
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلب النقل بنجاح',
                'transfer_request' => [
                    'id' => $transferRequest->id,
                    'order_number' => $order->order_number,
                    'to_admin_name' => $transferRequest->toAdmin->name,
                    'reason' => $transferRequest->reason,
                    'created_at' => $transferRequest->created_at->format('Y-m-d H:i')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating transfer request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء طلب النقل'
            ], 500);
        }
    }

    /**
     * Approve transfer request.
     */
    public function approve(OrderTransferRequest $transferRequest)
    {
        // Check if current user is the target admin
        if ($transferRequest->to_admin_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك الموافقة على طلب نقل لا يخصك'
            ], 403);
        }

        // Check if request is still pending
        if (!$transferRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الطلب تم الرد عليه بالفعل'
            ], 400);
        }

        try {
            $transferRequest->approve();

            return response()->json([
                'success' => true,
                'message' => 'تمت الموافقة على نقل الطلب بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving transfer request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الموافقة على طلب النقل'
            ], 500);
        }
    }

    /**
     * Reject transfer request.
     */
    public function reject(Request $request, OrderTransferRequest $transferRequest)
    {
        // Check if current user is the target admin
        if ($transferRequest->to_admin_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك رفض طلب نقل لا يخصك'
            ], 403);
        }

        // Check if request is still pending
        if (!$transferRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الطلب تم الرد عليه بالفعل'
            ], 400);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        try {
            $transferRequest->reject($request->rejection_reason);

            return response()->json([
                'success' => true,
                'message' => 'تم رفض طلب النقل بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting transfer request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفض طلب النقل'
            ], 500);
        }
    }

    /**
     * Cancel transfer request (only by sender).
     */
    public function cancel(OrderTransferRequest $transferRequest)
    {
        // Check if current user is the sender
        if ($transferRequest->from_admin_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إلغاء طلب نقل لم ترسله'
            ], 403);
        }

        // Check if request is still pending
        if (!$transferRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء طلب تم الرد عليه بالفعل'
            ], 400);
        }

        try {
            $transferRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء طلب النقل بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error canceling transfer request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إلغاء طلب النقل'
            ], 500);
        }
    }

    /**
     * Get admins list for transfer.
     */
    public function getAdmins()
    {
        $admins = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'superadmin']);
        })->where('id', '!=', auth()->id())
        ->select('id', 'name', 'email')
        ->get();

        return response()->json([
            'success' => true,
            'admins' => $admins
        ]);
    }
}
