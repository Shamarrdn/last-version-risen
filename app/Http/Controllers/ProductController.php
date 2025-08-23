<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Services\Customer\Products\ProductService;
use App\Services\Customer\Products\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productService;
    protected $cartService;

    public function __construct(ProductService $productService, CartService $cartService)
    {
        $this->productService = $productService;
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getFilteredProducts($request);
        $categories = $this->productService->getCategories();
        $priceRange = $this->productService->getPriceRange();

        if ($request->ajax()) {
            return response()->json([
                'products' => $this->productService->formatProductsForJson($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ]);
        }

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }

    public function show(Product $product)
    {
        // تحميل البيانات من النظام الجديد مع جميع العلاقات
        $product->load([
            'category', 
            'images', 
            'inventory.color', 
            'inventory.size', 
            'quantityDiscounts'
        ]);

        // الحصول على معلومات الـ inventory مع التفاصيل الكاملة
        $inventoryData = $product->inventory()
            ->with(['color', 'size'])
            ->get();

        // حساب التوفر الفعلي من الـ inventory
        $totalAvailableStock = $inventoryData->sum(function($item) {
            return max(0, $item->stock - $item->consumed_stock);
        });

        $hasAnyStock = $totalAvailableStock > 0;

        // Debug: إذا لم يكن هناك مخزون، تحقق من النظام القديم
        if (!$hasAnyStock) {
            // تحقق من وجود stock في الجدول الأساسي
            $productHasOldStock = $product->stock && ($product->stock - ($product->consumed_stock ?? 0)) > 0;
            
            if ($productHasOldStock) {
                // استخدم النظام القديم مؤقتاً
                $hasAnyStock = true;
                $totalAvailableStock = max(0, $product->stock - ($product->consumed_stock ?? 0));
            }
        }

        // الحصول على العناصر المتاحة فقط
        $availableInventoryData = $inventoryData->filter(function($item) {
            return $item->is_available && ($item->stock - $item->consumed_stock) > 0;
        });

        // إذا لم توجد بيانات inventory، استخدم النظام القديم
        if ($availableInventoryData->isEmpty() && $hasAnyStock) {
            // إنشاء بيانات وهمية للنظام القديم
            $availableColors = collect();
            $availableSizes = collect();
            
            // إذا كان المنتج يدعم اختيار الألوان، اجلب الألوان العامة
            if ($product->enable_color_selection) {
                $availableColors = \App\Models\ProductColor::take(5)->get();
            }
            
            // إذا كان المنتج يدعم اختيار المقاسات، اجلب المقاسات العامة
            if ($product->enable_size_selection) {
                $availableSizes = \App\Models\ProductSize::take(6)->get();
            }
        } else {
            // الحصول على الألوان والمقاسات المتاحة
            $availableColors = $availableInventoryData->pluck('color')->unique('id')->filter();
            $availableSizes = $availableInventoryData->pluck('size')->unique('id')->filter();
        }

        // تجميع البيانات لسهولة الوصول - فقط العناصر المتاحة
        $sizeColorMatrix = [];
        
        if ($availableInventoryData->isNotEmpty()) {
            // النظام الجديد
            foreach($availableInventoryData as $item) {
                $sizeId = $item->size_id;
                $colorId = $item->color_id;
                
                if (!isset($sizeColorMatrix[$sizeId])) {
                    $sizeColorMatrix[$sizeId] = [
                        'size' => $item->size,
                        'colors' => []
                    ];
                }
                
                $sizeColorMatrix[$sizeId]['colors'][$colorId] = [
                    'color' => $item->color,
                    'price' => $item->price,
                    'available_stock' => max(0, $item->stock - $item->consumed_stock),
                    'variant_id' => $item->id
                ];
            }
        } else if ($hasAnyStock) {
            // النظام القديم - إنشاء matrix مبسط
            foreach($availableSizes as $size) {
                $sizeColorMatrix[$size->id] = [
                    'size' => $size,
                    'colors' => []
                ];
                
                foreach($availableColors as $color) {
                    $sizeColorMatrix[$size->id]['colors'][$color->id] = [
                        'color' => $color,
                        'price' => $product->base_price ?? 0,
                        'available_stock' => $totalAvailableStock,
                        'variant_id' => null
                    ];
                }
            }
        }

        // تحديث حالة التوفر في قاعدة البيانات إذا لزم الأمر
        if (!$hasAnyStock && $product->is_available) {
            $product->update(['is_available' => false]);
        } elseif ($hasAnyStock && !$product->is_available) {
            $product->update(['is_available' => true]);
        }

        // تحديث قيمة is_available للعرض
        $product->is_available = $hasAnyStock;

        $availableFeatures = $this->productService->getAvailableFeatures($product);
        $relatedProducts = $this->productService->getRelatedProducts($product);
        $quantityDiscounts = $product->quantityDiscounts()
            ->where('is_active', true)
            ->orderBy('min_quantity')
            ->get();

        // تحديد السعر للعرض
        $minPrice = null;
        $maxPrice = null;
        
        if ($availableInventoryData->isNotEmpty()) {
            // النظام الجديد - من الـ inventory
            $prices = $availableInventoryData->pluck('price')->filter();
            $minPrice = $prices->min();
            $maxPrice = $prices->max();
        } elseif ($hasAnyStock) {
            // النظام القديم - من الجدول الأساسي
            $minPrice = $maxPrice = $product->base_price ?? $product->price ?? 0;
        }

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'availableFeatures',
            'quantityDiscounts',
            'availableColors',
            'availableSizes',
            'inventoryData',
            'availableInventoryData',
            'sizeColorMatrix',
            'totalAvailableStock',
            'minPrice',
            'maxPrice'
        ));
    }

    public function filter(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'categories' => 'nullable|array',
                'categories.*' => 'nullable|string',
                'minPrice' => 'nullable|numeric|min:0',
                'maxPrice' => 'nullable|numeric|min:0',
                'sort' => 'nullable|string|in:newest,price-low,price-high',
                'has_discounts' => 'nullable|boolean'
            ]);

            $request->merge([
                'min_price' => $validatedData['minPrice'] ?? null,
                'max_price' => $validatedData['maxPrice'] ?? null,
                'sort' => $validatedData['sort'] ?? 'newest',
                'has_discounts' => $validatedData['has_discounts'] ?? null,
                'categories' => $validatedData['categories'] ?? []
            ]);

            if (!empty($validatedData['categories'])) {
                $request->merge(['category' => $validatedData['categories'][0]]);
            }

            $products = $this->productService->getFilteredProducts($request);

            return response()->json([
                'success' => true,
                'products' => $this->productService->formatProductsForFilter($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المنتجات - ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductDetails(Product $product)
    {
        if (!$product->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير متوفر حالياً'
            ], 404);
        }

        $product->load(['category', 'images', 'colors', 'sizes']);

        return response()->json($this->productService->getProductDetails($product));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:50',
            'color_id' => 'nullable|integer|exists:color_options,id',
            'size_id' => 'nullable|integer|exists:size_options,id'
        ]);

        $result = $this->cartService->addToCart($request);

        if (!$result['success']) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message']
            ], $result['status']);
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'cart_count' => $result['cart_count'],
            'cart_total' => $result['cart_total'],
            'product_name' => $result['product_name'],
            'product_id' => $result['product_id'],
            'cart_item_id' => $result['cart_item_id']
        ]);
    }

    public function getCartItems(Request $request)
    {
        return response()->json($this->cartService->getCartItems($request));
    }

    public function updateCartItem(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $result = $this->cartService->updateCartItem($cartItem, $request->quantity);

        return response()->json($result);
    }

    public function removeCartItem(CartItem $cartItem)
    {
        try {
            $result = $this->cartService->removeCartItem($cartItem);

            if (!$result['success']) {
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message']
                ], $result['status'] ?? 403);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج'
            ], 500);
        }
    }

    /**
     * الحصول على المقاسات المتاحة للون محدد
     */
    public function getSizesForColor(Request $request, Product $product)
    {
        $request->validate([
            'color_id' => 'required|exists:color_options,id'
        ]);

        try {
            $variants = \App\Models\ProductSizeColorInventory::where('product_id', $product->id)
                ->where('color_id', $request->color_id)
                ->where('is_available', true)
                ->where('stock', '>', 0)
                ->with(['size'])
                ->get();

            $sizes = $variants->map(function ($variant) {
                return [
                    'id' => $variant->size_id,
                    'name' => $variant->size->name ?? 'غير محدد',
                    'price' => $variant->price,
                    'available_stock' => $variant->available_stock,
                    'variant_id' => $variant->id
                ];
            });

            return response()->json([
                'success' => true,
                'sizes' => $sizes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المقاسات'
            ], 500);
        }
    }

    /**
     * الحصول على الألوان المتاحة للمقاس محدد
     */
    public function getColorsForSize(Request $request, Product $product)
    {
        $request->validate([
            'size_id' => 'required|exists:size_options,id'
        ]);

        try {
            $variants = \App\Models\ProductSizeColorInventory::where('product_id', $product->id)
                ->where('size_id', $request->size_id)
                ->where('is_available', true)
                ->where('stock', '>', 0)
                ->with(['color'])
                ->get();

            $colors = $variants->map(function ($variant) {
                return [
                    'id' => $variant->color_id,
                    'name' => $variant->color->name ?? 'غير محدد',
                    'code' => $variant->color->code ?? '#007bff',
                    'price' => $variant->price,
                    'available_stock' => $variant->available_stock,
                    'variant_id' => $variant->id
                ];
            });

            return response()->json([
                'success' => true,
                'colors' => $colors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الألوان'
            ], 500);
        }
    }

    /**
     * الحصول على تفاصيل الـ variant
     */
    public function getVariantDetails(Request $request, Product $product)
    {
        $request->validate([
            'size_id' => 'nullable|exists:size_options,id',
            'color_id' => 'nullable|exists:color_options,id'
        ]);

        try {
            $variant = \App\Models\ProductSizeColorInventory::where('product_id', $product->id)
                ->where('size_id', $request->size_id)
                ->where('color_id', $request->color_id)
                ->first();

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الخيار غير متوفر'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'variant' => [
                    'id' => $variant->id,
                    'price' => $variant->price,
                    'available_stock' => $variant->available_stock,
                    'is_available' => $variant->is_available
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الخيار'
            ], 500);
        }
    }


}
