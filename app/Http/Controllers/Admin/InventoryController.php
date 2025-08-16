<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductSizeColorInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventory = ProductSizeColorInventory::with(['product', 'size', 'color'])
            ->orderBy('product_id')
            ->paginate(20);
            
        return view('admin.inventory.index', compact('inventory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $sizes = ProductSize::orderBy('name')->get();
        $colors = ProductColor::orderBy('name')->get();
        
        return view('admin.inventory.create', compact('products', 'sizes', 'colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'nullable|exists:size_options,id',
            'color_id' => 'nullable|exists:color_options,id',
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);
        
        // Check if inventory item already exists
        $existingInventory = ProductSizeColorInventory::where([
            'product_id' => $validated['product_id'],
            'size_id' => $validated['size_id'],
            'color_id' => $validated['color_id'],
        ])->first();
        
        if ($existingInventory) {
            // Update existing inventory
            $existingInventory->stock += $validated['stock'];
            $existingInventory->is_available = $existingInventory->stock > 0;
            
            if (isset($validated['price']) && $validated['price'] > 0) {
                $existingInventory->price = $validated['price'];
            }
            
            $existingInventory->save();
            
            return redirect()->route('admin.inventory.index')
                ->with('success', 'تم تحديث المخزون الموجود بنجاح');
        }
        
        // Create new inventory item
        $inventory = new ProductSizeColorInventory($validated);
        $inventory->is_available = $validated['stock'] > 0;
        $inventory->save();
        
        return redirect()->route('admin.inventory.index')
            ->with('success', 'تم إضافة المخزون بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $inventory = ProductSizeColorInventory::with(['product', 'size', 'color'])->findOrFail($id);
        return view('admin.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $inventory = ProductSizeColorInventory::with(['product', 'size', 'color'])->findOrFail($id);
        return view('admin.inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $inventory = ProductSizeColorInventory::findOrFail($id);
        
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
        ]);
        
        // Update inventory
        $inventory->update($validated);
        
        // If stock was increased and item was previously unavailable, make it available
        if ($inventory->stock > 0 && !$inventory->is_available) {
            $inventory->is_available = true;
            $inventory->save();
        }
        
        return redirect()->route('admin.inventory.index')
            ->with('success', 'تم تحديث المخزون بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $inventory = ProductSizeColorInventory::findOrFail($id);
        
        try {
            $inventory->delete();
            return redirect()->route('admin.inventory.index')
                ->with('success', 'تم حذف المخزون بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting inventory: ' . $e->getMessage());
            return redirect()->route('admin.inventory.index')
                ->with('error', 'حدث خطأ أثناء محاولة حذف المخزون');
        }
    }
}
