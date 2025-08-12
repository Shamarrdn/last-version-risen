<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderFriend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderFriendController extends Controller
{
    /**
     * Add a friend to track an order.
     */
    public function addFriend(Request $request, Order $order)
    {
        // Check if user owns the order or is an admin
        if ($order->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إضافة صديق لطلب لا يخصك'
            ], 403);
        }

        // Check if admin_id is provided (for admin selection)
        if ($request->has('admin_id')) {
            $request->validate([
                'admin_id' => 'required|exists:users,id'
            ]);

            try {
                // Get admin details
                $admin = \App\Models\User::findOrFail($request->admin_id);
                
                // Check if admin already exists for this order
                $existingFriend = $order->friends()
                    ->where('friend_email', $admin->email)
                    ->first();

                if ($existingFriend) {
                    return response()->json([
                        'success' => false,
                        'message' => 'هذا الأدمن موجود بالفعل في قائمة الأصدقاء'
                    ], 400);
                }

                // Create friend access for admin
                $friend = OrderFriend::createFriendAccess(
                    $order->id,
                    auth()->id(),
                    $admin->email,
                    $admin->name,
                    $admin->phone ?? null
                );

                // Send email to admin with access link
                $this->sendFriendAccessEmail($friend);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة الأدمن بنجاح وتم إرسال رابط الوصول له',
                    'friend' => [
                        'id' => $friend->id,
                        'name' => $friend->friend_name,
                        'email' => $friend->friend_email,
                        'phone' => $friend->friend_phone,
                        'access_token' => $friend->access_token
                    ]
                ]);

            } catch (\Exception $e) {
                Log::error('Error adding admin as friend to order: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إضافة الأدمن'
                ], 500);
            }
        }

        // Original validation for manual friend addition
        $request->validate([
            'friend_email' => 'required|email',
            'friend_name' => 'required|string|max:255',
            'friend_phone' => 'nullable|string|max:20'
        ]);

        try {
            // Check if friend already exists for this order
            $existingFriend = $order->friends()
                ->where('friend_email', $request->friend_email)
                ->first();

            if ($existingFriend) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الصديق موجود بالفعل لهذا الطلب'
                ], 400);
            }

            // Create friend access
            $friend = OrderFriend::createFriendAccess(
                $order->id,
                auth()->id(),
                $request->friend_email,
                $request->friend_name,
                $request->friend_phone
            );

            // Send email to friend with access link
            $this->sendFriendAccessEmail($friend);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الصديق بنجاح وتم إرسال رابط الوصول له',
                'friend' => [
                    'id' => $friend->id,
                    'name' => $friend->friend_name,
                    'email' => $friend->friend_email,
                    'phone' => $friend->friend_phone,
                    'access_token' => $friend->access_token
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding friend to order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الصديق'
            ], 500);
        }
    }

    /**
     * Remove a friend from order tracking.
     */
    public function removeFriend(Request $request, Order $order, OrderFriend $friend)
    {
        // Check if user owns the order or is an admin
        if ($order->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إزالة صديق من طلب لا يخصك'
            ], 403);
        }

        try {
            $friend->deactivate();

            return response()->json([
                'success' => true,
                'message' => 'تم إزالة الصديق بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error removing friend from order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إزالة الصديق'
            ], 500);
        }
    }

    /**
     * Get order details via friend access token.
     */
    public function accessOrder($accessToken)
    {
        $friend = OrderFriend::where('access_token', $accessToken)
            ->where('is_active', true)
            ->with(['order.user', 'order.items.product', 'order.assignedAdmin'])
            ->first();

        if (!$friend) {
            return redirect()->route('home')->with('error', 'رابط الوصول غير صحيح أو منتهي الصلاحية');
        }

        // Update last accessed timestamp
        $friend->updateLastAccessed();

        return view('orders.friend-access', compact('friend'));
    }

    /**
     * Update order status via friend access.
     */
    public function updateOrderStatus(Request $request, $accessToken)
    {
        $friend = OrderFriend::where('access_token', $accessToken)
            ->where('is_active', true)
            ->with('order')
            ->first();

        if (!$friend) {
            return response()->json([
                'success' => false,
                'message' => 'رابط الوصول غير صحيح أو منتهي الصلاحية'
            ], 403);
        }

        $request->validate([
            'order_status' => 'required|in:pending,processing,completed,cancelled,out_for_delivery,on_the_way,delivered,returned'
        ]);

        try {
            $friend->order->update([
                'order_status' => $request->order_status
            ]);

            // Log the status change
            Log::info('Order status updated by friend', [
                'order_id' => $friend->order->id,
                'friend_id' => $friend->id,
                'friend_name' => $friend->friend_name,
                'new_status' => $request->order_status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب بنجاح',
                'new_status' => $request->order_status
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating order status by friend: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة الطلب'
            ], 500);
        }
    }

    /**
     * Get friends list for an order.
     */
    public function getFriends(Order $order)
    {
        // Check if user owns the order or is an admin
        if ($order->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك عرض أصدقاء طلب لا يخصك'
            ], 403);
        }

        $friends = $order->activeFriends()->get();

        return response()->json([
            'success' => true,
            'friends' => $friends->map(function ($friend) {
                return [
                    'id' => $friend->id,
                    'name' => $friend->friend_name,
                    'email' => $friend->friend_email,
                    'phone' => $friend->friend_phone,
                    'last_accessed' => $friend->last_accessed_at?->format('Y-m-d H:i'),
                    'created_at' => $friend->created_at->format('Y-m-d H:i')
                ];
            })
        ]);
    }

    /**
     * Send email to friend with access link.
     */
    private function sendFriendAccessEmail(OrderFriend $friend)
    {
        try {
            $accessUrl = route('orders.friend-access', $friend->access_token);
            
            // You can create a custom email template for this
            $subject = 'دعوة لتتبع الطلب - ' . $friend->order->order_number;
            $message = "
                مرحباً {$friend->friend_name}،
                
                تم دعوتك لتتبع الطلب رقم: {$friend->order->order_number}
                
                يمكنك الوصول للطلب من خلال الرابط التالي:
                {$accessUrl}
                
                هذا الرابط صالح حتى يتم إلغاؤه من قبل صاحب الطلب.
                
                مع تحيات،
                فريق " . config('app.name') . "
            ";

            // For now, we'll just log the email content
            // In production, you should use Laravel's Mail facade
            Log::info('Friend access email content', [
                'to' => $friend->friend_email,
                'subject' => $subject,
                'message' => $message,
                'access_url' => $accessUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending friend access email: ' . $e->getMessage());
        }
    }

    /**
     * Get orders that the current admin has been invited to track as a friend.
     */
    public function getInvitedOrders()
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول لهذه الصفحة'
            ], 403);
        }

        try {
            $userEmail = auth()->user()->email;
            Log::info('Getting invited orders for user: ' . $userEmail);
            
            // Get all OrderFriend records where this admin's email is the friend_email
            $invitedOrders = OrderFriend::where('friend_email', $userEmail)
                ->where('is_active', true)
                ->with(['order.user', 'order.items.product', 'order.assignedAdmin'])
                ->get();
            
            Log::info('Found ' . $invitedOrders->count() . ' invited orders for email: ' . $userEmail);
            
            // Also check if there are any records where user_id matches the current admin
            $adminInvitedOrders = OrderFriend::where('user_id', auth()->id())
                ->where('is_active', true)
                ->with(['order.user', 'order.items.product', 'order.assignedAdmin'])
                ->get();
            
            Log::info('Found ' . $adminInvitedOrders->count() . ' orders where admin is user_id');
            
            // Combine both results and remove duplicates
            $allInvitedOrders = $invitedOrders->merge($adminInvitedOrders)->unique('id');
            
            Log::info('Total unique invited orders: ' . $allInvitedOrders->count());
            
            $mappedOrders = $allInvitedOrders->map(function ($friend) {
                return [
                    'friend_id' => $friend->id,
                    'access_token' => $friend->access_token,
                    'order' => [
                        'id' => $friend->order->id,
                        'uuid' => $friend->order->uuid,
                        'order_number' => $friend->order->order_number,
                        'total_amount' => $friend->order->total_amount,
                        'order_status' => $friend->order->order_status,
                        'payment_status' => $friend->order->payment_status,
                        'created_at' => $friend->order->created_at->format('Y-m-d H:i'),
                        'customer' => [
                            'name' => $friend->order->user->name,
                            'email' => $friend->order->user->email,
                            'phone' => $friend->order->user->phone
                        ],
                        'assigned_admin' => $friend->order->assignedAdmin ? [
                            'name' => $friend->order->assignedAdmin->name,
                            'email' => $friend->order->assignedAdmin->email
                        ] : null,
                        'items_count' => $friend->order->items->count()
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'orders' => $mappedOrders,
                'debug' => [
                    'user_email' => $userEmail,
                    'user_id' => auth()->id(),
                    'total_found' => $allInvitedOrders->count(),
                    'by_email' => $invitedOrders->count(),
                    'by_user_id' => $adminInvitedOrders->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting invited orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل الطلبات المدعوة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the invited orders page for admins.
     */
    public function showInvitedOrders()
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        return view('admin.orders.invited');
    }
}
