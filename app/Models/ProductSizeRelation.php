<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSizeRelation extends Model
{
    use HasFactory;

    protected $table = 'product_size_color_inventory';

    protected $fillable = [
        'product_id',
        'size',
        'size_id',
        'color_id',
        'is_available',
        'price',
        'stock'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'stock' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sizeOption()
    {
        return $this->belongsTo(ProductSize::class, 'size_id');
    }

    public function colorOption()
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }
}
