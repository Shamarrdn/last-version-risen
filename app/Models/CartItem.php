<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CartItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'cart_id',
    'product_id',
    'variant_id',
    'quantity',
    'unit_price',
    'subtotal',
    'color',
    'size',
    'color_id',
    'size_id'
  ];

  protected $casts = [
    'unit_price' => 'integer',
    'subtotal' => 'integer',
    'quantity' => 'integer'
  ];

  public function cart(): BelongsTo
  {
    return $this->belongsTo(Cart::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function variant(): BelongsTo
  {
    return $this->belongsTo(ProductSizeColorInventory::class, 'variant_id');
  }

  public function colorOption(): BelongsTo
  {
    return $this->belongsTo(ProductColor::class, 'color_id');
  }

  public function sizeOption(): BelongsTo
  {
    return $this->belongsTo(ProductSize::class, 'size_id');
  }
}
