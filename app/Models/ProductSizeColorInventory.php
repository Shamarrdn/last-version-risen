<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSizeColorInventory extends Model
{
    use HasFactory;
    
    protected $table = 'product_size_color_inventory';
    
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'stock',
        'consumed_stock',
        'price',
        'is_available',
    ];
    
    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'consumed_stock' => 'integer',
    ];
    
    /**
     * Get the product that owns this inventory item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the size option for this inventory item
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class, 'size_id');
    }
    
    /**
     * Get the color option for this inventory item
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }
    
    /**
     * Get available stock (total stock minus consumed stock)
     */
    public function getAvailableStockAttribute()
    {
        return max(0, $this->stock - $this->consumed_stock);
    }
    
    /**
     * Check if the inventory item has available stock
     */
    public function hasAvailableStock($quantity)
    {
        return $this->available_stock >= $quantity;
    }
    
    /**
     * Consume stock from this inventory item
     */
    public function consumeStock($quantity)
    {
        $this->consumed_stock += $quantity;
        
        // If stock is depleted, mark as unavailable
        if ($this->available_stock <= 0) {
            $this->is_available = false;
        }
        
        $this->save();
        
        return $this;
    }
    
    /**
     * Return stock to this inventory item
     */
    public function returnStock($quantity)
    {
        $this->consumed_stock = max(0, $this->consumed_stock - $quantity);
        
        // If stock is available again, mark as available
        if ($this->available_stock > 0) {
            $this->is_available = true;
        }
        
        $this->save();
        
        return $this;
    }
}
