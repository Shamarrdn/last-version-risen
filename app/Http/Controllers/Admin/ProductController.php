<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $this->ensureDefaultSizesAndColors();

        $availableSizes = \App\Models\ProductSize::all();
        $availableColors = \App\Models\ProductColor::all();

        return view('admin.products.create', compact('categories', 'availableSizes', 'availableColors'));
    }

    public function store(Request $request)
    {
        \Log::info('Product store method called', [
            'data' => $request->all(),
            'files' => $request->hasFile('images') ? 'Has images' : 'No images'
        ]);

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
            'stock' => 'nullable|integer|min:0',

            'selected_sizes' => 'nullable|array',
            'selected_sizes.*' => 'exists:size_options,id',
            'selected_colors' => 'nullable|array',
            'selected_colors.*' => 'exists:color_options,id',

            'variants' => 'nullable|array',
            'variants.*.size_id' => 'nullable|exists:size_options,id',
            'variants.*.color_id' => 'nullable|exists:color_options,id',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.is_available' => 'nullable|boolean',

            'inventory' => 'nullable|array',

            'inventories' => 'nullable|array',
            'inventories.*.*.color_id' => 'nullable|exists:color_options,id',
            'inventories.*.*.size_id' => 'nullable|exists:size_options,id',
            'inventories.*.*.stock' => 'nullable|integer|min:0',
            'inventories.*.*.price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            if (empty($request->slug)) {
                $validated['slug'] = $this->generateSlugFromName($request->name);
            } else {
                $validated['slug'] = $this->generateUniqueSlug($request->slug);
            }

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

            $validated['enable_custom_color'] = $request->has('enable_custom_color');
            $validated['enable_custom_size'] = $request->has('enable_custom_size');
            $validated['enable_color_selection'] = $request->has('enable_color_selection');
            $validated['enable_size_selection'] = $request->has('enable_size_selection');
            $validated['is_available'] = $request->has('is_available');
            $validated['stock'] = intval($request->input('stock', 0));

            $product = Product::create($validated);

            if ($request->has('categories') && is_array($request->categories)) {
                $product->categories()->attach($request->categories);
            }

            if ($request->has('inventories') && is_array($request->inventories)) {
                foreach ($request->inventories as $sizeId => $colors) {
                    foreach ($colors as $colorId => $data) {
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
                    }
                }
            } else {
                $rows = $this->normalizeVariantsFromRequest($request, $product->id);

                if (!empty($rows)) {
                    try {
                        $validRows = array_filter($rows, function($row) {
                            return !empty($row['color_id']) && $row['color_id'] !== null && $row['color_id'] !== 'null';
                        });

                        if (!empty($validRows)) {
                            \App\Models\ProductSizeColorInventory::upsert(
                                $validRows,
                                ['product_id', 'size_id', 'color_id'],
                                ['stock', 'price', 'is_available']
                            );
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('فشل في حفظ بيانات المقاسات والألوان: ' . $e->getMessage());
                    }
                }
            }

            if ($request->has('selected_colors') && is_array($request->selected_colors)) {
                $product->colors()->sync($request->selected_colors);
            }

            if ($request->has('selected_sizes') && is_array($request->selected_sizes)) {
                $product->sizes()->sync($request->selected_sizes);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->uploadFile($image, 'products');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => $request->input('is_primary.' . $index, false) ? true : false
                    ]);
                }
            }

            // تحديث المخزون العام بناءً على المخزون التفصيلي
            $totalStock = $product->inventory->sum('stock');
            $product->update(['stock' => $totalStock]);
            
            DB::commit();
            \Log::info('Product created successfully', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'total_stock' => $totalStock
            ]);
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

        $this->ensureDefaultSizesAndColors();

        $availableSizes = \App\Models\ProductSize::all();
        $availableColors = \App\Models\ProductColor::all();

        $selectedCategories = $product->categories->pluck('id')->toArray();

        $product->load([
            'images',
            'categories',
            'inventory' => function($query) {
                $query->with(['size', 'color']);
            }
        ]);

        $selectedSizes = [];
        $selectedColors = [];
        $stockData = [];
        $priceData = [];

        foreach ($product->inventory as $inventory) {
            if ($inventory->size) {
                $sizeId = $inventory->size_id;
                $colorId = $inventory->color_id;

                if (!in_array($sizeId, $selectedSizes)) {
                    $selectedSizes[] = $sizeId;
                }

                if ($colorId && $colorId !== null && $colorId !== 'null' && !in_array($colorId, $selectedColors)) {
                    $selectedColors[] = $colorId;
                }

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
        \Log::info('Product update method called', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'request_data' => $request->all()
        ]);
        
        try {
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
                // تم إزالة stock validation في التحديث أيضاً

                'selected_sizes' => 'nullable|array',
                'selected_sizes.*' => 'exists:size_options,id',
                'selected_colors' => 'nullable|array',
                'selected_colors.*' => 'exists:color_options,id',

                'variants' => 'nullable|array',
                'variants.*.size_id' => 'nullable|exists:size_options,id',
                'variants.*.color_id' => 'nullable|exists:color_options,id',
                'variants.*.stock' => 'nullable|integer|min:0',
                'variants.*.price' => 'nullable|numeric|min:0',
                'variants.*.is_available' => 'nullable|boolean',

                'inventory' => 'nullable|array',

                'inventories' => 'nullable|array',
                'inventories.*.*.color_id' => 'nullable|exists:color_options,id',
                'inventories.*.*.size_id' => 'nullable|exists:size_options,id',
                'inventories.*.*.stock' => 'nullable|integer|min:0',
                'inventories.*.*.price' => 'nullable|numeric|min:0',
                
                'delete_inventory_ids' => 'nullable|array',
                'delete_inventory_ids.*' => 'nullable|integer',
            ]);

            DB::beginTransaction();

            if (empty($request->slug)) {
                $validated['slug'] = $this->generateSlugFromName($request->name, $product->id);
            } else if ($validated['slug'] !== $product->slug) {
                $validated['slug'] = $this->generateUniqueSlug($validated['slug'], 1, $product->id);
            }

            $details = [];
            if ($request->has('detail_keys') && $request->has('detail_values') &&
                is_array($request->detail_keys) && is_array($request->detail_values)) {
                foreach ($request->detail_keys as $index => $key) {
                    if (!empty($key) && isset($request->detail_values[$index]) && !empty($request->detail_values[$index])) {
                        $details[$key] = $request->detail_values[$index];
                    }
                }
            }

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
                // تم إزالة حقل stock - الاعتماد على المخزون التفصيلي فقط
            ]);

            $product->categories()->sync(is_array($request->categories) ? $request->categories : []);

            if ($request->has('inventories') && is_array($request->inventories)) {
                \Log::info('Processing inventories for update', ['inventories' => $request->inventories]);
                
                foreach ($request->inventories as $sizeId => $sizeData) {
                    if (!is_array($sizeData)) {
                        continue;
                    }
                    
                    foreach ($sizeData as $colorId => $inventoryData) {
                        if (!is_array($inventoryData)) {
                            continue;
                        }

                        $actualSizeId = $inventoryData['size_id'] ?? $sizeId;
                        $actualColorId = $inventoryData['color_id'] ?? $colorId;
                        $stock = $inventoryData['stock'] ?? 0;
                        $price = $inventoryData['price'] ?? 0;

                        if (empty($actualSizeId) || empty($actualColorId)) {
                            continue;
                        }

                        try {
                            \App\Models\ProductSizeColorInventory::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'size_id'    => $actualSizeId,
                                    'color_id'   => $actualColorId,
                                ],
                                [
                                    'stock'        => $stock,
                                    'price'        => $price,
                                    'is_available' => 1,
                                ]
                            );
                            
                            \Log::info('Updated inventory', [
                                'product_id' => $product->id,
                                'size_id' => $actualSizeId,
                                'color_id' => $actualColorId,
                                'stock' => $stock,
                                'price' => $price
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to update inventory', [
                                'error' => $e->getMessage(),
                                'size_id' => $actualSizeId,
                                'color_id' => $actualColorId
                            ]);
                            throw $e;
                        }
                    }
                }
            } else {
                $rows = $this->normalizeVariantsFromRequest($request, $product->id);

                $explicitlyDeleteAll = $request->has('delete_all_inventory') && $request->delete_all_inventory;

                if (!empty($rows)) {
                    try {
                        $hasSizeData = $request->has('selected_sizes') && !empty($request->selected_sizes);
                        $hasColorData = $request->has('selected_colors') && !empty($request->selected_colors);

                        if (($hasSizeData || $hasColorData) && !$explicitlyDeleteAll) {
                            $this->deleteMissingVariants($product, $rows);
                        }

                        $validRows = array_filter($rows, function($row) {
                            return !empty($row['color_id']) && $row['color_id'] !== null && $row['color_id'] !== 'null';
                        });

                        if (!empty($validRows)) {
                            \App\Models\ProductSizeColorInventory::upsert(
                                $validRows,
                                ['product_id', 'size_id', 'color_id'],
                                ['stock', 'price', 'is_available']
                            );
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('فشل في تحديث بيانات المقاسات والألوان: ' . $e->getMessage());
                    }
                } else {
                    if ($explicitlyDeleteAll) {
                        $deletedCount = $product->inventory()->count();
                        $product->inventory()->delete();
                    } else {
                        if ($product->inventory()->count() == 0) {
                            \App\Models\ProductSizeColorInventory::create([
                                'product_id' => $product->id,
                                'size_id' => null,
                                'color_id' => null,
                                'stock' => $product->stock ?? 10,
                                'price' => $product->base_price ?? 0,
                                'is_available' => true,
                            ]);
                        }
                    }
                }
            }

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

            // Handle deletion of existing inventory rows
            if ($request->has('delete_inventory_ids') && is_array($request->delete_inventory_ids)) {
                $idsToDelete = array_filter($request->delete_inventory_ids, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                
                if (!empty($idsToDelete)) {
                    $deletedCount = \App\Models\ProductSizeColorInventory::where('product_id', $product->id)
                        ->whereIn('id', $idsToDelete)
                        ->delete();
                    
                    \Log::info("Deleted {$deletedCount} inventory rows for product {$product->id}");
                }
            }

            // تحديث المخزون العام بناءً على المخزون التفصيلي
            $totalStock = $product->inventory->sum('stock');
            $product->update(['stock' => $totalStock]);
            
            DB::commit();
            \Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'total_stock' => $totalStock
            ]);
            return redirect()->route('admin.products.index')
                ->with('success', 'تم تحديث المنتج بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Product update validation failed', [
                'product_id' => $product->id,
                'errors' => $e->errors()
            ]);
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product update failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', 'فشل تحديث المنتج. ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            $product->colors()->detach();
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
        $product->load([
            'category',
            'images',
            'categories',
            'inventory' => fn($q) => $q->with(['color','size'])
        ]);

        return view('admin.products.show', compact('product'));
    }

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

    private function getStockStatus($availableStock, $totalStock)
    {
        if ($availableStock <= 0) {
            return 'out_of_stock';
        } elseif ($availableStock < 20) {
            return 'low';
        } elseif ($availableStock < ($totalStock * 0.3)) {
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

    private function normalizeVariantsFromRequest(\Illuminate\Http\Request $request, int $productId): array
    {
        $out = [];
        $product = Product::findOrFail($productId);
        $basePrice = $product->base_price ?? 0;
        $defaultStock = $product->stock ?? 10;

        if ($request->filled('variants') && is_array($request->variants)) {
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
            $hasSizes = $request->has('selected_sizes') && is_array($request->selected_sizes) && !empty($request->selected_sizes);
            $hasColors = $request->has('selected_colors') && is_array($request->selected_colors) && !empty($request->selected_colors);

            $stockData = is_array($request->input('stock')) ? $request->input('stock', []) : [];
            $priceData = is_array($request->input('price')) ? $request->input('price', []) : [];

            if ($hasSizes && $hasColors) {
                foreach ($request->selected_sizes as $sizeId) {
                    foreach ($request->selected_colors as $colorId) {
                        $stock = $defaultStock;
                        $price = $basePrice;

                        if (isset($stockData[$sizeId]) && isset($stockData[$sizeId][$colorId])) {
                            $stock = (int)$stockData[$sizeId][$colorId];
                        }

                        if (isset($priceData[$sizeId]) && isset($priceData[$sizeId][$colorId])) {
                            $price = $priceData[$sizeId][$colorId];
                        }

                        if (!empty($colorId) && $colorId !== 'null' && $colorId !== null) {
                            $out[] = [
                                'product_id'   => $productId,
                                'size_id'      => (int)$sizeId,
                                'color_id'     => (int)$colorId,
                                'stock'        => $stock,
                                'price'        => $price,
                                'is_available' => true,
                            ];
                        }
                    }
                }
            } elseif ($hasSizes) {
                foreach ($request->selected_sizes as $sizeId) {
                    $stock = $defaultStock;
                    $price = $basePrice;

                    if (isset($stockData[$sizeId])) {
                        foreach ($stockData[$sizeId] as $anyColorId => $stockValue) {
                            $stock = (int)$stockValue;
                            $price = isset($priceData[$sizeId][$anyColorId]) ? $priceData[$sizeId][$anyColorId] : $basePrice;
                            break;
                        }
                    }
                }
            } elseif ($hasColors) {
                foreach ($request->selected_colors as $colorId) {
                    $stock = $defaultStock;
                    $price = $basePrice;

                    foreach ($stockData as $anySizeId => $colors) {
                        if (isset($colors[$colorId])) {
                            $stock = (int)$colors[$colorId];
                            $price = isset($priceData[$anySizeId][$colorId]) ? $priceData[$anySizeId][$colorId] : $basePrice;
                            break;
                        }
                    }
                }
            }

            if (empty($out)) {
                $allInputs = $request->all();
                foreach ($allInputs as $key => $value) {
                    if (strpos($key, 'stock[') === 0) {
                        preg_match('/stock\[([^\]]+)\]\[([^\]]+)\]/', $key, $matches);
                        if (count($matches) === 3) {
                            $sizeId = (int)$matches[1];
                            $colorId = (int)$matches[2];
                            $priceKey = "price[{$matches[1]}][{$matches[2]}]";
                            $price = isset($allInputs[$priceKey]) ? $allInputs[$priceKey] : $basePrice;

                            if (!empty($colorId) && $colorId !== 'null' && $colorId !== null) {
                                $out[] = [
                                    'product_id'   => $productId,
                                    'size_id'      => $sizeId,
                                    'color_id'     => $colorId,
                                    'stock'        => (int)($value ?: $defaultStock),
                                    'price'        => $price,
                                    'is_available' => true,
                                ];
                            }
                        }
                    }
                }
            }
        }

        if (empty($out)) {
            $existingInventory = \App\Models\ProductSizeColorInventory::where('product_id', $productId)->get();

            if ($existingInventory->isNotEmpty()) {
                foreach ($existingInventory as $item) {
                    if (!empty($item->color_id) && $item->color_id !== null && $item->color_id !== 'null') {
                        $out[] = [
                            'product_id'   => $productId,
                            'size_id'      => $item->size_id,
                            'color_id'     => $item->color_id,
                            'stock'        => $defaultStock,
                            'price'        => $item->price ?? $basePrice,
                            'is_available' => true,
                        ];
                    }
                }
            }

            if (empty($out)) {
                $hasSizes = $request->has('selected_sizes') && is_array($request->selected_sizes) && !empty($request->selected_sizes);
                $hasColors = $request->has('selected_colors') && is_array($request->selected_colors) && !empty($request->selected_colors);

                if ($hasSizes || $hasColors) {
                    if ($hasSizes && $hasColors) {
                        foreach ($request->selected_sizes as $sizeId) {
                            foreach ($request->selected_colors as $colorId) {
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
                    }
                }
            }
        }

        $keyed = [];
        foreach ($out as $row) {
            $k = ($row['size_id'] ?? 'null').'-'.($row['color_id'] ?? 'null');
            if (!isset($keyed[$k])) {
                $keyed[$k] = $row;
            } else {
                $keyed[$k]['stock'] = max($keyed[$k]['stock'], $row['stock']);
                $keyed[$k]['price'] = $row['price'] ?? $keyed[$k]['price'];
                $keyed[$k]['is_available'] = $row['is_available'] ?? $keyed[$k]['is_available'];
            }
        }

        $result = array_values($keyed);

        return $result;
    }

    private function ensureDefaultSizesAndColors(): void
    {
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
        }

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
        }
    }

    private function deleteMissingVariants(\App\Models\Product $product, array $incoming): void
    {
        $existingRecords = $product->inventory()->get();

        if ($existingRecords->isEmpty()) {
            return;
        }

        $incomingKeys = collect($incoming)
            ->map(function($r) {
                $sizeId = $r['size_id'] ?? 'null';
                $colorId = $r['color_id'] ?? 'null';
                return "{$sizeId}-{$colorId}";
            })
            ->unique()
            ->values();

        $existingMapped = $existingRecords->keyBy(function($r) {
            $sizeId = $r->size_id ?? 'null';
            $colorId = $r->color_id ?? 'null';
            return "{$sizeId}-{$colorId}";
        });

        $toDeleteKeys = $existingMapped->keys()->diff($incomingKeys);

        if ($toDeleteKeys->isNotEmpty()) {
            $ids = $existingMapped->only($toDeleteKeys->all())->pluck('id')->all();

            if (!empty($ids)) {
                $deleteCount = $product->inventory()->whereIn('id', $ids)->delete();
            }
        }
    }

    public function deleteInventory(Product $product, $inventory)
    {
        try {
            // Find the inventory item by ID and ensure it belongs to this product
            $inventoryItem = \App\Models\ProductSizeColorInventory::where('product_id', $product->id)
                ->where('id', $inventory)
                ->first();

            if (!$inventoryItem) {
                \Log::warning("Inventory item not found: product_id={$product->id}, inventory_id={$inventory}");
                return response()->json([
                    'success' => false,
                    'message' => 'عنصر المخزون غير موجود'
                ], 404);
            }

            // Log the deletion
            \Log::info("Deleting inventory item: ID={$inventoryItem->id}, Product={$product->name}, Size={$inventoryItem->size_id}, Color={$inventoryItem->color_id}");
            
            $inventoryItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف عنصر المخزون بنجاح'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting inventory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف عنصر المخزون: ' . $e->getMessage()
            ], 500);
        }
    }
}
