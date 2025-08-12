<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // التوجيه بناءً على دور المستخدم
        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } else {
            // العملاء العاديين
            // إضافة المتغيرات الأساسية المطلوبة للعرض
            $stats = [
                'orders_count' => 0,
                'cart_items_count' => 0,
                'unread_notifications' => 0
            ];
            
            $addresses = collect();
            $phones = collect();
            $recent_orders = collect();
            
            return view('dashboard', compact('stats', 'addresses', 'phones', 'recent_orders'));
        }
    }
}