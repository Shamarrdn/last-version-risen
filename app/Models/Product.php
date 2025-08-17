<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
  use HasFactory, Searchable;

  protected $fillable = [
    'name',
    'slug',
    'description',
    'details',
    'base_price',
    'is_available',
    'category_id',
    'stock',
    'consumed_stock',
    'enable_custom_color',
    'enable_custom_size',
    'enable_color_selection',
    'enable_size_selection'
  ];

  protected $casts = [
    'is_available' => 'boolean',
    'enable_custom_color' => 'boolean',
    'enable_custom_size' => 'boolean',
    'enable_color_selection' => 'boolean',
    'enable_size_selection' => 'boolean',
    'details' => 'array'
  ];

  protected $searchableFields = [
    'name',
    'description',
    'sku'
  ];

  protected $filterableFields = [
    'category_id',
    'price',
    'stock',
    'featured'
  ];

  protected $appends = [
    'image_url',
    'all_images'
  ];

  public function getRouteKeyName()
  {
    return 'slug';
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  /**
   * علاقة many-to-many مع التصنيفات للكوبونات
   */
  public function categories(): BelongsToMany
  {
    return $this->belongsToMany(Category::class, 'category_product');
  }

  public function images(): HasMany
  {
    return $this->hasMany(ProductImage::class);
  }

      public function colors(): BelongsToMany
  {
    return $this->belongsToMany(ProductColor::class, 'product_size_color_inventory', 'product_id', 'color_id')
      ->distinct();
  }
  
  public function inventory(): HasMany
  {
    return $this->hasMany(ProductSizeColorInventory::class, 'product_id');
  }

  public function sizes(): BelongsToMany
  {
    return $this->belongsToMany(ProductSize::class, 'product_size_color_inventory', 'product_id', 'size_id')
      ->distinct();
  }

  public function orderItems(): HasMany
  {
    return $this->hasMany(OrderItem::class);
  }

  public function discounts()
  {
    return $this->belongsToMany(Coupon::class, 'coupon_product');
  }

  public function quantityDiscounts()
  {
    return $this->hasMany(QuantityDiscount::class);
  }

  /**
   * الحصول على الكوبونات الصالحة المتاحة لهذا المنتج
   *
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function getAvailableCoupons()
  {
    // جمع كل الكوبونات المتاحة للمنتج
    $productCoupons = $this->discounts()->where('is_active', true)->get();

    // جمع الكوبونات المتاحة لجميع المنتجات
    $globalCoupons = Coupon::where('is_active', true)
                           ->where('applies_to_all_products', true)
                           ->get();

    // جمع الكوبونات المتاحة عبر فئة المنتج
    $categoryCoupons = collect();
    if ($this->category_id) {
      $categoryCoupons = Coupon::where('is_active', true)
                              ->whereHas('categories', function ($query) {
                                $query->where('category_id', $this->category_id);
                              })
                              ->get();
    }

    // دمج المجموعات
    $allCoupons = $productCoupons->merge($globalCoupons)->merge($categoryCoupons);

    // تصفية الكوبونات لإزالة المكررات والتحقق من الصلاحية
    return $allCoupons->unique('id')->filter(function ($coupon) {
      return $coupon->isValid();
    });
  }

  public function scopePriceRange(Builder $query, $min = null, $max = null): Builder
  {
    if ($min !== null) {
      $query->whereHas('sizes', function($q) use ($min) {
        $q->where('price', '>=', $min);
      });
    }

    if ($max !== null) {
      $query->whereHas('sizes', function($q) use ($max) {
        $q->where('price', '<=', $max);
      });
    }

    return $query;
  }

  public function scopeFeatured(Builder $query): Builder
  {
    return $query->where('featured', true);
  }

  public function scopeInStock(Builder $query): Builder
  {
    return $query->where('is_available', true);
  }

  public function getPrimaryImageAttribute()
  {
    return $this->images->where('is_primary', true)->first()
      ?? $this->images->first();
  }

  public function getImageUrlAttribute()
  {
    if ($image = $this->primary_image) {
      return Storage::url($image->image_path);
    }
    return asset('images/placeholder.jpg');
  }

  public function getAllImagesAttribute()
  {
    return $this->images->map(function($image) {
      return Storage::url($image->image_path);
    })->toArray();
  }

  public function getAllowCustomColorAttribute()
  {
    return $this->enable_custom_color;
  }

  /**
   * الحصول على الألوان المتاحة من النظام الجديد
   */
  public function getAvailableColorsAttribute()
  {
    return $this->inventory()
      ->where('is_available', true)
      ->where('stock', '>', 0)
      ->with('color')
      ->get()
      ->pluck('color')
      ->unique('id')
      ->filter();
  }

  /**
   * الحصول على المقاسات المتاحة من النظام الجديد
   */
  public function getAvailableSizesAttribute()
  {
    return $this->inventory()
      ->where('is_available', true)
      ->where('stock', '>', 0)
      ->with('size')
      ->get()
      ->pluck('size')
      ->unique('id')
      ->filter();
  }

  /**
   * الحصول على السعر الأدنى من النظام الجديد
   */
  public function getMinPriceFromInventoryAttribute()
  {
    $minPrice = $this->inventory()
      ->where('is_available', true)
      ->where('stock', '>', 0)
      ->min('price');

    return $minPrice ?? $this->base_price ?? 0;
  }

  /**
   * الحصول على السعر الأقصى من النظام الجديد
   */
  public function getMaxPriceFromInventoryAttribute()
  {
    $maxPrice = $this->inventory()
      ->where('is_available', true)
      ->where('stock', '>', 0)
      ->max('price');

    return $maxPrice ?? $this->base_price ?? 0;
  }

  /**
   * الحصول على variant محدد
   */
  public function getVariant($colorId = null, $sizeId = null)
  {
    return $this->inventory()
      ->where('color_id', $colorId)
      ->where('size_id', $sizeId)
      ->where('is_available', true)
      ->first();
  }

  /**
   * التحقق من توفر variant
   */
  public function hasVariant($colorId = null, $sizeId = null, $quantity = 1)
  {
    $variant = $this->getVariant($colorId, $sizeId);
    return $variant && $variant->available_stock >= $quantity;
  }

  public function getAllowCustomSizeAttribute()
  {
    return $this->enable_custom_size;
  }

  public function getAllowColorSelectionAttribute()
  {
    return $this->enable_color_selection;
  }

  public function getAllowSizeSelectionAttribute()
  {
    return $this->enable_size_selection;
  }

  public function toArray()
  {
    $array = parent::toArray();
    $array['category_name'] = $this->category->name ?? null;
    $array['price_range'] = $this->getPriceRange();
    return $array;
  }

  /**
   * Get the minimum price for this product
   * Based on sizes only
   */
  public function getMinPriceAttribute()
  {
    $prices = [];

    if ($this->enable_size_selection && $this->sizes->isNotEmpty()) {
      $sizesPrices = $this->sizes->pluck('price')->filter()->toArray();
      if (!empty($sizesPrices)) {
        $prices[] = min($sizesPrices);
      }
    }

    // If no sizes with prices, use base_price if available
    if (empty($prices)) {
      return $this->base_price ?? 0;
    }

    return !empty($prices) ? min($prices) : 0;
  }

  /**
   * Get the maximum price for this product
   * Based on sizes only
   */
  public function getMaxPriceAttribute()
  {
    $prices = [];

    if ($this->enable_size_selection && $this->sizes->isNotEmpty()) {
      $sizesPrices = $this->sizes->pluck('price')->filter()->toArray();
      if (!empty($sizesPrices)) {
        $prices[] = max($sizesPrices);
      }
    }

    // If no sizes with prices, use base_price if available
    if (empty($prices)) {
      return $this->base_price ?? 0;
    }

    return !empty($prices) ? max($prices) : 0;
  }

  /**
   * Get the price range for this product
   */
  public function getPriceRange()
  {
    $prices = [];

    if ($this->enable_size_selection && $this->sizes->isNotEmpty()) {
      $sizesPrices = $this->sizes->pluck('price')->filter()->toArray();
      if (!empty($sizesPrices)) {
        $prices = $sizesPrices;
      }
    }

    if (empty($prices) && $this->base_price) {
      // If we have a base price but no size prices, use the base price for both min and max
      return [
        'min' => $this->base_price,
        'max' => $this->base_price
      ];
    } else if (empty($prices)) {
      // If no prices at all, return zeros
      return [
        'min' => 0,
        'max' => 0
      ];
    }

    return [
      'min' => min($prices),
      'max' => max($prices)
    ];
  }

  /**
   * Check if the product has any active discounts
   *
   * @return bool
   */
  public function hasDiscounts()
  {
    // Check if product has available coupons
    if ($this->getAvailableCoupons()->isNotEmpty()) {
      return true;
    }

    // Check if product has active quantity discounts
    if ($this->quantityDiscounts()->where('is_active', true)->exists()) {
      return true;
    }

    return false;
  }


  public function getAvailableStockAttribute()
  {
    return max(0, $this->stock - $this->consumed_stock);
  }

  public function hasAvailableStock($quantity)
  {
    return $this->available_stock >= $quantity;
  }


  /**
   * استهلاك كمية من المخزون
   */
  public function consumeStock($quantity)
  {
    $this->consumed_stock += $quantity;

    // إذا نفذ المخزون، تغيير حالة المنتج
    if ($this->available_stock <= 0) {
      $this->is_available = false;
    }

    $this->save();

    return $this;
  }

  /**
   * إرجاع كمية من المخزون المستهلك
   */
  public function returnStock($quantity)
  {
    $this->consumed_stock = max(0, $this->consumed_stock - $quantity);

    // إذا عاد المخزون، تفعيل المنتج
    if ($this->available_stock > 0) {
      $this->is_available = true;
    }

    $this->save();

    return $this;
  }

  /**
   * التحقق من نفاد المخزون
   */
  public function isOutOfStock()
  {
    return $this->available_stock <= 0;
  }
}
