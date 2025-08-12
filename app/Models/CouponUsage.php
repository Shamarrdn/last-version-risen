<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime'
    ];

    /**
     * العلاقة مع جدول الكوبونات
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * العلاقة مع جدول المستخدمين
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع جدول الطلبات
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
