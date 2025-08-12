<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTransferRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'from_admin_id',
        'to_admin_id',
        'reason',
        'status',
        'rejection_reason',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the order that owns the transfer request.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin who initiated the transfer.
     */
    public function fromAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_admin_id');
    }

    /**
     * Get the admin who will receive the transfer.
     */
    public function toAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_admin_id');
    }

    /**
     * Check if the transfer request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the transfer request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the transfer request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Approve the transfer request.
     */
    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'responded_at' => now()
        ]);

        // Transfer the order to the new admin
        $this->order->update([
            'assigned_admin_id' => $this->to_admin_id,
            'assigned_at' => now()
        ]);
    }

    /**
     * Reject the transfer request.
     */
    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'responded_at' => now()
        ]);
    }

    /**
     * Get status text in Arabic.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_APPROVED => 'تمت الموافقة',
            self::STATUS_REJECTED => 'مرفوض',
            default => 'غير معروف'
        };
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary'
        };
    }
}
