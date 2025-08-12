<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\QuantityDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuantityDiscountController extends Controller
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

    public function index()
    {
        $discounts = QuantityDiscount::with('product')
            ->latest()
            ->paginate(10);

        return view('admin.discounts.quantity.index', compact('discounts'));
    }

    public function create()
    {
        $products = Product::where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('admin.discounts.quantity.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'min_quantity' => ['required', 'integer', 'min:1'],
            'max_quantity' => ['nullable', 'integer', 'gt:min_quantity'],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean']
        ]);

        $overlapping = QuantityDiscount::where('product_id', $validated['product_id'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('min_quantity', '<=', $validated['min_quantity'])
                        ->where(function ($q) use ($validated) {
                            $q->whereNull('max_quantity')
                                ->orWhere('max_quantity', '>=', $validated['min_quantity']);
                        });
                })
                ->orWhere(function ($q) use ($validated) {
                    if (!empty($validated['max_quantity'])) {
                        $q->where('min_quantity', '<=', $validated['max_quantity'])
                            ->where(function ($q) use ($validated) {
                                $q->whereNull('max_quantity')
                                    ->orWhere('max_quantity', '>=', $validated['max_quantity']);
                            });
                    }
                });
            })
            ->exists();

        if ($overlapping) {
            return back()
                ->withInput()
                ->withErrors(['min_quantity' => 'يوجد تداخل في نطاقات الكميات مع خصومات أخرى لنفس المنتج']);
        }

        QuantityDiscount::create($validated);

        return redirect()
            ->route('admin.quantity-discounts.index')
            ->with('success', 'تم إضافة خصم الكمية بنجاح');
    }

    public function show(QuantityDiscount $quantityDiscount)
    {
        return view('admin.discounts.quantity.show', ['discount' => $quantityDiscount]);
    }

    public function edit(QuantityDiscount $quantityDiscount)
    {
        $products = Product::where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('admin.discounts.quantity.edit', compact('quantityDiscount', 'products'));
    }

    public function update(Request $request, QuantityDiscount $quantityDiscount)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'min_quantity' => ['required', 'integer', 'min:1'],
            'max_quantity' => ['nullable', 'integer', 'gt:min_quantity'],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean']
        ]);

        $overlapping = QuantityDiscount::where('product_id', $validated['product_id'])
            ->where('id', '!=', $quantityDiscount->id)
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('min_quantity', '<=', $validated['min_quantity'])
                        ->where(function ($q) use ($validated) {
                            $q->whereNull('max_quantity')
                                ->orWhere('max_quantity', '>=', $validated['min_quantity']);
                        });
                })
                ->orWhere(function ($q) use ($validated) {
                    if (!empty($validated['max_quantity'])) {
                        $q->where('min_quantity', '<=', $validated['max_quantity'])
                            ->where(function ($q) use ($validated) {
                                $q->whereNull('max_quantity')
                                    ->orWhere('max_quantity', '>=', $validated['max_quantity']);
                            });
                    }
                });
            })
            ->exists();

        if ($overlapping) {
            return back()
                ->withInput()
                ->withErrors(['min_quantity' => 'يوجد تداخل في نطاقات الكميات مع خصومات أخرى لنفس المنتج']);
        }

        $quantityDiscount->update($validated);

        return redirect()
            ->route('admin.quantity-discounts.index')
            ->with('success', 'تم تحديث خصم الكمية بنجاح');
    }

    public function destroy(QuantityDiscount $quantityDiscount)
    {
        $quantityDiscount->delete();

        return redirect()
            ->route('admin.quantity-discounts.index')
            ->with('success', 'تم حذف خصم الكمية بنجاح');
    }
}
