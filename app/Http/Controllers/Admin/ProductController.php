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
        return view('admin.products.create', compact('categories'));
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
            'stock' => 'required|integer|min:0',
        ];

        if ($request->has('has_colors')) {
            $rules['colors'] = 'required|array|min:1';
            $rules['colors.*'] = 'required|string|max:255';
            $rules['color_available'] = 'array';
            $rules['color_available.*'] = 'boolean';
        }

        if ($request->has('has_sizes')) {
            $rules['sizes'] = 'required|array|min:1';
            $rules['sizes.*'] = 'required|string|max:255';
            $rules['size_ids.*'] = 'nullable|exists:product_sizes,id';
            $rules['size_available.*'] = 'nullable|boolean';
            $rules['size_prices.*'] = 'nullable|numeric|min:0';
        }

        $validatedData = $request->validate($rules);

        try {
            DB::beginTransaction();

            if (empty($request->slug)) {
                $validatedData['slug'] = $this->generateSlugFromName($request->name);
            } else {
                $validatedData['slug'] = $this->generateUniqueSlug($request->slug);
            }

            $details = [];
            if ($request->has('detail_keys') && $request->has('detail_values')) {
                foreach ($request->detail_keys as $index => $key) {
                    if (!empty($key) && !empty($request->detail_values[$index])) {
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
            $validatedData['stock'] = $request->input('stock', 0);

            $product = Product::create($validatedData);

            if ($request->has('categories')) {
                $product->categories()->attach($request->categories);
            }

            if ($request->has('has_colors') && $request->has('colors')) {
                foreach ($request->colors as $index => $color) {
                    if (!empty($color)) {
                        $product->colors()->create([
                            'color' => $color,
                            'is_available' => $request->color_available[$index] ?? true
                        ]);
                    }
                }
            }

            if ($request->enable_size_selection && isset($request->sizes)) {
                foreach ($request->sizes as $index => $size) {
                    if (!empty($size)) {
                        $price = null;
                        if (isset($request->size_prices[$index]) && !empty($request->size_prices[$index])) {
                            $price = $request->size_prices[$index];
                        }

                        $product->sizes()->create([
                            'size' => $size,
                            'price' => $price,
                            'is_available' => isset($request->size_available[$index]) ? 1 : 0
                        ]);
                    }
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->uploadFile($image, 'products');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => $request->input('is_primary.' . $index, false)
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
        $selectedCategories = $product->categories->pluck('id')->toArray();
        return view('admin.products.edit', compact('product', 'categories', 'selectedCategories'));
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

            if ($request->has('has_colors')) {
                $rules['colors'] = 'required|array|min:1';
                $rules['colors.*'] = 'required|string|max:255';
                $rules['color_ids.*'] = 'nullable|exists:product_colors,id';
                $rules['color_available.*'] = 'nullable|boolean';
            }

            if ($request->has('has_sizes')) {
                $rules['sizes'] = 'required|array|min:1';
                $rules['sizes.*'] = 'required|string|max:255';
                $rules['size_ids.*'] = 'nullable|exists:product_sizes,id';
                $rules['size_available.*'] = 'nullable|boolean';
                $rules['size_prices.*'] = 'nullable|numeric|min:0';
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            if (empty($request->slug)) {
                $validated['slug'] = $this->generateSlugFromName($request->name, $product->id);
            } else if ($validated['slug'] !== $product->slug) {
                $validated['slug'] = $this->generateUniqueSlug($validated['slug'], 1, $product->id);
            }

            $details = [];
            if ($request->has('detail_keys') && $request->has('detail_values')) {
                foreach ($request->detail_keys as $index => $key) {
                    if (!empty($key) && !empty($request->detail_values[$index])) {
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

            $product->categories()->sync($request->categories ?? []);

            if ($request->has('has_colors')) {
                $currentColorIds = $product->colors->pluck('id')->toArray();
                $updatedColorIds = array_filter($request->color_ids ?? []);
                $deletedColorIds = array_diff($currentColorIds, $updatedColorIds);

                if (!empty($deletedColorIds)) {
                    $product->colors()->whereIn('id', $deletedColorIds)->delete();
                }

                foreach ($request->colors as $index => $colorName) {
                    if (!empty($colorName)) {
                        $colorId = $request->color_ids[$index] ?? null;
                        $colorData = [
                            'color' => $colorName,
                            'is_available' => $request->color_available[$index] ?? true
                        ];

                        if ($colorId && in_array($colorId, $currentColorIds)) {
                            $product->colors()->where('id', $colorId)->update($colorData);
                        } else {
                            $product->colors()->create($colorData);
                        }
                    }
                }
            } else {
                $product->colors()->delete();
            }

            if ($request->has('has_sizes')) {
                $currentSizeIds = $product->sizes->pluck('id')->toArray();
                $updatedSizeIds = array_filter($request->size_ids ?? []);
                $deletedSizeIds = array_diff($currentSizeIds, $updatedSizeIds);

                if (!empty($deletedSizeIds)) {
                    $product->sizes()->whereIn('id', $deletedSizeIds)->delete();
                }

                foreach ($request->sizes as $index => $sizeName) {
                    if (!empty($sizeName)) {
                        $sizeId = $request->size_ids[$index] ?? null;
                        $sizeData = [
                            'size' => $sizeName,
                            'is_available' => $request->size_available[$index] ?? true
                        ];

                        if (isset($request->size_prices[$index])) {
                            $sizeData['price'] = $request->size_prices[$index];
                        }

                        if ($sizeId && in_array($sizeId, $currentSizeIds)) {
                            $product->sizes()->where('id', $sizeId)->update($sizeData);
                        } else {
                            $product->sizes()->create($sizeData);
                        }
                    }
                }
            } else {
                $product->sizes()->delete();
            }

            if ($request->has('remove_images')) {
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
                        'is_primary' => $request->input('is_primary_new.' . $index, false)
                    ]);
                }
            }

            if ($request->has('is_primary')) {
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
            $product->sizes()->delete();
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
