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

    /**
     * تحديث سريع للمخزون عبر AJAX
     */
    public function quickUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'inventory_id' => 'required|exists:product_size_color_inventory,id',
                'stock' => 'required|integer|min:0',
            ]);

            $inventory = ProductSizeColorInventory::findOrFail($validated['inventory_id']);
            
            // حفظ المخزون القديم للمقارنة
            $oldStock = $inventory->stock;
            
            // تحديث المخزون
            $inventory->stock = $validated['stock'];
            $inventory->is_available = $validated['stock'] > 0;
            $inventory->save();

            // تحديث حالة المنتج الرئيسي
            $inventory->updateAvailabilityStatus();

            Log::info('تم تحديث المخزون بنجاح', [
                'inventory_id' => $inventory->id,
                'old_stock' => $oldStock,
                'new_stock' => $validated['stock'],
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المخزون بنجاح',
                'inventory' => [
                    'id' => $inventory->id,
                    'stock' => $inventory->stock,
                    'consumed_stock' => $inventory->consumed_stock,
                    'available_stock' => $inventory->available_stock,
                    'is_available' => $inventory->is_available,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث المخزون: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المخزون'
            ], 500);
        }
    }

    /**
     * فلترة المخزون عبر AJAX
     */
    public function filter(Request $request)
    {
        try {
            $query = ProductSizeColorInventory::with(['product', 'size', 'color']);

            // البحث في اسم المنتج
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->whereHas('product', function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            // فلترة حسب حالة المخزون
            if ($request->filled('stock_status')) {
                switch ($request->stock_status) {
                    case 'available':
                        $query->whereRaw('stock > consumed_stock');
                        break;
                    case 'low':
                        $query->whereRaw('(stock - consumed_stock) <= 5 AND (stock - consumed_stock) > 0');
                        break;
                    case 'out':
                        $query->whereRaw('stock <= consumed_stock');
                        break;
                }
            }

            // فلترة حسب المقاس
            if ($request->filled('size_id')) {
                $query->where('size_id', $request->size_id);
            }

            // فلترة حسب اللون
            if ($request->filled('color_id')) {
                $query->where('color_id', $request->color_id);
            }

            $inventory = $query->orderBy('product_id')->get();
            
            // إنشاء HTML للجدول
            $html = '';
            foreach ($inventory as $item) {
                $html .= $this->renderInventoryRow($item);
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'total' => $inventory->count()
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في فلترة المخزون: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء فلترة البيانات'
            ], 500);
        }
    }

    /**
     * إنشاء HTML لصف المخزون
     */
    private function renderInventoryRow($item)
    {
        $statusBadge = '';
        if ($item->is_available && $item->available_stock > 0) {
            $statusBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>متاح</span>';
        } elseif ($item->available_stock <= 5 && $item->available_stock > 0) {
            $statusBadge = '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle me-1"></i>منخفض</span>';
        } else {
            $statusBadge = '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>نفذ</span>';
        }

        $availableBadgeClass = $item->available_stock > 20 ? 'bg-success' : ($item->available_stock > 5 ? 'bg-warning' : 'bg-danger');

        return '
        <tr>
            <td>'.$item->id.'</td>
            <td>
                <div class="d-flex align-items-center">
                    '.($item->product ? '
                        <img src="'.$item->product->image_url.'" alt="'.$item->product->name.'" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <div class="fw-bold">'.$item->product->name.'</div>
                            <small class="text-muted">'.substr($item->product->description, 0, 30).'</small>
                        </div>
                    ' : '<span class="text-muted">منتج غير موجود</span>').'
                </div>
            </td>
            <td>
                '.($item->size ? '<span class="badge bg-info">'.$item->size->name.'</span>' : '<span class="text-muted">-</span>').'
            </td>
            <td>
                '.($item->color ? '
                    <div class="d-flex align-items-center">
                        <span class="color-circle me-2" style="background-color: '.($item->color->code ?? '#ccc').'"></span>
                        <span>'.$item->color->name.'</span>
                    </div>
                ' : '<span class="text-muted">-</span>').'
            </td>
            <td><span class="fw-bold">'.$item->stock.'</span></td>
            <td><span class="text-warning fw-bold">'.$item->consumed_stock.'</span></td>
            <td>
                <span class="badge '.$availableBadgeClass.'" id="available-'.$item->id.'">'.$item->available_stock.'</span>
            </td>
            <td>
                <div class="price-cell">
                    <span class="fw-bold text-primary">'.($item->price ? number_format($item->price, 2) . ' ر.س' : '-').'</span>
                </div>
            </td>
            <td>
                <div class="status-cell" id="status-'.$item->id.'">'.$statusBadge.'</div>
            </td>
            <td>
                <div class="quick-update-controls">
                    <div class="input-group input-group-sm" style="max-width: 200px;">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="adjustStock('.$item->id.', -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="form-control text-center" id="stock-input-'.$item->id.'" value="'.$item->stock.'" min="0">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="adjustStock('.$item->id.', 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn btn-primary btn-sm" type="button" onclick="updateStock('.$item->id.')">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </div>
            </td>
            <td>
                <div class="btn-group">
                    <a href="'.route('admin.inventory.edit', $item->id).'" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal'.$item->id.'">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>';
    }
}
