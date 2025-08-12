<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuantityDiscount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'min_quantity',
        'max_quantity',
        'type',
        'value',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'value' => 'decimal:2'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateDiscount(float $price, int $quantity): float
    {
        $totalPrice = $price * $quantity;

        if ($this->type === 'fixed') {
            return $this->value;
        }

        // حساب الخصم كنسبة مئوية من السعر الإجمالي
        return $totalPrice * ($this->value / 100);
    }
}
