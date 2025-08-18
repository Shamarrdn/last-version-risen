<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole(['admin', 'superadmin'])) {
                return redirect('/dashboard');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'sizes', 'categories'])
            ->withCount('orderItems');

        if ($request->product) {
            $query->where('id', $request->product);
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'price_high':
                $query->orderBy(function ($q) {
                    return $q->select(DB::raw('MAX(COALESCE(ps.price, 0))'))
                        ->from('products as p')
                        ->leftJoin('product_size_color_inventory as ps', 'p.id', '=', 'ps.product_id')
                        ->whereColumn('p.id', 'products.id')
                        ->limit(1);
                }, 'desc');
                break;
            case 'price_low':
                $query->orderBy(function ($q) {
                    return $q->select(DB::raw('MIN(COALESCE(ps.price, 0))'))
                        ->from('products as p')
                        ->leftJoin('product_size_color_inventory as ps', 'p.id', '=', 'ps.product_id')
                        ->whereColumn('p.id', 'products.id')
                        ->limit(1);
                });
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(15);
        $categories = Category::all();
        $allProducts = Product::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'allProducts'));
    }

    public function create()
    {
        $categories = Category::all();
        
        // التأكد من وجود المقاسات والألوان الافتراضية
        $this->ensureDefaultSizesAndColors();
        
        $availableSizes = \App\Models\ProductSize::all();
        $availableColors = \App\Models\ProductColor::all();
        
        return view('admin.products.create', compact('categories', 'availableSizes', 'availableColors'));
    }

    public function store(Request $request)
    {
        // ===== DEBUG LEVEL 1: Raw Request Data =====
        Log::info('🔍 [DEBUG LEVEL 1] Raw Request Data - STORE', $request->all());
        
        // ===== DEBUG LEVEL 1.5: Specific Field Analysis =====
        Log::info('🔍 [DEBUG LEVEL 1.5] Specific Field Analysis - STORE', [
            'selected_sizes' => $request->get('selected_sizes'),
            'selected_colors' => $request->get('selected_colors'),
            'stock_data' => $request->get('stock'),
            'price_data' => $request->get('price'),
            'inventories' => $request->get('inventories'),
            'stock_data_type' => gettype($request->get('stock')),
            'price_data_type' => gettype($request->get('price')),
            'all_request_keys' => array_keys($request->all()),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        // قواعد الفاليديشن الأساسية
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg',
            'is_primary.*' => 'boolean',
            'is_available' => 'boolean',
            'enable_custom_color' => 'boolean',
            'enable_custom_size' => 'boolean',
            'enable_color_selection' => 'boolean',
            'enable_size_selection' => 'boolean',
            'detail_keys.*' => 'nullable|string|max:255',
            'detail_values.*' => 'nullable|string|max:255',
            'base_price' => 'nullable|numeric|min:0',
            'stock' => 'required|numeric|min:0|max:999999',
            
            // قواعد الفاليديشن للمقاسات والألوان - محدثة لتتطابق مع النماذج
            'selected_sizes' => 'nullable|array',
            'selected_sizes.*' => 'exists:size_options,id',
            'selected_colors' => 'nullable|array',
            'selected_colors.*' => 'exists:color_options,id',
            
            // قواعد الفاليديشن للـ variants (الشكل الجديد)
            'variants' => 'nullable|array',
            'variants.*.size_id' => 'nullable|exists:size_options,id',
            'variants.*.color_id' => 'nullable|exists:color_options,id',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.is_available' => 'nullable|boolean',
            
            // قواعد الفاليديشن للشكل المتداخل
            'inventory' => 'nullable|array',
            
            // قواعد الفاليديشن للنمط الجديد
            'inventories' => 'nullable|array',
            'inventories.*.*.color_id' => 'nullable|exists:color_options,id',
            'inventories.*.*.stock' => 'nullable|integer|min:0',
            'inventories.*.*.price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // معالجة البيانات الأساسية
            if (empty($request->slug)) {
                $validated['slug'] = $this->generateSlugFromName($request->name);
            } else {
                $validated['slug'] = $this->generateUniqueSlug($request->slug);
            }

            // معالجة التفاصيل
            $details = [];
            if ($request->has('detail_keys') && $request->has('detail_values') && 
                is_array($request->detail_keys) && is_array($request->detail_values)) {
                foreach ($request->detail_keys as $index => $key) {
                    if (!empty($key) && isset($request->detail_values[$index]) && !empty($request->detail_values[$index])) {
                        $details[$key] = $request->detail_values[$index];
                    }
                }
            }
            $validated['details'] = !empty($details) ? $details : null;

            // معالجة الحقول البولينية
            $validated['enable_custom_color'] = $request->has('enable_custom_color');
            $validated['enable_custom_size'] = $request->has('enable_custom_size');
            $validated['enable_color_selection'] = $request->has('enable_color_selection');
            $validated['enable_size_selection'] = $request->has('enable_size_selection');
            $validated['is_available'] = $request->has('is_available');
            $validated['stock'] = intval($validated['stock']);

            // إنشاء المنتج
            $product = Product::create($validated);

            // ربط التصنيفات
            if ($request->has('categories') && is_array($request->categories)) {
                $product->categories()->attach($request->categories);
            }

            // ===== DEBUG LEVEL 2: Before inventory processing =====
            Log::info('🔍 [DEBUG LEVEL 2] About to process inventory - STORE', [
                'product_id' => $product->id,
                'request_keys' => array_keys($request->all()),
                'has_inventories' => $request->has('inventories'),
                'inventories_data' => $request->get('inventories'),
                'all_request_data' => $request->all()
            ]);
            
            // معالجة المخزون - النظام الجديد
            if ($request->has('inventories') && is_array($request->inventories)) {
                Log::info('🔍 [DEBUG LEVEL 3] Processing inventories data - STORE', [
                    'product_id' => $product->id,
                    'inventories_count' => count($request->inventories),
                    'inventories' => $request->inventories
                ]);
                
                foreach ($request->inventories as $sizeId => $colors) {
                    foreach ($colors as $colorId => $data) {
                        Log::info('🔍 [DEBUG LEVEL 4] Processing inventory item - STORE', [
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                            'color_id' => $colorId,
                            'data' => $data
                        ]);
                        
                        \App\Models\ProductSizeColorInventory::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'size_id'    => $sizeId,
                                'color_id'   => $colorId,
                            ],
                            [
                                'stock'        => $data['stock'] ?? 0,
                                'price'        => $data['price'] ?? 0,
                                'is_available' => 1,
                            ]
                        );
                        
                        Log::info('✅ Inventory item created/updated successfully', [
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                            'color_id' => $colorId,
                            'stock' => $data['stock'] ?? 0,
                            'price' => $data['price'] ?? 0
                        ]);
                    }
                }
            } else {
                // Fallback للنظام القديم
                Log::info('🔍 [DEBUG LEVEL 3] Using fallback inventory system - STORE', [
                    'product_id' => $product->id,
                    'has_selected_sizes' => $request->has('selected_sizes'),
                    'has_selected_colors' => $request->has('selected_colors')
                ]);
                
                $rows = $this->normalizeVariantsFromRequest($request, $product->id);

                if (!empty($rows)) {
                    try {
                        // تأكد من أن جميع السجلات لها color_id صحيح
                        $validRows = array_filter($rows, function($row) {
                            return !empty($row['color_id']) && $row['color_id'] !== null && $row['color_id'] !== 'null';
                        });
                        
                        if (!empty($validRows)) {
                            \App\Models\ProductSizeColorInventory::upsert(
                                $validRows,
                                ['product_id', 'size_id', 'color_id'],
                                ['stock', 'price', 'is_available']
                            );
                            \Log::info('ProductSizeColorInventory upsert completed successfully', [
                                'product_id' => $product->id,
                                'rows_count' => count($validRows),
                                'first_row' => $validRows[0] ?? null
                            ]);
                        } else {
                            \Log::warning('No valid variants to store (all had null color_id)', [
                                'product_id' => $product->id,
                                'original_rows_count' => count($rows)
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('[VARIANTS_DEBUG][UPSERT_ERROR] ' . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        throw new \Exception('فشل في حفظ بيانات المقاسات والألوان: ' . $e->getMessage());
                    }
                }
            }

            // ربط الألوان والمقاسات كـ pivot للفلترة والتقارير
            if ($request->has('selected_colors') && is_array($request->selected_colors)) {
                $product->colors()->sync($request->selected_colors);
            }
            
            if ($request->has('selected_sizes') && is_array($request->selected_sizes)) {
                $product->sizes()->sync($request->selected_sizes);
            }

            // معالجة الصور
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->uploadFile($image, 'products');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => $request->input('is_primary.' . $index, false) ? true : false
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'تم إضافة المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل إضافة المنتج. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        
        // التأكد من وجود المقاسات والألوان الافتراضية
        $this->ensureDefaultSizesAndColors();
        
        $availableSizes = \App\Models\ProductSize::all();
        $availableColors = \App\Models\ProductColor::all();

        $selectedCategories = $product->categories->pluck('id')->toArray();
        
        // تحميل البيانات الموجودة من النظام الجديد - تحسين التحميل
        $product->load([
            'images', 
            'categories',
            'inventory' => function($query) {
                $query->with(['size', 'color']);
            }
        ]);
        
        // تجهيز البيانات للمقاسات والألوان الموجودة
        $selectedSizes = [];
        $selectedColors = [];
        $stockData = [];
        $priceData = [];
        
        // تجميع البيانات من inventory
        foreach ($product->inventory as $inventory) {
            if ($inventory->size) {
                $sizeId = $inventory->size_id;
                $colorId = $inventory->color_id;
                
                // إضافة المقاس إذا لم يكن موجود
                if (!in_array($sizeId, $selectedSizes)) {
                    $selectedSizes[] = $sizeId;
                }
                
                // إضافة اللون إذا لم يكن موجود وكان صحيحاً
                if ($colorId && $colorId !== null && $colorId !== 'null' && !in_array($colorId, $selectedColors)) {
                    $selectedColors[] = $colorId;
                }
                
                // إضافة بيانات المخزون والسعر فقط إذا كان color_id صحيح
                if ($colorId && $colorId !== null && $colorId !== 'null') {
                    if (!isset($stockData[$sizeId])) {
                        $stockData[$sizeId] = [];
                    }
                    if (!isset($priceData[$sizeId])) {
                        $priceData[$sizeId] = [];
                    }
                    
                    $stockData[$sizeId][$colorId] = $inventory->stock;
                    $priceData[$sizeId][$colorId] = $inventory->price;
                }
            }
        }
        
        // تجهيز ماب يساعدك تملي الفورم - فقط السجلات التي لها color_id صحيح
        $inventoryMap = $product->inventory
            ->filter(function ($row) {
                return $row->color_id && $row->color_id !== null && $row->color_id !== 'null';
            })
            ->map(function ($row) {
                return [
                    'id'           => $row->id,
                    'size_id'      => $row->size_id,
                    'color_id'     => $row->color_id,
                    'stock'        => $row->stock,
                    'consumed'     => $row->consumed_stock ?? 0,
                    'price'        => $row->price,
                    'is_available' => $row->is_available,
                    'size_name'    => $row->size ? $row->size->name : null,
                    'color_name'   => $row->color ? $row->color->name : null,
                ];
            })->values();
            
        return view('admin.products.edit', compact(
            'product', 
            'categories', 
            'availableSizes', 
            'availableColors', 
            'selectedCategories', 
            'inventoryMap',
            'selectedSizes',
            'selectedColors',
            'stockData',
            'priceData'
        ));
    }

    public function update(Request $request, Product $product)
    {
        // ===== DEBUG LEVEL 1: Raw Request Data =====
        Log::info('🔍 [DEBUG LEVEL 1] Raw Request Data - UPDATE', $request->all());
        
        // ===== DEBUG LEVEL 1.5: Specific Field Analysis =====
        Log::info('🔍 [DEBUG LEVEL 1.5] Specific Field Analysis - UPDATE', [
            'selected_sizes' => $request->get('selected_sizes'),
            'selected_colors' => $request->get('selected_colors'),
            'stock_data' => $request->get('stock'),
            'price_data' => $request->get('price'),
            'inventories' => $request->get('inventories'),
            'stock_data_type' => gettype($request->get('stock')),
            'price_data_type' => gettype($request->get('price')),
            'all_request_keys' => array_keys($request->all()),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        try {
            // قواعد الفاليديشن الأساسية
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:categories,id',
                'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg',
                'is_primary' => 'nullable|exists:product_images,id',
                'is_primary_new.*' => 'nullable|boolean',
                'remove_images.*' => 'nullable|exists:product_images,id',
                'enable_custom_color' => 'boolean',
                'enable_custom_size' => 'boolean',
                'enable_color_selection' => 'boolean',
                'enable_size_selection' => 'boolean',
                'detail_keys.*' => 'nullable|string|max:255',
                'detail_values.*' => 'nullable|string|max:255',
                'base_price' => 'nullable|numeric|min:0',
                'stock' => 'required|integer|min:0',
                
                // قواعد الفاليديشن للمقاسات والألوان
                'selected_sizes' => 'nullable|array',
                'selected_sizes.*' => 'exists:size_options,id',
                'selected_colors' => 'nullable|array',
                'selected_colors.*' => 'exists:color_options,id',
                
                // قواعد الفاليديشن للـ variants (الشكل الجديد)
                'variants' => 'nullable|array',
                'variants.*.size_id' => 'nullable|exists:size_options,id',
                'variants.*.color_id' => 'nullable|exists:color_options,id',
                'variants.*.stock' => 'nullable|integer|min:0',
                'variants.*.price' => 'nullable|numeric|min:0',
                'variants.*.is_available' => 'nullable|boolean',
                
                // قواعد الفاليديشن للشكل المتداخل
                'inventory' => 'nullable|array',
                
                // قواعد الفاليديشن للنمط الجديد
                'inventories' => 'nullable|array',
                'inventories.*.*.color_id' => 'nullable|exists:color_options,id',
                'inventories.*.*.stock' => 'nullable|integer|min:0',
                'inventories.*.*.price' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            // معالجة البيانات الأساسية
            if (empty($request->slug)) {
                $validated['slug'] = $this->generateSlugFromName($request->name, $product->id);
            } else if ($validated['slug'] !== $product->slug) {
                $validated['slug'] = $this->generateUniqueSlug($validated['slug'], 1, $product->id);
            }

            // معالجة التفاصيل
            $details = [];
            if ($request->has('detail_keys') && $request->has('detail_values') && 
                is_array($request->detail_keys) && is_array($request->detail_values)) {
                foreach ($request->detail_keys as $index => $key) {
                    if (!empty($key) && isset($request->detail_values[$index]) && !empty($request->detail_values[$index])) {
                        $details[$key] = $request->detail_values[$index];
                    }
                }
            }

            // تحديث المنتج
            $product->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'details' => !empty($details) ? $details : null,
                'base_price' => $request->base_price,
                'category_id' => $validated['category_id'],
                'enable_custom_color' => $request->has('enable_custom_color'),
                'enable_custom_size' => $request->has('enable_custom_size'),
                'enable_color_selection' => $request->has('enable_color_selection'),
                'enable_size_selection' => $request->has('enable_size_selection'),
                'is_available' => $request->has('is_available'),
                'stock' => $request->input('stock', 0),
            ]);

            // ربط التصنيفات
            $product->categories()->sync(is_array($request->categories) ? $request->categories : []);

            // ===== DEBUG LEVEL 2: Before inventory processing =====
            Log::info('🔍 [DEBUG LEVEL 2] About to process inventory - UPDATE', [
                'product_id' => $product->id,
                'request_keys' => array_keys($request->all()),
                'has_inventories' => $request->has('inventories'),
                'inventories_data' => $request->get('inventories'),
                'all_request_data' => $request->all()
            ]);
            
            // معالجة المخزون - النظام الجديد
            if ($request->has('inventories') && is_array($request->inventories)) {
                Log::info('🔍 [DEBUG LEVEL 3] Processing inventories data - UPDATE', [
                    'product_id' => $product->id,
                    'inventories_count' => count($request->inventories),
                    'inventories' => $request->inventories
                ]);
                
                foreach ($request->inventories as $sizeId => $colors) {
                    foreach ($colors as $colorId => $data) {
                        Log::info('🔍 [DEBUG LEVEL 4] Processing inventory item - UPDATE', [
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                            'colorId' => $colorId,
                            'data' => $data
                        ]);
                        
                        \App\Models\ProductSizeColorInventory::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'size_id'    => $sizeId,
                                'color_id'   => $colorId,
                            ],
                            [
                                'stock'        => $data['stock'] ?? 0,
                                'price'        => $data['price'] ?? 0,
                                'is_available' => 1,
                            ]
                        );
                        
                        Log::info('✅ Inventory item updated successfully', [
                            'product_id' => $product->id,
                            'size_id' => $sizeId,
                            'color_id' => $colorId,
                            'stock' => $data['stock'] ?? 0,
                            'price' => $data['price'] ?? 0
                        ]);
                    }
                }
            } else {
                // Fallback للنظام القديم
                Log::info('🔍 [DEBUG LEVEL 3] Using fallback inventory system - UPDATE', [
                    'product_id' => $product->id,
                    'has_selected_sizes' => $request->has('selected_sizes'),
                    'has_selected_colors' => $request->has('selected_colors')
                ]);
                
                $rows = $this->normalizeVariantsFromRequest($request, $product->id);

                \Log::info('ProductController update - Normalized variants:', [
                    'product_id' => $product->id,
                    'rows_count' => count($rows),
                    'request_data' => [
                        'selected_sizes' => $request->get('selected_sizes'),
                        'selected_colors' => $request->get('selected_colors'),
                        'has_stock_data' => $request->has('stock'),
                        'has_price_data' => $request->has('price'),
                    ]
                ]);
                
                // التحقق من إذا تم طلب حذف كل المخزون بشكل صريح
                $explicitlyDeleteAll = $request->has('delete_all_inventory') && $request->delete_all_inventory;

                if (!empty($rows)) {
                    // ===== DEBUG LEVEL 3: Before upsert =====
                    Log::info('🔍 [DEBUG LEVEL 3] Variants to upsert - UPDATE', [
                        'product_id' => $product->id,
                        'variants_count' => count($rows),
                        'variants' => $rows
                    ]);
                    
                    try {
                        // 1. قبل حذف أي شيء، تأكد من أن هناك بيانات فعلية
                        $hasSizeData = $request->has('selected_sizes') && !empty($request->selected_sizes);
                        $hasColorData = $request->has('selected_colors') && !empty($request->selected_colors);
                        
                        // 2. إذا حدد المستخدم مقاسات/ألوان جديدة، تحديث فقط ما تغير
                        if (($hasSizeData || $hasColorData) && !$explicitlyDeleteAll) {
                            // احذف فقط الـ variants اللي اتشالت من الفورم مع الحفاظ على البقية
                            $this->deleteMissingVariants($product, $rows);
                            \Log::info('Updated only missing variants', [
                                'has_size_data' => $hasSizeData,
                                'has_color_data' => $hasColorData
                            ]);
                        } else {
                            \Log::info('Keeping existing variants since no selection data was provided');
                        }
                        
                        // 3. تحديث أو إضافة البيانات الجديدة - تأكد من أن color_id صحيح
                        $validRows = array_filter($rows, function($row) {
                            return !empty($row['color_id']) && $row['color_id'] !== null && $row['color_id'] !== 'null';
                        });
                        
                        if (!empty($validRows)) {
                            \App\Models\ProductSizeColorInventory::upsert(
                                $validRows,
                                ['product_id', 'size_id', 'color_id'],
                                ['stock', 'price', 'is_available']
                            );
                        } else {
                            \Log::warning('No valid variants to update (all had null color_id)', [
                                'product_id' => $product->id,
                                'original_rows_count' => count($rows)
                            ]);
                        }
                        
                        \Log::info('ProductSizeColorInventory update completed successfully', [
                            'product_id' => $product->id,
                            'rows_count' => count($rows),
                            'first_few_rows' => array_slice($rows, 0, 3)
                        ]);
                    } catch (\Exception $e) {
                        Log::error('[VARIANTS_DEBUG][UPSERT_ERROR] ' . $e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        throw new \Exception('فشل في تحديث بيانات المقاسات والألوان: ' . $e->getMessage());
                    }
                } else {
                    // حالة خاصة: طلب صريح لحذف كل المخزون
                    if ($explicitlyDeleteAll) {
                        $deletedCount = $product->inventory()->count();
                        $product->inventory()->delete();
                        \Log::warning('Explicitly requested to delete all inventory', [
                            'product_id' => $product->id,
                            'deleted_count' => $deletedCount
                        ]);
                    } else {
                        // هنا المفترض أن الـ normalizeVariantsFromRequest أرجعت مصفوفة فارغة رغم محاولاتها المتعددة
                        // هذا يعني أن المنتج فعلاً لا يجب أن يكون له variants
                        \Log::warning('No variants were created despite all attempts. Keeping existing inventory if any', [
                            'product_id' => $product->id,
                            'existing_count' => $product->inventory()->count()
                        ]);
                        
                        // في حالة عدم وجود أي inventory، نضيف واحد افتراضي
                        if ($product->inventory()->count() == 0) {
                            \App\Models\ProductSizeColorInventory::create([
                                'product_id' => $product->id,
                                'size_id' => null,
                                'color_id' => null,
                                'stock' => $product->stock ?? 10,
                                'price' => $product->base_price ?? 0,
                                'is_available' => true,
                            ]);
                            \Log::info('Created default generic inventory item', [
                                'product_id' => $product->id,
                                'stock' => $product->stock ?? 10,
                                'price' => $product->base_price ?? 0
                            ]);
                        }
                    }
                }
            }

            // ربط الألوان والمقاسات كـ pivot للفلترة والتقارير
            if (array_key_exists('selected_colors', $validated)) {
                $product->colors()->sync($validated['selected_colors'] ?? []);
            }
            
            if (array_key_exists('selected_sizes', $validated)) {
                $product->sizes()->sync($validated['selected_sizes'] ?? []);
            }

            if ($request->has('remove_images') && is_array($request->remove_images)) {
                foreach ($request->remove_images as $imageId) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        $this->deleteFile($image->image_path);
                        $image->delete();
                    }
                }
            }

            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $index => $image) {
                    $path = $this->uploadFile($image, 'products');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => $request->input('is_primary_new.' . $index, false) ? true : false
                    ]);
                }
            }

            if ($request->has('is_primary') && !empty($request->is_primary)) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $request->is_primary)->update(['is_primary' => true]);
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'تم تحديث المنتج بنجاح');
        } catch (\Exception $e) {
            Log::error('Product update error: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            DB::rollBack();
            return back()->withInput()
                ->with('error', 'فشل تحديث المنتج. ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            $product->colors()->detach();
            // حذف جميع مقاسات المنتج من جدول product_size_color_inventory
            \App\Models\ProductSizeColorInventory::where('product_id', $product->id)->delete();
            $product->orderItems()->delete();
            $product->discounts()->detach();
            $product->categories()->detach();

            foreach ($product->images as $image) {
                $this->deleteFile($image->image_path);
                $image->delete();
            }

            $product->delete();

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'تم حذف المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل حذف المنتج. ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'colors', 'sizes', 'categories', 'inventory' => fn($q) => $q->where('is_available', true)->with(['color','size'])]);

        // ممكن تشتق الألوان/المقاسات من الـ inventory بدلاً من الـ pivots
        $colors = $product->inventory->pluck('color')->filter()->unique('id')->values();
        $sizes  = $product->inventory->pluck('size')->filter()->unique('id')->values();

        return view('admin.products.show', compact('product', 'colors', 'sizes'));
    }

    /**
     * عرض صفحة مخزون المنتجات
     *
     * @return \Illuminate\Http\Response
     */
    public function inventory()
    {
        $products = Product::select('id', 'name', 'slug', 'stock', 'consumed_stock', 'is_available')
            ->orderBy('stock', 'asc')
            ->get()
            ->map(function ($product) {
                $availableStock = $product->available_stock;
                $consumedStock = $product->consumed_stock;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'total_stock' => $product->stock,
                    'consumed_stock' => $consumedStock,
                    'available_stock' => $availableStock,
                    'is_available' => $product->is_available,
                    'status' => $this->getStockStatus($availableStock, $product->stock),
                    'consumption_percentage' => $product->stock > 0 ? round(($consumedStock / $product->stock) * 100, 1) : 0
                ];
            });

        return view('admin.products.inventory', compact('products'));
    }

    /**
     * تحديد حالة المخزون
     */
    private function getStockStatus($availableStock, $totalStock)
    {
        if ($availableStock <= 0) {
            return 'out_of_stock';
        } elseif ($availableStock < 20) {
            return 'low';
        } elseif ($availableStock < ($totalStock * 0.3)) { // أقل من 30% من المخزون
            return 'medium';
        } else {
            return 'normal';
        }
    }

    protected function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    protected function getValidationRules(): array
    {
        return [
            'enable_custom_color' => 'boolean',
            'enable_custom_size' => 'boolean',
            'enable_color_selection' => 'boolean',
            'enable_size_selection' => 'boolean',
        ];
    }

    protected function prepareForValidation($data)
    {
        $checkboxFields = [
            'enable_custom_color',
            'enable_custom_size',
            'enable_color_selection',
            'enable_size_selection'
        ];

        foreach ($checkboxFields as $field) {
            $data[$field] = isset($data[$field]) && $data[$field] === 'on';
        }

        return $data;
    }

    protected function generateSlugFromName($name, $excludeId = null)
    {
        $slug = Str::slug($name, '-');

        if (empty($slug)) {
            $slug = preg_replace('/\s+/', '-', $name);
            $slug = preg_replace('/[^\p{L}\p{N}\-]/u', '', $slug);
            $slug = mb_strtolower($slug, 'UTF-8');
        }

        return $this->generateUniqueSlug($slug, 1, $excludeId);
    }

    protected function generateUniqueSlug($slug, $counter = 1, $excludeId = null)
    {
        $originalSlug = $slug;
        $query = Product::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * تحويل البيانات من Request إلى مصفوفة منظمة للـ variants
     * تمت إعادة كتابة الدالة لتعالج المشكلات الموجودة في التعامل مع الـ variants
     */
    private function normalizeVariantsFromRequest(\Illuminate\Http\Request $request, int $productId): array
    {
        // [VARIANTS_DEBUG][NORMALIZE] Incoming Request Data
        Log::info('[VARIANTS_DEBUG][NORMALIZE] Incoming Request Data:', $request->all());
        
        // ===== DEBUG LEVEL 2: normalizeVariantsFromRequest Input =====
        Log::info('🔍 [DEBUG LEVEL 2] normalizeVariantsFromRequest input', [
            'product_id' => $productId,
            'request_all' => $request->all(),
            'has_variants' => $request->filled('variants'),
            'has_inventory' => $request->filled('inventory'),
            'has_selected_sizes' => $request->has('selected_sizes'),
            'has_selected_colors' => $request->has('selected_colors'),
            'selected_sizes' => $request->get('selected_sizes'),
            'selected_colors' => $request->get('selected_colors'),
            'stock_data' => $request->get('stock'),
            'price_data' => $request->get('price'),
            'all_inputs' => array_keys($request->all())
        ]);
        
        $out = [];
        $product = Product::findOrFail($productId);
        $basePrice = $product->base_price ?? 0;
        $defaultStock = $product->stock ?? 10;

        \Log::info('🔍 [NORMALIZE_VARIANTS] Starting normalization', [
            'product_id' => $productId,
            'has_variants' => $request->filled('variants'),
            'has_inventory' => $request->filled('inventory'),
            'has_selected_sizes' => $request->has('selected_sizes'),
            'has_selected_colors' => $request->has('selected_colors'),
            'base_price' => $basePrice,
            'default_stock' => $defaultStock,
            'all_request_keys' => array_keys($request->all()),
            'selected_sizes' => $request->get('selected_sizes'),
            'selected_colors' => $request->get('selected_colors'),
            'stock_data' => $request->get('stock'),
            'price_data' => $request->get('price')
        ]);

        if ($request->filled('variants') && is_array($request->variants)) {
            // الحالة 1: البيانات مجهزة مسبقاً على شكل variants
            foreach ($request->variants as $row) {
                $out[] = [
                    'product_id'   => $productId,
                    'size_id'      => $row['size_id'] ?? null,
                    'color_id'     => $row['color_id'] ?? null,
                    'stock'        => (int)($row['stock'] ?? $defaultStock),
                    'price'        => $row['price'] ?? $basePrice,
                    'is_available' => isset($row['is_available']) ? (bool)$row['is_available'] : true,
                ];
            }
        } elseif ($request->filled('inventory') && is_array($request->inventory)) {
            // الحالة 2: البيانات مجهزة على شكل inventory[size_id][color_id]
            foreach ($request->inventory as $sizeId => $colorsMap) {
                foreach ((array)$colorsMap as $colorId => $data) {
                    $out[] = [
                        'product_id'   => $productId,
                        'size_id'      => $sizeId !== 'null' ? (int)$sizeId : null,
                        'color_id'     => $colorId !== 'null' ? (int)$colorId : null,
                        'stock'        => (int)($data['stock'] ?? $defaultStock),
                        'price'        => $data['price'] ?? $basePrice,
                        'is_available' => isset($data['is_available']) ? (bool)$data['is_available'] : true,
                    ];
                }
            }
        } else {
            // الحالة 3: البيانات على شكل selected_sizes و selected_colors
            $hasSizes = $request->has('selected_sizes') && is_array($request->selected_sizes) && !empty($request->selected_sizes);
            $hasColors = $request->has('selected_colors') && is_array($request->selected_colors) && !empty($request->selected_colors);
            
            $stockData = is_array($request->input('stock')) ? $request->input('stock', []) : [];
            $priceData = is_array($request->input('price')) ? $request->input('price', []) : [];
            
            \Log::info('Processing selected_sizes and selected_colors', [
                'has_sizes' => $hasSizes,
                'has_colors' => $hasColors,
                'selected_sizes' => $request->get('selected_sizes'),
                'selected_colors' => $request->get('selected_colors'),
                'stock_data_keys' => array_keys($stockData),
                'stock_data' => $stockData,
                'price_data' => $priceData,
                'all_request_keys' => array_keys($request->all())
            ]);
            
            // إصلاح: نتعامل مع البيانات بالشكل الصحيح من النماذج
            if ($hasSizes && $hasColors) {
                // الحالة 3.1: توجد مقاسات وألوان معاً
                foreach ($request->selected_sizes as $sizeId) {
                    foreach ($request->selected_colors as $colorId) {
                        // البحث عن المخزون والسعر في البيانات المرسلة
                        $stock = $defaultStock;
                        $price = $basePrice;
                        
                        // البحث في stock[size_id][color_id]
                        if (isset($stockData[$sizeId]) && isset($stockData[$sizeId][$colorId])) {
                            $stock = (int)$stockData[$sizeId][$colorId];
                        }
                        
                        // البحث في price[size_id][color_id]
                        if (isset($priceData[$sizeId]) && isset($priceData[$sizeId][$colorId])) {
                            $price = $priceData[$sizeId][$colorId];
                        }
                        
                        // تأكد من أن color_id ليس null أو فارغ
                        if (!empty($colorId) && $colorId !== 'null' && $colorId !== null) {
                            $out[] = [
                                'product_id'   => $productId,
                                'size_id'      => (int)$sizeId,
                                'color_id'     => (int)$colorId,
                                'stock'        => $stock,
                                'price'        => $price,
                                'is_available' => true,
                            ];
                            \Log::info('Added size+color variant', [
                                'size_id' => $sizeId, 
                                'color_id' => $colorId, 
                                'stock' => $stock,
                                'price' => $price
                            ]);
                        } else {
                            \Log::warning('Skipping variant with null/empty color_id', [
                                'size_id' => $sizeId,
                                'color_id' => $colorId
                            ]);
                        }
                    }
                }
            } elseif ($hasSizes) {
                // الحالة 3.2: مقاسات فقط
                foreach ($request->selected_sizes as $sizeId) {
                    // البحث عن المخزون والسعر لهذا المقاس
                    $stock = $defaultStock;
                    $price = $basePrice;
                    
                    // البحث في stock[size_id][color_id] لأي لون
                    if (isset($stockData[$sizeId])) {
                        foreach ($stockData[$sizeId] as $anyColorId => $stockValue) {
                            $stock = (int)$stockValue;
                            $price = isset($priceData[$sizeId][$anyColorId]) ? $priceData[$sizeId][$anyColorId] : $basePrice;
                            break;
                        }
                    }
                    
                    // لا نضيف سجلات بدون color_id
                    \Log::warning('Skipping size-only variant (no color_id)', [
                        'size_id' => $sizeId
                    ]);
                    \Log::info('Added size-only variant', [
                        'size_id' => $sizeId, 
                        'stock' => $stock,
                        'price' => $price
                    ]);
                }
            } elseif ($hasColors) {
                // الحالة 3.3: ألوان فقط
                foreach ($request->selected_colors as $colorId) {
                    // البحث عن المخزون والسعر لهذا اللون
                    $stock = $defaultStock;
                    $price = $basePrice;
                    
                    // البحث في stock[size_id][color_id] لأي مقاس
                    foreach ($stockData as $anySizeId => $colors) {
                        if (isset($colors[$colorId])) {
                            $stock = (int)$colors[$colorId];
                            $price = isset($priceData[$anySizeId][$colorId]) ? $priceData[$anySizeId][$colorId] : $basePrice;
                            break;
                        }
                    }
                    
                    // لا نضيف سجلات بدون size_id
                    \Log::warning('Skipping color-only variant (no size_id)', [
                        'color_id' => $colorId
                    ]);
                    \Log::info('Added color-only variant', [
                        'color_id' => $colorId, 
                        'stock' => $stock,
                        'price' => $price
                    ]);
                }
            }
                   
            // إذا كانت rows فارغة بعد المعالجة، ربما البيانات غير منظمة في الشكل المتوقع
            if (empty($out)) {
                \Log::warning('No variants created from selected_sizes/colors. Looking for stock[] fields directly');
                // نفحص جميع الحقول بحثاً عن stock[size_id][color_id]
                $allInputs = $request->all();
                foreach ($allInputs as $key => $value) {
                    if (strpos($key, 'stock[') === 0) {
                        // استخراج size_id و color_id من اسم الحقل
                        preg_match('/stock\[([^\]]+)\]\[([^\]]+)\]/', $key, $matches);
                        if (count($matches) === 3) {
                            $sizeId = (int)$matches[1];
                            $colorId = (int)$matches[2];
                            $priceKey = "price[{$matches[1]}][{$matches[2]}]";
                            $price = isset($allInputs[$priceKey]) ? $allInputs[$priceKey] : $basePrice;
                            
                            // تأكد من أن color_id ليس null أو فارغ
                            if (!empty($colorId) && $colorId !== 'null' && $colorId !== null) {
                                $out[] = [
                                    'product_id'   => $productId,
                                    'size_id'      => $sizeId,
                                    'color_id'     => $colorId,
                                    'stock'        => (int)($value ?: $defaultStock),
                                    'price'        => $price,
                                    'is_available' => true,
                                ];
                                \Log::info('Added variant from direct field matching', [
                                    'field' => $key, 
                                    'size_id' => $sizeId, 
                                    'color_id' => $colorId,
                                    'stock' => $value,
                                    'price' => $price
                                ]);
                            } else {
                                \Log::warning('Skipping variant with null/empty color_id from direct field matching', [
                                    'field' => $key,
                                    'size_id' => $sizeId,
                                    'color_id' => $colorId
                                ]);
                            }
                        }
                    }
                }
            }
        }
        
        // Fallback: إذا لم نجد أي بيانات بعد كل المحاولات السابقة
        if (empty($out)) {
            \Log::warning('All attempts failed to create variants. Creating fallbacks based on existing data.');
            
            // 1. نجرب نسترجع الموجود حالياً ونحدث أرقام المخزون فقط
            $existingInventory = \App\Models\ProductSizeColorInventory::where('product_id', $productId)->get();
            
            if ($existingInventory->isNotEmpty()) {
                foreach ($existingInventory as $item) {
                    // تأكد من أن color_id صحيح
                    if (!empty($item->color_id) && $item->color_id !== null && $item->color_id !== 'null') {
                        $out[] = [
                            'product_id'   => $productId,
                            'size_id'      => $item->size_id,
                            'color_id'     => $item->color_id,
                            'stock'        => $defaultStock, // نستخدم المخزون الافتراضي لأن مخزون القديم ما يصحش نستخدمه
                            'price'        => $item->price ?? $basePrice,
                            'is_available' => true,
                        ];
                    } else {
                        \Log::warning('Skipping existing inventory item with null color_id', [
                            'item_id' => $item->id,
                            'size_id' => $item->size_id,
                            'color_id' => $item->color_id
                        ]);
                    }
                }
                \Log::info('Created variants based on existing inventory', [
                    'count' => count($out),
                    'product_id' => $productId,
                    'default_stock' => $defaultStock,
                    'base_price' => $basePrice
                ]);
            }
            
            // 2. إذا لم يكن هناك inventory سابق، نستخدم المقاسات والألوان المحددة بشكل منفصل
            if (empty($out)) {
                $hasSizes = $request->has('selected_sizes') && is_array($request->selected_sizes) && !empty($request->selected_sizes);
                $hasColors = $request->has('selected_colors') && is_array($request->selected_colors) && !empty($request->selected_colors);
                
                if ($hasSizes || $hasColors) {
                    // هناك مقاسات أو ألوان، لكن لم يتم تحويلها إلى variants
                    if ($hasSizes && $hasColors) {
                        // اصنع variant واحد لكل مقاس×لون
                        foreach ($request->selected_sizes as $sizeId) {
                            foreach ($request->selected_colors as $colorId) {
                                // تأكد من أن color_id صحيح
                                if (!empty($colorId) && $colorId !== 'null' && $colorId !== null) {
                                    $out[] = [
                                        'product_id'   => $productId,
                                        'size_id'      => (int)$sizeId,
                                        'color_id'     => (int)$colorId,
                                        'stock'        => $defaultStock,
                                        'price'        => $basePrice,
                                        'is_available' => true,
                                    ];
                                }
                            }
                        }
                        \Log::info('Created default variants for all size×color combinations', ['count' => count($out)]);
                    } elseif ($hasSizes) {
                        // لا نضيف سجلات بدون color_id
                        \Log::warning('Skipping size-only variants in fallback (no color_id)');
                        \Log::info('Created default variants for all sizes', ['count' => count($out)]);
                    } elseif ($hasColors) {
                        // لا نضيف سجلات بدون size_id
                        \Log::warning('Skipping color-only variants in fallback (no size_id)');
                        \Log::info('Created default variants for all colors', ['count' => count($out)]);
                    }
                } else {
                    // لا نضيف سجلات بدون size_id و color_id
                    \Log::warning('Skipping generic variant (no size_id and no color_id)');
                    \Log::info('Created single generic variant as last resort', ['base_price' => $basePrice, 'stock' => $defaultStock]);
                }
            }
        }

        // نظّف الداتا من ازدواجيات محتملة
        $keyed = [];
        foreach ($out as $row) {
            $k = ($row['size_id'] ?? 'null').'-'.($row['color_id'] ?? 'null');
            if (!isset($keyed[$k])) {
                $keyed[$k] = $row;
            } else {
                // لو مكرر؛ نجمعها ببساطة (آخر قيمة تكسب)
                $keyed[$k]['stock'] = max($keyed[$k]['stock'], $row['stock']);
                $keyed[$k]['price'] = $row['price'] ?? $keyed[$k]['price'];
                $keyed[$k]['is_available'] = $row['is_available'] ?? $keyed[$k]['is_available'];
                
                \Log::info('Merged duplicate variant', [
                    'key' => $k,
                    'original_stock' => $keyed[$k]['stock'],
                    'new_stock' => $row['stock'],
                    'final_stock' => $keyed[$k]['stock']
                ]);
            }
        }
        
        $result = array_values($keyed);
        
        // [VARIANTS_DEBUG][NORMALIZE] Outgoing Variants
        Log::info('[VARIANTS_DEBUG][NORMALIZE] Outgoing Variants:', $result);
        
        // ===== DEBUG LEVEL 2: normalizeVariantsFromRequest Output =====
        Log::info('🔍 [DEBUG LEVEL 2] normalizeVariantsFromRequest output', [
            'product_id' => $productId,
            'variants_count' => count($result),
            'variants' => $result
        ]);
        
        \Log::info('Final variants after normalization', [
            'count' => count($result),
            'product_id' => $productId,
            'base_price' => $basePrice,
            'default_stock' => $defaultStock
        ]);
        
        return $result;
    }

    /**
     * التأكد من وجود المقاسات والألوان الافتراضية
     */
    private function ensureDefaultSizesAndColors(): void
    {
        // إنشاء مقاسات افتراضية إذا لم تكن موجودة
        if (\App\Models\ProductSize::count() === 0) {
            $defaultSizes = [
                ['name' => 'XS', 'description' => 'مقاس صغير جداً'],
                ['name' => 'S', 'description' => 'مقاس صغير'],
                ['name' => 'M', 'description' => 'مقاس متوسط'],
                ['name' => 'L', 'description' => 'مقاس كبير'],
                ['name' => 'XL', 'description' => 'مقاس كبير جداً'],
                ['name' => 'XXL', 'description' => 'مقاس كبير جداً جداً'],
            ];
            
            foreach ($defaultSizes as $size) {
                \App\Models\ProductSize::create($size);
            }
            \Log::info('Created default sizes', ['count' => count($defaultSizes)]);
        }
        
        // إنشاء ألوان افتراضية إذا لم تكن موجودة
        if (\App\Models\ProductColor::count() === 0) {
            $defaultColors = [
                ['name' => 'أحمر', 'code' => '#FF0000', 'description' => 'لون أحمر'],
                ['name' => 'أزرق', 'code' => '#0000FF', 'description' => 'لون أزرق'],
                ['name' => 'أخضر', 'code' => '#00FF00', 'description' => 'لون أخضر'],
                ['name' => 'أصفر', 'code' => '#FFFF00', 'description' => 'لون أصفر'],
                ['name' => 'أسود', 'code' => '#000000', 'description' => 'لون أسود'],
                ['name' => 'أبيض', 'code' => '#FFFFFF', 'description' => 'لون أبيض'],
            ];
            
            foreach ($defaultColors as $color) {
                \App\Models\ProductColor::create($color);
            }
            \Log::info('Created default colors', ['count' => count($defaultColors)]);
        }
    }

    /**
     * حذف الـ variants التي تم إزالتها من الفورم
     * تم تحديث هذه الدالة للتعامل مع الحالات الخاصة والإصدارات المختلفة للبيانات
     */
    private function deleteMissingVariants(\App\Models\Product $product, array $incoming): void
    {
        // احصل على البيانات الحالية من قاعدة البيانات
        $existingRecords = $product->inventory()->get();
        
        // لو مفيش inventory حالي، ما فيش حاجة تتمسح
        if ($existingRecords->isEmpty()) {
            \Log::info('No existing inventory to delete for product', ['product_id' => $product->id]);
            return;
        }
        
        // تحويل البيانات الواردة إلى مجموعة من المفاتيح الفريدة
        $incomingKeys = collect($incoming)
            ->map(function($r) {
                $sizeId = $r['size_id'] ?? 'null';
                $colorId = $r['color_id'] ?? 'null';
                return "{$sizeId}-{$colorId}";
            })
            ->unique()
            ->values();

        // تحويل السجلات الموجودة إلى مجموعة مفاتيح مماثلة للمقارنة
        $existingMapped = $existingRecords->keyBy(function($r) {
            $sizeId = $r->size_id ?? 'null';
            $colorId = $r->color_id ?? 'null';
            return "{$sizeId}-{$colorId}";
        });
        
        // تحديد السجلات التي يجب حذفها (موجودة حالياً لكن غير موجودة في البيانات الجديدة)
        $toDeleteKeys = $existingMapped->keys()->diff($incomingKeys);
        
        \Log::info('Variant deletion analysis', [
            'product_id' => $product->id,
            'existing_count' => $existingMapped->count(),
            'incoming_count' => $incomingKeys->count(),
            'to_delete_count' => $toDeleteKeys->count(),
            'sample_existing' => $existingMapped->keys()->take(3)->all(),
            'sample_incoming' => $incomingKeys->take(3)->all(),
            'sample_to_delete' => $toDeleteKeys->take(3)->all(),
        ]);

        if ($toDeleteKeys->isNotEmpty()) {
            // احصل على معرفات السجلات التي سيتم حذفها
            $ids = $existingMapped->only($toDeleteKeys->all())->pluck('id')->all();
            
            if (!empty($ids)) {
                $deleteCount = $product->inventory()->whereIn('id', $ids)->delete();
                \Log::info('Deleted variants', [
                    'product_id' => $product->id, 
                    'deleted_count' => $deleteCount, 
                    'ids' => array_slice($ids, 0, 5)
                ]);
            }
        } else {
            \Log::info('No variants to delete', ['product_id' => $product->id]);
        }
    }
}
