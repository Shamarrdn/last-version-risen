<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductSize;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Validator;

class SizesColorsController extends Controller
{
    /**
     * عرض صفحة الألوان والمقاسات
     */
    public function index()
    {
        $sizes = ProductSize::all();
        $colors = ProductColor::all();
        
        return view('admin.sizes-colors.index', compact('sizes', 'colors'));
    }

    /**
     * عرض صفحة الألوان والمقاسات للسوبر أدمن
     */
    public function superadminIndex()
    {
        $sizes = ProductSize::all();
        $colors = ProductColor::all();
        
        return view('admin.superadmin.sizes-colors.index', compact('sizes', 'colors'));
    }

    /**
     * إضافة مقاس جديد
     */
    public function storeSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $exists = ProductSize::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail('اسم المقاس موجود بالفعل');
                    }
                }
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'اسم المقاس مطلوب',
            'name.string' => 'اسم المقاس يجب أن يكون نص',
            'name.max' => 'اسم المقاس يجب أن لا يتجاوز 50 حرف',
            'description.max' => 'الوصف يجب أن لا يتجاوز 255 حرف',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $size = ProductSize::create([
                'name' => trim($request->name),
                'description' => $request->description ? trim($request->description) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المقاس بنجاح',
                'size' => $size
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المقاس',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة لون جديد
     */
    public function storeColor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $exists = ProductColor::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail('اسم اللون موجود بالفعل');
                    }
                }
            ],
            'code' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'اسم اللون مطلوب',
            'name.string' => 'اسم اللون يجب أن يكون نص',
            'name.max' => 'اسم اللون يجب أن لا يتجاوز 50 حرف',
            'code.required' => 'كود اللون مطلوب',
            'code.string' => 'كود اللون يجب أن يكون نص',
            'code.max' => 'كود اللون يجب أن لا يتجاوز 7 أحرف',
            'code.regex' => 'كود اللون يجب أن يكون بصيغة صحيحة (مثال: #FF0000)',
            'description.max' => 'الوصف يجب أن لا يتجاوز 255 حرف',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $color = ProductColor::create([
                'name' => trim($request->name),
                'code' => strtoupper($request->code),
                'description' => $request->description ? trim($request->description) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة اللون بنجاح',
                'color' => $color
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة اللون',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث مقاس
     */
    public function updateSize(Request $request, $id)
    {
        // إضافة debugging
        \Log::info('Update Size Request', [
            'id' => $id,
            'data' => $request->all(),
            'method' => $request->method()
        ]);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = ProductSize::whereRaw('LOWER(name) = ?', [strtolower($value)])
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('اسم المقاس موجود بالفعل');
                    }
                }
            ],
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'اسم المقاس مطلوب',
            'name.string' => 'اسم المقاس يجب أن يكون نص',
            'name.max' => 'اسم المقاس يجب أن لا يتجاوز 50 حرف',
            'description.max' => 'الوصف يجب أن لا يتجاوز 255 حرف',
        ]);

        if ($validator->fails()) {
            \Log::error('Size Update Validation Failed', [
                'id' => $id,
                'errors' => $validator->errors()->toArray()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $size = ProductSize::findOrFail($id);
            $size->update([
                'name' => trim($request->name),
                'description' => $request->description ? trim($request->description) : null,
            ]);

            \Log::info('Size Updated Successfully', [
                'id' => $id,
                'new_name' => $size->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المقاس بنجاح',
                'size' => $size
            ]);
        } catch (\Exception $e) {
            \Log::error('Size Update Error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المقاس',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث لون
     */
    public function updateColor(Request $request, $id)
    {
        // إضافة debugging
        \Log::info('Update Color Request', [
            'id' => $id,
            'data' => $request->all(),
            'method' => $request->method()
        ]);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = ProductColor::whereRaw('LOWER(name) = ?', [strtolower($value)])
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('اسم اللون موجود بالفعل');
                    }
                }
            ],
            'code' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'اسم اللون مطلوب',
            'name.string' => 'اسم اللون يجب أن يكون نص',
            'name.max' => 'اسم اللون يجب أن لا يتجاوز 50 حرف',
            'code.required' => 'كود اللون مطلوب',
            'code.string' => 'كود اللون يجب أن يكون نص',
            'code.max' => 'كود اللون يجب أن لا يتجاوز 7 أحرف',
            'code.regex' => 'كود اللون يجب أن يكون بصيغة صحيحة (مثال: #FF0000)',
            'description.max' => 'الوصف يجب أن لا يتجاوز 255 حرف',
        ]);

        if ($validator->fails()) {
            \Log::error('Color Update Validation Failed', [
                'id' => $id,
                'errors' => $validator->errors()->toArray()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $color = ProductColor::findOrFail($id);
            $color->update([
                'name' => trim($request->name),
                'code' => strtoupper($request->code),
                'description' => $request->description ? trim($request->description) : null,
            ]);

            \Log::info('Color Updated Successfully', [
                'id' => $id,
                'new_name' => $color->name,
                'new_code' => $color->code
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث اللون بنجاح',
                'color' => $color
            ]);
        } catch (\Exception $e) {
            \Log::error('Color Update Error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث اللون',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف مقاس
     */
    public function destroySize($id)
    {
        try {
            $size = ProductSize::findOrFail($id);
            $size->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المقاس بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المقاس',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف لون
     */
    public function destroyColor($id)
    {
        try {
            $color = ProductColor::findOrFail($id);
            $color->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف اللون بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف اللون',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على بيانات مقاس
     */
    public function getSize($id)
    {
        try {
            $size = ProductSize::findOrFail($id);
            return response()->json([
                'success' => true,
                'size' => $size
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'المقاس غير موجود'
            ], 404);
        }
    }

    /**
     * الحصول على بيانات لون
     */
    public function getColor($id)
    {
        try {
            $color = ProductColor::findOrFail($id);
            return response()->json([
                'success' => true,
                'color' => $color
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'اللون غير موجود'
            ], 404);
        }
    }

    /**
     * الحصول على جميع المقاسات
     */
    public function getSizes()
    {
        $sizes = ProductSize::all();
        return response()->json([
            'success' => true,
            'sizes' => $sizes
        ]);
    }

    /**
     * الحصول على جميع الألوان
     */
    public function getColors()
    {
        $colors = ProductColor::all();
        return response()->json([
            'success' => true,
            'colors' => $colors
        ]);
    }
}
