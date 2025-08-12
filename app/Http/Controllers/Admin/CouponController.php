<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CouponController extends Controller
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
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = \App\Models\Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $messages = [
            'code.required' => 'حقل كود الخصم مطلوب',
            'code.unique' => 'كود الخصم مستخدم بالفعل',
            'name.required' => 'حقل اسم الكوبون مطلوب',
            'type.required' => 'نوع الخصم مطلوب',
            'type.in' => 'نوع الخصم يجب أن يكون نسبة مئوية أو قيمة ثابتة',
            'value.required' => 'قيمة الخصم مطلوبة',
            'value.numeric' => 'قيمة الخصم يجب أن تكون رقم',
            'value.min' => 'قيمة الخصم يجب أن تكون على الأقل 0',
            'min_order_amount.numeric' => 'الحد الأدنى لقيمة الطلب يجب أن يكون رقم',
            'min_order_amount.min' => 'الحد الأدنى لقيمة الطلب يجب أن يكون على الأقل 0',
            'max_uses.integer' => 'الحد الأقصى للاستخدام يجب أن يكون رقم صحيح',
            'max_uses.min' => 'الحد الأقصى للاستخدام يجب أن يكون على الأقل 0',
            'is_active.boolean' => 'حقل الكوبون نشط يجب أن يكون صح أو خطأ',
            'applies_to_all_products.boolean' => 'حقل "يطبق على جميع المنتجات" يجب أن يكون صح أو خطأ',
            'products.required_without_all' => 'يجب اختيار منتجات أو تصنيفات عندما لا يطبق الكوبون على جميع المنتجات',
            'categories.required_without_all' => 'يجب اختيار منتجات أو تصنيفات عندما لا يطبق الكوبون على جميع المنتجات',
            'starts_at.date' => 'تاريخ بدء الصلاحية يجب أن يكون تاريخ صحيح',
            'expires_at.date' => 'تاريخ انتهاء الصلاحية يجب أن يكون تاريخ صحيح',
            'expires_at.after_or_equal' => 'تاريخ انتهاء الصلاحية يجب أن يكون بعد تاريخ البدء أو مساوي له'
        ];

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'applies_to_all_products' => 'sometimes|boolean',
            'products' => 'required_without_all:applies_to_all_products,categories|array',
            'products.*' => 'exists:products,id',
            'categories' => 'required_without_all:applies_to_all_products,products|array',
            'categories.*' => 'exists:categories,id',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ], $messages);

        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()
                ->withInput()
                ->withErrors(['value' => 'قيمة الخصم للنسبة المئوية يجب أن تكون بين 0 و 100']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['applies_to_all_products'] = $request->has('applies_to_all_products');

        $coupon = Coupon::create($validated);

        // حفظ المنتجات والتصنيفات المحددة إذا لم يكن الكوبون يطبق على جميع المنتجات
        if (!$validated['applies_to_all_products']) {
            if (isset($validated['products'])) {
                $coupon->products()->attach($validated['products']);
            }

            if (isset($validated['categories'])) {
                $coupon->categories()->attach($validated['categories']);
            }
        }

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم إنشاء كود الخصم بنجاح');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load('products', 'categories');
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $products = \App\Models\Product::orderBy('name')->get();
        $selectedProducts = $coupon->products->pluck('id')->toArray();

        $categories = Category::orderBy('name')->get();
        $selectedCategories = $coupon->categories->pluck('id')->toArray();

        return view('admin.coupons.edit', compact('coupon', 'products', 'selectedProducts', 'categories', 'selectedCategories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $messages = [
            'code.required' => 'حقل كود الخصم مطلوب',
            'code.unique' => 'كود الخصم مستخدم بالفعل',
            'name.required' => 'حقل اسم الكوبون مطلوب',
            'type.required' => 'نوع الخصم مطلوب',
            'type.in' => 'نوع الخصم يجب أن يكون نسبة مئوية أو قيمة ثابتة',
            'value.required' => 'قيمة الخصم مطلوبة',
            'value.numeric' => 'قيمة الخصم يجب أن تكون رقم',
            'value.min' => 'قيمة الخصم يجب أن تكون على الأقل 0',
            'min_order_amount.numeric' => 'الحد الأدنى لقيمة الطلب يجب أن يكون رقم',
            'min_order_amount.min' => 'الحد الأدنى لقيمة الطلب يجب أن يكون على الأقل 0',
            'max_uses.integer' => 'الحد الأقصى للاستخدام يجب أن يكون رقم صحيح',
            'max_uses.min' => 'الحد الأقصى للاستخدام يجب أن يكون على الأقل 0',
            'is_active.boolean' => 'حقل الكوبون نشط يجب أن يكون صح أو خطأ',
            'applies_to_all_products.boolean' => 'حقل "يطبق على جميع المنتجات" يجب أن يكون صح أو خطأ',
            'products.required_without_all' => 'يجب اختيار منتجات أو تصنيفات عندما لا يطبق الكوبون على جميع المنتجات',
            'categories.required_without_all' => 'يجب اختيار منتجات أو تصنيفات عندما لا يطبق الكوبون على جميع المنتجات',
            'starts_at.date' => 'تاريخ بدء الصلاحية يجب أن يكون تاريخ صحيح',
            'expires_at.date' => 'تاريخ انتهاء الصلاحية يجب أن يكون تاريخ صحيح',
            'expires_at.after_or_equal' => 'تاريخ انتهاء الصلاحية يجب أن يكون بعد تاريخ البدء أو مساوي له'
        ];

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons')->ignore($coupon->id)],
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'applies_to_all_products' => 'sometimes|boolean',
            'products' => 'required_without_all:applies_to_all_products,categories|array',
            'products.*' => 'exists:products,id',
            'categories' => 'required_without_all:applies_to_all_products,products|array',
            'categories.*' => 'exists:categories,id',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ], $messages);

        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()
                ->withInput()
                ->withErrors(['value' => 'قيمة الخصم للنسبة المئوية يجب أن تكون بين 0 و 100']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['applies_to_all_products'] = $request->has('applies_to_all_products');

        $coupon->update($validated);

        // تحديث المنتجات والتصنيفات المحددة
        $coupon->products()->detach();
        $coupon->categories()->detach();

        if (!$validated['applies_to_all_products']) {
            if (isset($validated['products'])) {
                $coupon->products()->attach($validated['products']);
            }

            if (isset($validated['categories'])) {
                $coupon->categories()->attach($validated['categories']);
            }
        }

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم تحديث كود الخصم بنجاح');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم حذف كود الخصم بنجاح');
    }

    /**
     * Generate a unique random coupon code
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCode(Request $request)
    {
        // تحقق من البيانات الواردة
        $type = $request->input('type', 'alphanumeric');
        $length = (int) $request->input('length', 8);

        // Limit length between 4 and 16 characters
        $length = max(4, min(16, $length));

        // التحقق من أن النوع صالح
        if (!in_array($type, ['alphanumeric', 'alphabetic', 'numeric'])) {
            $type = 'alphanumeric';
        }

        $code = $this->createUniqueCode($type, $length);

        return response()->json([
            'success' => true,
            'code' => $code
        ]);
    }

    /**
     * Create a unique coupon code
     *
     * @param string $type Type of code (alphanumeric, alphabetic, numeric)
     * @param int $length Length of the code
     * @return string
     */
    private function createUniqueCode($type, $length)
    {
        $attempts = 0;
        $maxAttempts = 10;

        do {
            switch ($type) {
                case 'alphabetic':
                    $code = strtoupper(Str::random($length));
                    // Remove ambiguous characters
                    $code = str_replace(['O', 'I'], ['A', 'H'], $code);
                    break;

                case 'numeric':
                    $code = '';
                    for ($i = 0; $i < $length; $i++) {
                        $code .= mt_rand(0, 9);
                    }
                    break;

                case 'alphanumeric':
                default:
                    // Generate a random alphanumeric code without ambiguous characters
                    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                    $code = '';
                    for ($i = 0; $i < $length; $i++) {
                        $code .= $chars[mt_rand(0, strlen($chars) - 1)];
                    }
            }

            // Check if code exists
            $exists = Coupon::where('code', $code)->exists();
            $attempts++;

        } while ($exists && $attempts < $maxAttempts);

        // If we couldn't generate a unique code after max attempts, add a random suffix
        if ($exists) {
            $code .= substr(uniqid(), -4);
        }

        return $code;
    }
}
