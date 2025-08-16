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
                        ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
                        ->whereColumn('p.id', 'products.id')
                        ->limit(1);
                }, 'desc');
                break;
            case 'price_low':
                $query->orderBy(function ($q) {
                    return $q->select(DB::raw('MIN(COALESCE(ps.price, 0))'))
                        ->from('products as p')
                        ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
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
        
        // إنشاء مقاسات افتراضية إذا لم تكن موجودة
        $availableSizes = \App\Models\ProductSize::all();
        if ($availableSizes->isEmpty()) {
            // إنشاء مقاسات افتراضية
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
            $availableSizes = \App\Models\ProductSize::all();
        }
        
        // إنشاء ألوان افتراضية إذا لم تكن موجودة
        $availableColors = \App\Models\ProductColor::all();
        if ($availableColors->isEmpty()) {
            // إنشاء ألوان افتراضية
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
            $availableColors = \App\Models\ProductColor::all();
        }
        
        return view('admin.products.create', compact('categories', 'availableSizes', 'availableColors'));
    }

    public function store(Request $request)
    {
        $rules = [
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
        ];

        // التحقق من المقاسات والألوان المختارة
        if ($request->has('selected_sizes') && $request->has('selected_colors')) {
            $rules['selected_sizes'] = 'array';
            $rules['selected_sizes.*'] = 'exists:size_options,id';
            $rules['selected_colors'] = 'array';
            $rules['selected_colors.*'] = 'exists:color_options,id';
            
            // التحقق من المخزون والأسعار للمقاسات والألوان
            if ($request->has('stock') && is_array($request->stock)) {
                $rules['stock.*.*'] = 'nullable|integer|min:0';
            }
            if ($request->has('price') && is_array($request->price)) {
                $rules['price.*.*'] = 'nullable|numeric|min:0';
            }
        }

        // معالجة البيانات قبل التحقق من صحتها
        $data = $request->all();
        
        // التأكد من أن المخزون قيمة رقمية
        if (isset($data['stock'])) {
            if (is_array($data['stock'])) {
                $data['stock'] = $data['stock'][0] ?? 0;
            }
            $data['stock'] = max(0, floatval($data['stock']));
        }
        
        $validatedData = validator($data, $rules, [
            'stock.required' => 'حقل المخزون مطلوب',
            'stock.numeric' => 'حقل المخزون يجب أن يكون رقماً',
            'stock.min' => 'حقل المخزون يجب أن يكون 0 أو أكثر',
            'stock.max' => 'حقل المخزون يجب أن يكون أقل من 999999',
        ])->validate();

        try {
            DB::beginTransaction();

            if (empty($request->slug)) {
                $validatedData['slug'] = $this->generateSlugFromName($request->name);
            } else {
                $validatedData['slug'] = $this->generateUniqueSlug($request->slug);
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
            $validatedData['details'] = !empty($details) ? $details : null;

            $validatedData['enable_custom_color'] = $request->has('enable_custom_color');
            $validatedData['enable_custom_size'] = $request->has('enable_custom_size');
            $validatedData['enable_color_selection'] = $request->has('enable_color_selection');
            $validatedData['enable_size_selection'] = $request->has('enable_size_selection');
            $validatedData['is_available'] = $request->has('is_available');
            // التأكد من أن المخزون قيمة صحيحة
            $validatedData['stock'] = intval($validatedData['stock']);

            $product = Product::create($validatedData);

            if ($request->has('categories') && is_array($request->categories)) {
                $product->categories()->attach($request->categories);
            }

            // معالجة المقاسات والألوان المختارة
            if ($request->has('selected_sizes') && $request->has('selected_colors') && 
                is_array($request->selected_sizes) && is_array($request->selected_colors)) {
                // استخدام المخزون والأسعار للمقاسات والألوان فقط إذا كانت مصفوفات
                $stockData = is_array($request->input('stock')) ? $request->input('stock', []) : [];
                $priceData = is_array($request->input('price')) ? $request->input('price', []) : [];
                
                foreach ($request->selected_sizes as $sizeId) {
                    foreach ($request->selected_colors as $colorId) {
                        // التحقق من وجود مخزون لهذا المقاس واللون
                        $stock = isset($stockData[$sizeId][$colorId]) ? $stockData[$sizeId][$colorId] : null;
                        $price = isset($priceData[$sizeId][$colorId]) ? $priceData[$sizeId][$colorId] : null;
                        
                        if ($stock !== null && $stock > 0) {
                            // إضافة إلى جدول product_sizes
                            \DB::table('product_sizes')->insert([
                                'product_id' => $product->id,
                                'size_id' => $sizeId,
                                'color_id' => $colorId,
                                'stock' => $stock,
                                'price' => $price,
                                'is_available' => 1,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
                
                                    // ربط الألوان المختارة بالمنتج
                if (!empty($request->selected_colors)) {
                    // إضافة الألوان إلى جدول product_colors
                    foreach ($request->selected_colors as $colorId) {
                        $product->colors()->attach($colorId);
                    }
                }
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
        $product->load(['images', 'colors', 'sizes', 'categories']);
        $categories = Category::all();
        $availableSizes = \App\Models\ProductSize::all();
        if ($availableSizes->isEmpty()) {
            // Create default sizes
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
            $availableSizes = \App\Models\ProductSize::all();
        }

        $availableColors = \App\Models\ProductColor::all();
        if ($availableColors->isEmpty()) {
            // Create default colors
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
            $availableColors = \App\Models\ProductColor::all();
        }

        $selectedCategories = $product->categories->pluck('id')->toArray();
        return view('admin.products.edit', compact('product', 'categories', 'availableSizes', 'availableColors', 'selectedCategories'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            $rules = [
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
            ];

            // إضافة validation rules للمقاسات والألوان المختارة
            if ($request->has('selected_sizes')) {
                $rules['selected_sizes'] = 'array';
                $rules['selected_sizes.*'] = 'exists:size_options,id';
            }

            if ($request->has('selected_colors')) {
                $rules['selected_colors'] = 'array';
                $rules['selected_colors.*'] = 'exists:color_options,id';
            }

            if ($request->has('stock')) {
                $rules['stock'] = 'array';
                $rules['stock.*.*'] = 'nullable|numeric|min:0';
            }

            if ($request->has('price')) {
                $rules['price'] = 'array';
                $rules['price.*.*'] = 'nullable|numeric|min:0';
            }

            $validated = $request->validate($rules);

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
                'stock' => $request->input('stock', 0),
            ]);

            $product->categories()->sync(is_array($request->categories) ? $request->categories : []);

            // معالجة المقاسات والألوان المختارة
            if ($request->has('selected_sizes') && $request->has('selected_colors') && 
                is_array($request->selected_sizes) && is_array($request->selected_colors)) {
                // استخدام المخزون والأسعار للمقاسات والألوان فقط إذا كانت مصفوفات
                $stockData = is_array($request->input('stock')) ? $request->input('stock', []) : [];
                $priceData = is_array($request->input('price')) ? $request->input('price', []) : [];
                
                // حذف جميع المقاسات والألوان الحالية
                $product->colors()->detach();
                \DB::table('product_sizes')->where('product_id', $product->id)->delete();
                
                foreach ($request->selected_sizes as $sizeId) {
                    foreach ($request->selected_colors as $colorId) {
                        // التحقق من وجود مخزون لهذا المقاس واللون
                        $stock = isset($stockData[$sizeId][$colorId]) ? $stockData[$sizeId][$colorId] : null;
                        $price = isset($priceData[$sizeId][$colorId]) ? $priceData[$sizeId][$colorId] : null;
                        
                        if ($stock !== null && $stock > 0) {
                            // إضافة إلى جدول product_sizes
                            \DB::table('product_sizes')->insert([
                                'product_id' => $product->id,
                                'size_id' => $sizeId,
                                'color_id' => $colorId,
                                'stock' => $stock,
                                'price' => $price,
                                'is_available' => 1,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
                
                // ربط الألوان المختارة بالمنتج
                if (!empty($request->selected_colors)) {
                    foreach ($request->selected_colors as $colorId) {
                        $product->colors()->attach($colorId);
                    }
                }
            }

            // معالجة المقاسات والألوان المختارة
            if ($request->has('selected_sizes') && $request->has('selected_colors') && 
                is_array($request->selected_sizes) && is_array($request->selected_colors)) {
                // استخدام المخزون والأسعار للمقاسات والألوان فقط إذا كانت مصفوفات
                $stockData = is_array($request->input('stock')) ? $request->input('stock', []) : [];
                $priceData = is_array($request->input('price')) ? $request->input('price', []) : [];
                
                // حذف جميع المقاسات والألوان الحالية
                $product->colors()->detach();
                \DB::table('product_sizes')->where('product_id', $product->id)->delete();
                
                foreach ($request->selected_sizes as $sizeId) {
                    foreach ($request->selected_colors as $colorId) {
                        // التحقق من وجود مخزون لهذا المقاس واللون
                        $stock = isset($stockData[$sizeId][$colorId]) ? $stockData[$sizeId][$colorId] : null;
                        $price = isset($priceData[$sizeId][$colorId]) ? $priceData[$sizeId][$colorId] : null;
                        
                        if ($stock !== null && $stock > 0) {
                            // إضافة إلى جدول product_sizes
                            \DB::table('product_sizes')->insert([
                                'product_id' => $product->id,
                                'size_id' => $sizeId,
                                'color_id' => $colorId,
                                'stock' => $stock,
                                'price' => $price,
                                'is_available' => 1,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
                
                // ربط الألوان المختارة بالمنتج
                if (!empty($request->selected_colors)) {
                    foreach ($request->selected_colors as $colorId) {
                        $product->colors()->attach($colorId);
                    }
                }
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

            $product->colors()->delete();
            // حذف جميع مقاسات المنتج من جدول product_sizes
            \DB::table('product_sizes')->where('product_id', $product->id)->delete();
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
        $product->load(['category', 'images', 'colors', 'sizes', 'categories']);
        return view('admin.products.show', compact('product'));
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
}
