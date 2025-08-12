<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OrderFriend extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'friend_email',
        'friend_name',
        'friend_phone',
        'access_token',
        'is_active',
        'last_accessed_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_accessed_at' => 'datetime'
    ];

    /**
     * Get the order that owns the friend access.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who added the friend.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique access token.
     */
    public static function generateAccessToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('access_token', $token)->exists());

        return $token;
    }

    /**
     * Create a new friend access for an order.
     */
    public static function createFriendAccess($orderId, $userId, $friendEmail, $friendName, $friendPhone = null): self
    {
        return self::create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'friend_email' => $friendEmail,
            'friend_name' => $friendName,
            'friend_phone' => $friendPhone,
            'access_token' => self::generateAccessToken(),
            'is_active' => true
        ]);
    }

    /**
     * Update last accessed timestamp.
     */
    public function updateLastAccessed(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Deactivate friend access.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Check if friend access is valid.
     */
    public function isValid(): bool
    {
        return $this->is_active && $this->order && $this->order->exists;
    }
}
