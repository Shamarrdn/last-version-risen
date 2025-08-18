@extends($adminLayout)

@php
// Helper function to safely get old input values
function safeOld($key, $default = '') {
    $value = old($key, $default);
    if (is_array($value)) {
        return $value[0] ?? $default;
    }
    return $value;
}

// Helper function to safely get old input values as integer
function safeOldInt($key, $default = 0) {
    $value = safeOld($key, $default);
    return intval($value);
}

// Helper function to safely get old input values for arrays
function safeOldArray($key, $default = []) {
    $value = old($key, $default);
    if (!is_array($value)) {
        return $default;
    }
    return $value;
}
@endphp

@section('title', 'تعديل المنتج - ' . $product->name)
@section('page_title', 'تعديل المنتج: ' . $product->name)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="products-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-edit text-primary me-2"></i>
                                            تعديل المنتج
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-light-info me-2">
                                                <i class="fas fa-eye me-1"></i>
                                                عرض المنتج
                                            </a>
                                            <a href="{{ route('admin.products.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للمنتجات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add this after the form opening tag -->
                        @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <h5 class="alert-heading mb-2">يوجد أخطاء في النموذج:</h5>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Debug information -->
                        @if(config('app.debug'))
                        <div class="alert alert-info mb-4">
                            <h6>Debug Information:</h6>
                            <pre>{{ print_r($errors->toArray(), true) }}</pre>
                            <h6>Request Data:</h6>
                            <pre>{{ print_r(request()->all(), true) }}</pre>
                        </div>
                        @endif
                        @endif

                        <!-- Form -->
                        <form id="product-form" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                @method('PUT')

                            <div class="row g-4">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                معلومات أساسية
                                            </h5>
                                            <div class="mb-3">
                                                <label class="form-label">اسم المنتج</label>
                                                <input type="text" name="name"
                                                    class="form-control shadow-sm @error('name') is-invalid @enderror"
                                                    value="{{ safeOld('name', $product->name) }}">
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="category_id" class="form-label required">التصنيف الرئيسي</label>
                                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                                    <option value="">اختر التصنيف الرئيسي</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ safeOld('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="form-label">التصنيفات الإضافية (اختياري)</label>
                                                <div class="card border shadow-sm p-3">
                                                    <div class="row g-2">
                                                        @foreach($categories as $category)
                                                        <div class="col-md-4 col-sm-6">
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                    class="form-check-input"
                                                                    id="category-{{ $category->id }}"
                                                                    name="categories[]"
                                                                    value="{{ $category->id }}"
                                                                    {{ in_array($category->id, safeOldArray('categories', $selectedCategories)) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="category-{{ $category->id }}">
                                                                    {{ $category->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <small class="form-text text-muted">اختر التصنيفات الإضافية التي تريد إضافة المنتج إليها</small>
                                                @error('categories')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="isAvailable"
                                                        name="is_available" value="1" {{ safeOld('is_available', $product->is_available) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isAvailable">متاح للبيع</label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">الرابط المختصر (Slug)</label>
                                                <input type="text" name="slug"
                                                    class="form-control shadow-sm @error('slug') is-invalid @enderror"
                                                    value="{{ safeOld('slug', $product->slug) }}" readonly disabled>
                                                @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">يتم إنشاء الرابط المختصر تلقائياً من اسم المنتج</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description and Images -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                الوصف والصور
                                            </h5>
                                            <div class="mb-3">
                                                <label class="form-label">الوصف</label>
                                                <textarea name="description" class="form-control shadow-sm"
    rows="4">{{ safeOld('description', $product->description) }}</textarea>
                                                @error('description')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-tag text-primary me-2"></i>
                                                    السعر الأساسي
                                                </label>
                                                <div class="input-group shadow-sm">
                                                    <input type="number" name="base_price" class="form-control @error('base_price') is-invalid @enderror"
                                                        placeholder="السعر الأساسي" step="0.01" min="0"
                                                        value="{{ safeOld('base_price', $product->base_price) }}">
                                                    <span class="input-group-text">ر.س</span>
                                                </div>
                                                <small class="text-muted">سيتم استخدام هذا السعر إذا لم تكن هناك مقاسات بأسعار محددة</small>
                                                @error('base_price')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-boxes text-primary me-2"></i>
                                                    المخزون
                                                </label>
                                                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" placeholder="كمية المخزون" min="0" value="{{ safeOldInt('stock', $product->stock) }}">
                                                <small class="text-muted">حدد كمية المخزون المتاحة لهذا المنتج</small>
                                                @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Product Details -->
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-list-ul text-primary me-2"></i>
                                                    تفاصيل المنتج
                                                </label>
                                                <div class="alert alert-light border">
                                                    <small class="text-muted">أضف تفاصيل إضافية للمنتج مثل الأبعاد، البراند، بلد المنشأ، إلخ...</small>
                                                </div>
                                                <div id="detailsContainer">
                                                    @if(safeOldArray('detail_keys'))
@foreach(safeOldArray('detail_keys') as $index => $key)
                                                            <div class="input-group mb-2 shadow-sm">
                                                                <input type="text" name="detail_keys[]" class="form-control" placeholder="الخاصية" value="{{ $key }}">
                                                                <input type="text" name="detail_values[]" class="form-control" placeholder="القيمة" value="{{ safeOldArray('detail_values')[$index] ?? '' }}">
                                                                <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    @elseif($product->details)
                                                        @foreach($product->details as $key => $value)
                                                            <div class="input-group mb-2 shadow-sm">
                                                                <input type="text" name="detail_keys[]" class="form-control" placeholder="الخاصية" value="{{ $key }}">
                                                                <input type="text" name="detail_values[]" class="form-control" placeholder="القيمة" value="{{ $value }}">
                                                                <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm mt-2" onclick="addDetailInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة تفاصيل
                                                </button>
                                            </div>

                                            <!-- Current Images -->
                                            <div class="mb-3">
                                                <label class="form-label">الصور الحالية</label>
                                                <div class="row g-2 mb-2">
                                                    @foreach($product->images as $image)
                                                    <div class="col-auto">
                                                        <div class="position-relative">
                                                            <img src="{{ url('storage/' . $image->image_path) }}"
                                                                alt="صورة المنتج"
                                                                class="rounded"
                                                                style="width: 80px; height: 80px; object-fit: cover;">
                                                            <div class="position-absolute top-0 end-0 p-1">
                                                                <div class="form-check">
                                                                    <input type="radio" name="is_primary" value="{{ $image->id }}"
                                                                        class="form-check-input" @checked($image->is_primary)>
                                                                </div>
                                                            </div>
                                                            <div class="position-absolute bottom-0 start-0 p-1">
                                                                <div class="form-check">
                                                                    <input type="checkbox" name="remove_images[]" value="{{ $image->id }}"
                                                                        class="form-check-input">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <div class="small text-muted">
                                                    * حدد الصور للحذف
                                                    <br>
                                                    * اختر الصورة الرئيسية
                                                </div>
                                            </div>

                                            <!-- New Images -->
                                            <div class="mb-3">
                                                <label class="form-label">إضافة صور جديدة</label>
                                                @error('new_images.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('is_primary.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="newImagesContainer">
                                                    <div class="mb-2">
                                                        <div class="input-group shadow-sm">
                                                            <input type="file" name="new_images[]" class="form-control" accept="image/*">
                                                            <div class="input-group-text">
                                                                <label class="mb-0">
                                                                    <input type="radio" name="is_primary_new[0]" value="1" class="me-1">
                                                                    صورة رئيسية
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm mt-2" onclick="addNewImageInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة صورة
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <!-- Product Options -->
                                

                                <!-- إدارة المقاسات والألوان والمخزون -->
                                <div class="col-12 mt-4">
                                    <div class="card card-body shadow-sm border-0">
                                        <div class="card-title d-flex align-items-center justify-content-between">
                                            <h5>
                                                <i class="fas fa-palette me-2 text-primary"></i>
                                                إدارة المقاسات والألوان والمخزون
                                                </h5>
                                            <small class="text-muted">اختر المقاسات والألوان المتاحة وأدخل بيانات المخزون</small>
                                        </div>
                                        
                                        @if(isset($availableSizes) && $availableSizes->isEmpty() || isset($availableColors) && $availableColors->isEmpty())
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>تنبيه:</strong>
                                            @if(isset($availableSizes) && $availableSizes->isEmpty())
                                                لا توجد مقاسات متاحة. 
                                            @endif
                                            @if(isset($availableColors) && $availableColors->isEmpty())
                                                لا توجد ألوان متاحة.
                                            @endif
                                            سيتم إنشاء مقاسات وألوان افتراضية تلقائياً.
                                        </div>
                                        @endif

                                        <!-- النظام الجديد لإدارة المخزون -->
                                        <div id="newInventorySystem" class="mt-4">
                                            <h6 class="fw-bold mb-3">
                                                <i class="fas fa-boxes me-2"></i>
                                                إدارة المخزون التفصيلية
                                            </h6>
                                            
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>معلومات:</strong> يمكنك تعديل المقاسات والألوان المتعددة مع تحديد المخزون والسعر لكل مجموعة.
                                            </div>
                                            
                                            <div id="inventoryMatrix">
                                                <!-- سيتم إنشاء مصفوفة المخزون هنا ديناميكياً -->
                                            </div>
                                            
                                            <div class="text-center mt-3">
                                                <button type="button" class="btn btn-primary" onclick="addInventoryRow()">
                                                    <i class="fas fa-plus me-2"></i>
                                                    إضافة مقاس ولون جديد
                                                </button>
                                            </div>
                                        </div>

                                        <!-- النظام القديم (للتوافق) -->
                                        <div id="oldInventorySystem" class="mt-4" style="display: none;">
                                            <h6 class="fw-bold mb-3">
                                                <i class="fas fa-cogs me-2"></i>
                                                تفاصيل المقاسات والألوان (النظام القديم)
                                            </h6>
                                            <div id="sizeColorMatrix">
                                                <!-- سيتم إنشاء المقاسات هنا ديناميكياً -->
                                            </div>
                                            <button type="button" class="add-size-btn" id="addSizeButton">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة مقاس جديد
                                            </button>
                                        </div>
                                        
                                        <!-- زر تشخيص مؤقت -->
                                        <button type="button" class="btn btn-warning btn-sm mt-2" onclick="debugFormData()" style="display: block;">
                                            <i class="fas fa-bug me-2"></i>
                                            تشخيص البيانات
                                        </button>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                                                        <button type="submit" class="btn btn-primary" onclick="return prepareFormData()">
                                <i class="fas fa-save me-2"></i>
                                حفظ التغييرات
                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="/assets/css/admin/products.css">
<style>
    .color-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    
    .size-color-matrix {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .matrix-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .matrix-table th {
        background: #4A5568;
        color: white;
        border: none;
        padding: 12px;
        text-align: center;
        font-weight: 600;
    }
    
    .matrix-table td {
        padding: 12px;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    
    .matrix-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.875rem;
        padding: 8px 12px;
        border-radius: 20px;
    }
    
    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    
    .form-control-sm {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .card-title h5 {
        color: #212529;
        font-weight: 600;
    }
    
    .text-muted {
        font-size: 0.875rem;
    }
    
    /* تصميم المقاسات الجديد - بألوان هادئة */
    .size-container {
        background: #EDF2F7;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #E2E8F0;
        position: relative;
        overflow: hidden;
        color: #2D3748;
    }
    
    .size-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #4A5568;
    }
    
    .size-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .size-title {
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .size-number {
        background: #4A5568;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        margin-right: 8px;
    }
    
    /* تصميم النظام الجديد لإدارة المخزون */
    .inventory-row {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .inventory-row:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
    }
    
    .inventory-row .card-body {
        padding: 1.5rem;
    }
    
    .inventory-row .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .inventory-row .form-select,
    .inventory-row .form-control {
        border: 1px solid #ced4da;
        border-radius: 6px;
        transition: border-color 0.3s ease;
    }
    
    .inventory-row .form-select:focus,
    .inventory-row .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }
    
    .inventory-row .btn-danger {
        background: #dc3545;
        border: none;
        transition: background-color 0.3s ease;
    }
    
    .inventory-row .btn-danger:hover {
        background: #c82333;
        transform: translateY(-1px);
    }
    
    #newInventorySystem {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    
    #newInventorySystem h6 {
        color: #007bff;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    #newInventorySystem .alert-info {
        background: rgba(0, 123, 255, 0.1);
        border: 1px solid rgba(0, 123, 255, 0.2);
        color: #0056b3;
    }
    
    #inventoryMatrix {
        margin-bottom: 20px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }
    
    .size-remove-btn {
        background: #E53E3E;
        border: none;
        color: white;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .size-remove-btn:hover {
        background: #C53030;
    }
    
    .size-select {
        background: #FFFFFF;
        border: 1px solid #CBD5E0;
        border-radius: 6px;
        padding: 8px 12px;
        color: #2D3748;
        font-weight: 500;
        margin-bottom: 15px;
        width: 100%;
    }
    
    .size-select:focus {
        outline: none;
        border-color: #4A5568;
        box-shadow: 0 0 0 2px rgba(74, 85, 104, 0.2);
    }
    
    .colors-section {
        background: #FFFFFF;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        border: 1px solid #E2E8F0;
    }
    
    .color-item {
        background: #F7FAFC;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
        position: relative;
        border: 1px solid #EDF2F7;
    }
    
    .color-item:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    
    .color-select {
        background: #FFFFFF;
        border: 1px solid #CBD5E0;
        border-radius: 6px;
        padding: 8px 12px;
        color: #2D3748;
        font-weight: 500;
        margin-bottom: 10px;
        width: 100%;
    }
    
    .color-select:focus {
        outline: none;
        border-color: #4A5568;
        box-shadow: 0 0 0 2px rgba(74, 85, 104, 0.2);
    }
    
    .color-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 10px;
    }
    
    .input-group-sm {
        display: flex;
        flex-direction: column;
    }
    
    .input-group-sm label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4A5568;
        margin-bottom: 4px;
    }
    
    .input-group-sm input {
        border: 1px solid #CBD5E0;
        border-radius: 4px;
        padding: 6px 8px;
        font-size: 0.875rem;
    }
    
    .input-group-sm input:focus {
        outline: none;
        border-color: #4A5568;
        box-shadow: 0 0 0 2px rgba(74, 85, 104, 0.2);
    }
    
    .color-remove-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #E53E3E;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.2s ease;
    }
    
    .color-remove-btn:hover {
        background: #C53030;
    }
    
    .add-color-btn {
        background: #38A169;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        width: 100%;
    }
    
    .add-color-btn:hover {
        background: #2F855A;
    }
    
    .add-size-btn {
        background: #4A5568;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
        margin-top: 20px;
    }
    
    .add-size-btn:hover {
        background: #2D3748;
        box-shadow: 0 3px 6px rgba(0,0,0,0.15);
    }
</style>
@endsection

@section('scripts')
<script>
    // إضافة meta tag للـ CSRF token
    document.head.insertAdjacentHTML('beforeend', '<meta name="csrf-token" content="{{ csrf_token() }}">');
    let newImageCount = 1;

    // المتغيرات العامة للمقاسات والألوان
    let selectedSizes = [];
    let availableSizes = [];
    let availableColors = [];
    let inventoryRows = [];
    let inventoryRowCounter = 0;

    // ملء البيانات الموجودة من قاعدة البيانات
    @if(isset($selectedSizes) && count($selectedSizes) > 0)
        // تحضير البيانات للمقاسات والألوان الموجودة
        console.log('🔍 Loading existing inventory data...');
        @foreach($product->inventory->groupBy('size_id') as $sizeId => $sizeInventories)
            console.log('Processing size group: {{ $sizeId }}');
            selectedSizes.push({
                id: {{ $sizeId }},
                name: '{{ $sizeInventories->first()->size->name ?? "" }}',
                colors: [
                    @foreach($sizeInventories as $inventory)
                    @if($inventory->color_id)
                    {
                        id: {{ $inventory->color_id }},
                        name: '{{ $inventory->color->name ?? "" }}',
                        stock: {{ $inventory->stock ?? 0 }},
                        price: {{ $inventory->price ?? 0 }}
                    },
                    @endif
                    @endforeach
                ]
            });
        @endforeach
        console.log('✅ Loaded selectedSizes:', selectedSizes);
    @else
        console.log('⚠️ No existing inventory data found');
    @endif

    // تحميل المقاسات والألوان المتاحة
    @if(isset($availableSizes))
        availableSizes = @json($availableSizes);
    @endif
    
    @if(isset($availableColors))
        availableColors = @json($availableColors);
    @endif

    // Function to generate slug from product name
    function generateSlug(name) {
        // Convert to lowercase and replace spaces with hyphens
        let slug = name.toLowerCase().trim().replace(/\s+/g, '-');
        // Remove special characters
        slug = slug.replace(/[^\u0621-\u064A\u0660-\u0669a-z0-9-]/g, '');
        // Replace multiple hyphens with a single one
        slug = slug.replace(/-+/g, '-');
        return slug;
    }

    // Add event listener to name field to auto-generate slug
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="name"]');
        const slugInput = document.querySelector('input[name="slug"]');

        if (nameInput && slugInput) {
            nameInput.addEventListener('input', function() {
                slugInput.value = generateSlug(this.value);
            });
        }
    });

    function toggleColorsSection(checkbox) {
        const colorsSection = document.getElementById('colorsSection');
        const enableColorSelection = document.getElementById('enable_color_selection');

        if (checkbox.checked) {
            colorsSection.classList.remove('section-collapsed');
            colorsSection.classList.add('section-expanded');
            if (enableColorSelection) {
                enableColorSelection.checked = true;
            }
            if (!document.querySelector('#colorsContainer .input-group')) {
                addColorInput();
            }
        } else {
            colorsSection.classList.remove('section-expanded');
            colorsSection.classList.add('section-collapsed');
            if (enableColorSelection) {
                enableColorSelection.checked = false;
            }
        }
    }

    function toggleSizesSection(checkbox) {
        const sizesSection = document.getElementById('sizesSection');
        const enableSizeSelection = document.getElementById('enable_size_selection');

        if (checkbox.checked) {
            sizesSection.classList.remove('section-collapsed');
            sizesSection.classList.add('section-expanded');
            if (enableSizeSelection) {
                enableSizeSelection.checked = true;
            }
            if (!document.querySelector('#sizesContainer .input-group')) {
                addSizeInput();
            }
        } else {
            sizesSection.classList.remove('section-expanded');
            sizesSection.classList.add('section-collapsed');
            if (enableSizeSelection) {
                enableSizeSelection.checked = false;
            }
        }
    }

    function addNewImageInput() {
        const container = document.getElementById('newImagesContainer');
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.innerHTML = `
        <div class="input-group shadow-sm">
            <input type="file" name="new_images[]" class="form-control" accept="image/*">
            <div class="input-group-text">
                <label class="mb-0">
                    <input type="radio" name="is_primary_new[${newImageCount}]" value="1" class="me-1">
                    صورة رئيسية
                </label>
            </div>
            <button type="button" class="btn btn-light-danger" onclick="this.closest('.mb-2').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
        container.appendChild(div);
        newImageCount++;
    }

    function addColorInput() {
        const container = document.getElementById('colorsContainer');
        const div = document.createElement('div');
        div.className = 'input-group mb-2 shadow-sm';
        div.innerHTML = `
        <input type="hidden" name="color_ids[]" value="">
        <input type="text" name="colors[]" class="form-control" placeholder="اسم اللون">
        <div class="input-group-text">
            <label class="mb-0">
                <input type="checkbox" name="color_available[]" value="1" checked class="me-1">
                متوفر
            </label>
        </div>
        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
        container.appendChild(div);
    }

    function addSizeInput() {
        const container = document.getElementById('sizesContainer');
        const div = document.createElement('div');
        div.className = 'input-group mb-2 shadow-sm';
        div.innerHTML = `
        <input type="hidden" name="size_ids[]" value="">
        <input type="text" name="sizes[]" class="form-control" placeholder="المقاس">
        <input type="number" name="size_prices[]" class="form-control" placeholder="السعر" step="0.01">
        <div class="input-group-text">
            <label class="mb-0">
                <input type="checkbox" name="size_available[]" value="1" checked class="me-1">
                متوفر
            </label>
        </div>
        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
        container.appendChild(div);
    }

    function addDetailInput() {
        const container = document.getElementById('detailsContainer');
        const div = document.createElement('div');
        div.className = 'input-group mb-2 shadow-sm';
        div.innerHTML = `
        <input type="text" name="detail_keys[]" class="form-control" placeholder="الخاصية">
        <input type="text" name="detail_values[]" class="form-control" placeholder="القيمة">
        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
        container.appendChild(div);
    }

    // Initialize the page when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Check if details container is empty and add one input if needed
        if (document.querySelectorAll('#detailsContainer .input-group').length === 0) {
            addDetailInput();
        }
    });

    // ===== إدارة المقاسات والألوان =====

    // دالة توليد slug
    function generateSlug(name) {
        let slug = name.toLowerCase().trim().replace(/\s+/g, '-');
        slug = slug.replace(/[^\u0621-\u064A\u0660-\u0669a-z0-9-]/g, '');
        slug = slug.replace(/-+/g, '-');
        return slug;
    }

    // تحميل البيانات عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        try {
            console.log('DOM loaded, setting up form...');
            
            // تهيئة المتغيرات العامة
            if (typeof selectedSizes === 'undefined') {
                selectedSizes = [];
                console.log('Initialized selectedSizes array');
            }
            
            if (typeof availableSizes === 'undefined') {
                availableSizes = [];
                console.log('Initialized availableSizes array');
            }
            
            if (typeof availableColors === 'undefined') {
                availableColors = [];
                console.log('Initialized availableColors array');
            }
            
            // إضافة مستمع حدث لزر إضافة مقاس جديد
            const addSizeButton = document.getElementById('addSizeButton');
            if (addSizeButton) {
                addSizeButton.addEventListener('click', function() {
                    console.log('Add size button clicked');
                    addNewSize();
                });
            } else {
                console.error('Add size button not found');
            }
            
            // إعداد حقل اسم المنتج لتوليد slug تلقائياً
            const nameInput = document.querySelector('input[name="name"]');
            const slugInput = document.querySelector('input[name="slug"]');

            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    slugInput.value = generateSlug(this.value);
                });
            }
            
            // إعداد حقل المخزون للتأكد من القيمة الصحيحة
            const mainStockInput = document.querySelector('input[name="stock"]');
            if (mainStockInput) {
                const initialValue = parseInt(mainStockInput.value) || 0;
                mainStockInput.value = Math.max(0, initialValue);
                console.log('Stock input initialized with value:', mainStockInput.value);
                
                mainStockInput.addEventListener('blur', function() {
                    const value = parseInt(this.value) || 0;
                    this.value = Math.max(0, value);
                    console.log('Stock input blur event - value set to:', this.value);
                });
                
                mainStockInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    console.log('Stock input input event - value:', this.value);
                });
            }
        } catch (error) {
            console.error('Error in DOMContentLoaded event:', error);
        }
        
        // تحميل المقاسات المتاحة من البيانات المرسلة من الخادم
        @if(isset($availableSizes))
            availableSizes = @json($availableSizes);
        @endif
        
        // تحميل الألوان المتاحة من البيانات المرسلة من الخادم
        @if(isset($availableColors) && $availableColors->count() > 0)
            availableColors = @json($availableColors);
        @else
            availableColors = [];
        @endif
        
        console.log('Available sizes:', availableSizes);
        console.log('Available colors:', availableColors);
        
        // تحميل البيانات الموجودة من inventoryMap
        @if(isset($inventoryMap) && $inventoryMap->count() > 0)
            console.log('Loading existing inventory data...');
            const inventoryData = @json($inventoryMap);
            console.log('Inventory data:', inventoryData);
            loadExistingInventoryData(inventoryData);
        @endif
        
        // دالة تحميل البيانات الموجودة
        function loadExistingInventoryData(inventoryData) {
            console.log('🔍 [DEBUG] Loading existing inventory data:', inventoryData);
            
            if (!inventoryData || inventoryData.length === 0) {
                console.log('No existing inventory data to load');
                return;
            }
            
            // تجميع البيانات حسب المقاسات
            const sizeGroups = {};
            inventoryData.forEach(item => {
                if (!sizeGroups[item.size_id]) {
                    sizeGroups[item.size_id] = {
                        id: item.size_id,
                        name: item.size_name || 'مقاس غير محدد',
                        colors: []
                    };
                }
                
                if (item.color_id) {
                    sizeGroups[item.size_id].colors.push({
                        id: item.color_id,
                        name: item.color_name || 'لون غير محدد',
                        stock: item.stock || 0,
                        price: item.price || ''
                    });
                }
            });
            
            // تحويل البيانات إلى مصفوفة
            selectedSizes = Object.values(sizeGroups);
            console.log('Processed existing data:', selectedSizes);
            
            // تحديث الواجهة
            updateSizeColorMatrix();
        }
        
        // إظهار تفاصيل أكثر في console للمساعدة في التصحيح
        if (availableSizes.length === 0) {
            console.warn('⚠️ لا توجد مقاسات متاحة في قاعدة البيانات');
        } else {
            console.log('✅ المقاسات المتاحة:', availableSizes.map(s => `${s.name} (ID: ${s.id})`));
        }
        
        if (availableColors.length === 0) {
            console.warn('⚠️ لا توجد ألوان متاحة في قاعدة البيانات');
        } else {
            console.log('✅ الألوان المتاحة:', availableColors.map(c => `${c.name} (ID: ${c.id})`));
        }
        
        // إضافة مقاس افتراضي عند تحميل الصفحة إذا لم تكن هناك مقاسات
        @if(!isset($inventoryMap) || $inventoryMap->count() === 0)
        if (selectedSizes.length === 0) {
            addNewSize();
        }
        @endif
        
        // إظهار رسالة للمستخدم إذا لم تكن هناك مقاسات أو ألوان متاحة
        if (availableSizes.length === 0 || availableColors.length === 0) {
            const message = [];
            if (availableSizes.length === 0) {
                message.push('مقاسات');
            }
            if (availableColors.length === 0) {
                message.push('ألوان');
            }
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-info mt-3';
            alertDiv.innerHTML = `
                <i class="fas fa-info-circle me-2"></i>
                <strong>معلومات:</strong> لا توجد ${message.join(' و ')} متاحة في قاعدة البيانات. 
                سيتم إنشاء ${message.join(' و ')} افتراضية تلقائياً.
            `;
            
            const container = document.querySelector('.container-fluid') || document.querySelector('.container');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
            }
            
            // إضافة رسالة في console للمساعدة في التصحيح
            console.log(`🔍 تم إضافة رسالة للمستخدم: لا توجد ${message.join(' و ')} متاحة`);
        }
        
        // إذا لم تكن هناك مقاسات متاحة، أضف مقاس افتراضي
        if (availableSizes.length === 0) {
            console.log('No available sizes, adding default size');
            addNewSize();
        }
        
        // إذا لم تكن هناك ألوان متاحة، أضف لون افتراضي للمقاس الأول
        if (availableColors.length === 0 && selectedSizes.length > 0) {
            console.log('No available colors, adding default color to first size');
            const firstSize = selectedSizes[0];
            if (firstSize && (!firstSize.colors || firstSize.colors.length === 0)) {
                addColorToSize(firstSize.id);
            }
        }
        
        // التحقق من وجود مقاسات وألوان متاحة
        if (availableSizes.length === 0) {
            console.warn('لا توجد مقاسات متاحة');
            // إظهار رسالة للمستخدم
            const sizeWarning = document.createElement('div');
            sizeWarning.className = 'alert alert-warning mt-3';
            sizeWarning.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> لا توجد مقاسات متاحة. سيتم إنشاء مقاسات افتراضية.';
            const sizeColorDetails = document.querySelector('#sizeColorDetails');
            if (sizeColorDetails) {
                sizeColorDetails.prepend(sizeWarning);
            }
        }
        
        if (availableColors.length === 0) {
            console.warn('لا توجد ألوان متاحة');
            // إظهار رسالة للمستخدم
            const colorWarning = document.createElement('div');
            colorWarning.className = 'alert alert-warning mt-3';
            colorWarning.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> لا توجد ألوان متاحة. سيتم إنشاء ألوان افتراضية.';
            const sizeColorDetails = document.querySelector('#sizeColorDetails');
            if (sizeColorDetails) {
                sizeColorDetails.prepend(colorWarning);
            }
        }
        
        // إضافة رسالة في أعلى الصفحة إذا لم تكن هناك مقاسات أو ألوان متاحة
        if (availableSizes.length === 0 || availableColors.length === 0) {
            const message = [];
            if (availableSizes.length === 0) {
                message.push('مقاسات');
            }
            if (availableColors.length === 0) {
                message.push('ألوان');
            }
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>تنبيه:</strong> لا توجد ${message.join(' و ')} متاحة في قاعدة البيانات. 
                سيتم إنشاء ${message.join(' و ')} افتراضية تلقائياً.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container-fluid') || document.querySelector('.container');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
            }
            
            // إضافة رسالة في console للمساعدة في التصحيح
            console.log(`🔍 تم إضافة رسالة تنبيه للمستخدم: لا توجد ${message.join(' و ')} متاحة`);
        }
        
        // إضافة رسالة في القوائم المنسدلة إذا لم تكن هناك خيارات
        setTimeout(() => {
            const sizeSelects = document.querySelectorAll('.size-select');
            const colorSelects = document.querySelectorAll('.color-select');
            
            console.log('Found size selects:', sizeSelects.length);
            console.log('Found color selects:', colorSelects.length);
            
            if (availableSizes.length === 0) {
                sizeSelects.forEach(select => {
                    if (select.options.length <= 1) { // فقط "اختر المقاس..."
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'لا توجد مقاسات متاحة';
                        option.disabled = true;
                        select.appendChild(option);
                        console.log('Added "no sizes" option to size select');
                    }
                });
            }
            
            if (availableColors.length === 0) {
                colorSelects.forEach(select => {
                    if (select.options.length <= 1) { // فقط "اختر اللون..."
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'لا توجد ألوان متاحة';
                        option.disabled = true;
                        select.appendChild(option);
                        console.log('Added "no colors" option to color select');
                    }
                });
            }
        }, 200);
        
        // إضافة رسالة في console للمساعدة في التصحيح
        console.log('🔍 تم إضافة معالجة للقوائم المنسدلة الفارغة');
        
        // التأكد من أن حقل المخزون يحتوي على قيمة صحيحة عند التحميل
        const stockInput2 = document.querySelector('input[name="stock"]');
        if (stockInput2) {
            const currentValue = parseInt(stockInput2.value) || 0;
            stockInput2.value = Math.max(0, currentValue);
            console.log('Stock input initialized with value:', stockInput2.value);
        }
    });

    // تحديث مصفوفة المقاسات والألوان - التصميم المبسط والهادئ
    function updateSizeColorMatrix() {
        try {
            const matrixContainer = document.getElementById('sizeColorMatrix');
            if (!matrixContainer) {
                console.error('Size color matrix container not found');
                return;
            }
            
            // التأكد من وجود مصفوفة المقاسات
            if (!selectedSizes || !Array.isArray(selectedSizes)) {
                console.warn('selectedSizes is not an array, initializing it');
                selectedSizes = [];
                return;
            }
            
            console.log('Updating size color matrix with', selectedSizes.length, 'sizes');
            
            // حفظ القيم المدخلة قبل إعادة التحديث وتحديث البيانات في selectedSizes
            const stockInputs = matrixContainer.querySelectorAll('input[name*="stock"]');
            const priceInputs = matrixContainer.querySelectorAll('input[name*="price"]');
            
            console.log('Saving current values before refresh...');
            
            // حفظ قيم المخزون في selectedSizes - تحسين البحث
            stockInputs.forEach(input => {
                const matches = input.name.match(/stock\[([^\]]+)\]\[([^\]]+)\]/);
                if (matches) {
                    const sizeId = matches[1];
                    const colorId = matches[2];
                    const value = input.value;
                    
                    console.log(`Trying to save stock: ${sizeId}-${colorId} = ${value}`);
                    
                    // العثور على المقاس واللون في selectedSizes - تحسين البحث
                    let size = selectedSizes.find(s => String(s.id) === String(sizeId));
                    
                    // إذا لم نجد المقاس، جرب البحث بالطرق البديلة
                    if (!size) {
                        size = selectedSizes.find(s => s.id == sizeId);
                    }
                    
                    if (size && size.colors) {
                        let color = size.colors.find(c => String(c.id) === String(colorId));
                        
                        // إذا لم نجد اللون، جرب البحث بالطرق البديلة
                        if (!color) {
                            color = size.colors.find(c => c.id == colorId);
                        }
                        
                        if (color) {
                            color.stock = value;
                            console.log(`✅ Saved stock: ${sizeId}-${colorId} = ${value}`);
                        } else {
                            console.warn(`❌ Color not found: ${colorId} in size: ${sizeId}`);
                            console.log('Available colors in this size:', size.colors.map(c => c.id));
                        }
                    } else {
                        console.warn(`❌ Size not found: ${sizeId}`);
                        console.log('Available sizes:', selectedSizes.map(s => s.id));
                    }
                }
            });
            
            // حفظ قيم الأسعار في selectedSizes - تحسين البحث
            priceInputs.forEach(input => {
                const matches = input.name.match(/price\[([^\]]+)\]\[([^\]]+)\]/);
                if (matches) {
                    const sizeId = matches[1];
                    const colorId = matches[2];
                    const value = input.value;
                    
                    console.log(`Trying to save price: ${sizeId}-${colorId} = ${value}`);
                    
                    // العثور على المقاس واللون في selectedSizes - تحسين البحث
                    let size = selectedSizes.find(s => String(s.id) === String(sizeId));
                    
                    // إذا لم نجد المقاس، جرب البحث بالطرق البديلة
                    if (!size) {
                        size = selectedSizes.find(s => s.id == sizeId);
                    }
                    
                    if (size && size.colors) {
                        let color = size.colors.find(c => String(c.id) === String(colorId));
                        
                        // إذا لم نجد اللون، جرب البحث بالطرق البديلة
                        if (!color) {
                            color = size.colors.find(c => c.id == colorId);
                        }
                        
                        if (color) {
                            color.price = value;
                            console.log(`✅ Saved price: ${sizeId}-${colorId} = ${value}`);
                        } else {
                            console.warn(`❌ Color not found: ${colorId} in size: ${sizeId}`);
                            console.log('Available colors in this size:', size.colors.map(c => c.id));
                        }
                    } else {
                        console.warn(`❌ Size not found: ${sizeId}`);
                        console.log('Available sizes:', selectedSizes.map(s => s.id));
                    }
                }
            });
            
            matrixContainer.innerHTML = '';
        
        // إنشاء مستطيلات المقاسات
        selectedSizes.forEach((size, sizeIndex) => {
            const sizeContainer = document.createElement('div');
            sizeContainer.className = 'size-container active';
            sizeContainer.dataset.sizeId = size.id;
            
            // الحصول على الألوان المختارة لهذا المقاس
            const selectedColors = size.colors || [];
            
            sizeContainer.innerHTML = `
                <div class="size-header">
                    <div class="size-title">
                        <i class="fas fa-ruler me-2"></i>
                        المقاس ${sizeIndex + 1}
                        <span class="size-number">${sizeIndex + 1}</span>
                    </div>
                    <button type="button" class="size-remove-btn" onclick="removeSizeFromCard(${sizeIndex})">
                        <i class="fas fa-times me-1"></i>
                        حذف
                    </button>
                </div>
                
                <select class="size-select" onchange="updateSizeName(${sizeIndex}, this.value)">
                    <option value="">اختر المقاس...</option>
                    ${availableSizes.map(s => `
                        <option value="${s.id}" ${s.id == size.id ? 'selected' : ''}>
                            ${s.name} ${s.description ? '- ' + s.description : ''}
                        </option>
                    `).join('')}
                </select>
                
                <div class="colors-section" id="colors-section-${size.id}">
                    <h6 class="mb-3 fw-bold">
                        <i class="fas fa-palette me-2"></i>
                        الألوان المتاحة
                    </h6>
                    <div class="size-colors-container" id="size-colors-${size.id}">
                        ${selectedColors.map(color => `
                            <div class="color-item" data-color-id="${color.id}">
                                <select class="color-select" onchange="updateColorName(this, '${size.id}')">
                                    <option value="">اختر اللون...</option>
                                    ${availableColors.map(c => `
                                        <option value="${c.id}" data-hex="${c.code || '#4A5568'}" ${c.id == color.id ? 'selected' : ''}>
                                            ${c.name} ${c.description ? '- ' + c.description : ''}
                                        </option>
                                    `).join('')}
                                </select>
                                
                                <div class="color-inputs">
                                    <div class="input-group-sm">
                                        <label>عدد القطع:</label>
                                        <input type="number" 
                                            name="stock[${size.id}][${color.id}]" 
                                            placeholder="50"
                                            min="0"
                                            value="${color.stock || ''}"
                                            required>
                                    </div>
                                    <div class="input-group-sm">
                                        <label>السعر (ر.س):</label>
                                        <input type="number" 
                                            name="price[${size.id}][${color.id}]" 
                                            placeholder="150"
                                            step="0.01"
                                            min="0"
                                            value="${color.price || ''}">
                                    </div>
                                </div>
                                
                                <button type="button" class="color-remove-btn" onclick="removeColorFromSize('${size.id}', '${color.id}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                    
                    <button type="button" class="add-color-btn mt-2" onclick="addColorToSize('${size.id}')">
                        <i class="fas fa-plus me-2"></i>
                        إضافة لون
                    </button>
                </div>
            `;
            
            matrixContainer.appendChild(sizeContainer);
            
            // إضافة event listeners للحقول الجديدة
            const newStockInputs = sizeContainer.querySelectorAll('input[name*="stock"]');
            const newPriceInputs = sizeContainer.querySelectorAll('input[name*="price"]');
            
            newStockInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const name = this.name;
                    const matches = name.match(/stock\[([^\]]+)\]\[([^\]]+)\]/);
                    if (matches) {
                        const sizeId = matches[1];
                        const colorId = matches[2];
                        const size = selectedSizes.find(s => String(s.id) === String(sizeId));
                        if (size && size.colors) {
                            const color = size.colors.find(c => String(c.id) === String(colorId));
                            if (color) {
                                color.stock = this.value;
                            }
                        }
                    }
                });
            });
            
            newPriceInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const name = this.name;
                    const matches = name.match(/price\[([^\]]+)\]\[([^\]]+)\]/);
                    if (matches) {
                        const sizeId = matches[1];
                        const colorId = matches[2];
                        const size = selectedSizes.find(s => String(s.id) === String(sizeId));
                        if (size && size.colors) {
                            const color = size.colors.find(c => String(c.id) === String(colorId));
                            if (color) {
                                color.price = this.value;
                            }
                        }
                    }
                });
            });
        });
        
        } catch (error) {
            console.error('Error in updateSizeColorMatrix:', error);
            alert('حدث خطأ أثناء تحديث مصفوفة المقاسات والألوان: ' + error.message);
        }
    }

    // إضافة مقاس جديد
    function addNewSize() {
        try {
            console.log('Adding new size...');
            let newSize;
            
            // إذا كانت هناك مقاسات متاحة، استخدم أول مقاس
            if (availableSizes && availableSizes.length > 0) {
                const firstSize = availableSizes[0];
                newSize = {
                    id: firstSize.id,
                    name: firstSize.name,
                    colors: [] // مصفوفة فارغة للألوان
                };
                console.log('Using available size:', firstSize);
            } else {
                // إذا لم تكن هناك مقاسات متاحة، أنشئ مقاس مؤقت
                newSize = {
                    id: 'temp_' + Date.now(),
                    name: 'مقاس جديد',
                    colors: [] // مصفوفة فارغة للألوان
                };
                console.log('Created temporary size');
            }
            
            // التأكد من أن مصفوفة المقاسات المختارة موجودة
            if (!selectedSizes) {
                selectedSizes = [];
                console.log('Initialized selectedSizes array');
            }
            
            selectedSizes.push(newSize);
            console.log('New size added:', newSize);
            console.log('Total sizes:', selectedSizes.length);
            
            // تحديث المصفوفة في واجهة المستخدم
            updateSizeColorMatrix();
            
            return true;
        } catch (error) {
            console.error('Error in addNewSize:', error);
            alert('حدث خطأ أثناء إضافة مقاس جديد: ' + error.message);
            return false;
        }
    }

    // تحديث اسم المقاس
    function updateSizeName(sizeIndex, sizeId) {
        if (sizeId) {
            // التحقق من عدم تكرار المقاس
            const existingSize = selectedSizes.find((size, index) => 
                index !== sizeIndex && size.id == sizeId
            );
            
            if (existingSize) {
                alert('هذا المقاس موجود بالفعل في منتج آخر');
                // إعادة تعيين القيمة المختارة
                const selectElement = event.target;
                selectElement.value = selectedSizes[sizeIndex].id || '';
                return;
            }
            
            const sizeOption = document.querySelector(`option[value="${sizeId}"]`);
            if (sizeOption) {
                // حفظ الألوان الموجودة مع بياناتها
                const existingColors = selectedSizes[sizeIndex].colors || [];
                
                selectedSizes[sizeIndex].id = sizeId;
                selectedSizes[sizeIndex].name = sizeOption.textContent;
                
                // استعادة الألوان مع بياناتها
                selectedSizes[sizeIndex].colors = existingColors;
                
                // تحديث المصفوفة مع الحفاظ على البيانات المدخلة
                updateSizeColorMatrix();
            }
        }
    }

    // حذف مقاس من البطاقة
    function removeSizeFromCard(sizeIndex) {
        if (confirm('هل أنت متأكد من حذف هذا المقاس؟')) {
            selectedSizes.splice(sizeIndex, 1);
            updateSizeColorMatrix();
        }
    }

    // إضافة لون لمقاس معين - التصميم الجديد
    function addColorToSize(sizeId) {
        console.log('Adding color to size:', sizeId);
        console.log('Available sizes:', selectedSizes);
        
        // البحث عن المقاس المحدد بطرق مختلفة
        let size = null;
        
        // الطريقة 1: البحث المباشر بالـ ID
        size = selectedSizes.find(s => s.id === sizeId);
        if (size) {
            console.log('Found size by direct ID match:', size);
        }
        
        // الطريقة 2: البحث بالـ string comparison
        if (!size) {
            size = selectedSizes.find(s => String(s.id) === String(sizeId));
            if (size) {
                console.log('Found size by string comparison:', size);
            }
        }
        
        // الطريقة 3: البحث بالـ index إذا كان sizeId رقم
        if (!size) {
            const sizeIndex = parseInt(sizeId);
            if (!isNaN(sizeIndex) && sizeIndex >= 0 && sizeIndex < selectedSizes.length) {
                size = selectedSizes[sizeIndex];
                console.log('Found size by index:', sizeIndex, size);
            }
        }
        
        // الطريقة 4: البحث في المقاسات المؤقتة إذا كان sizeId يحتوي على 'temp_'
        if (!size && String(sizeId).includes('temp_')) {
            size = selectedSizes.find(s => s.id && s.id.toString().includes('temp_'));
            if (size) {
                console.log('Found temp size:', size);
            }
        }
        
        // الطريقة 5: البحث في آخر مقاس تم إضافته إذا كان sizeId يحتوي على 'temp_'
        if (!size && String(sizeId).includes('temp_') && selectedSizes.length > 0) {
            size = selectedSizes[selectedSizes.length - 1];
            console.log('Using last added size:', size);
        }
        
        // إذا لم يتم العثور على المقاس، ابحث في جميع المقاسات
        if (!size) {
            for (let i = 0; i < selectedSizes.length; i++) {
                const currentSize = selectedSizes[i];
                if (String(currentSize.id) === String(sizeId)) {
                    size = currentSize;
                    console.log('Found size by comprehensive search at index:', i, size);
                    break;
                }
            }
        }
        
        // إذا لم يتم العثور على المقاس نهائياً، استخدم أول مقاس متاح
        if (!size && selectedSizes.length > 0) {
            size = selectedSizes[0];
            console.log('Using first available size:', size);
        }
        
        // إذا لم يتم العثور على المقاس نهائياً
        if (!size) {
            console.error('Size not found:', sizeId);
            console.error('Available sizes:', selectedSizes.map(s => ({ id: s.id, name: s.name })));
            alert('خطأ: لم يتم العثور على المقاس المحدد. يرجى إعادة المحاولة.');
            return;
        }
        
        // التأكد من وجود مصفوفة الألوان
        if (!size.colors) {
            size.colors = [];
        }
        
        let newColor;
        
        // إنشاء لون مؤقت مع ID فريد
        const tempColorId = 'temp_' + Date.now();
        newColor = {
            id: tempColorId,
            name: '',
            stock: '',
            price: ''
        };
        
        size.colors.push(newColor);
        
        // بدلاً من إعادة إنشاء كل شيء، أضف اللون الجديد فقط
        addColorToUI(size, newColor);
        
        console.log('Color added successfully to size:', size.id, 'Total colors:', size.colors.length);
    }
    
    // دالة إضافة اللون للواجهة فقط (بدون إعادة إنشاء كل شيء) - نسخة مبسطة
    function addColorToUI(size, color) {
        console.log('Adding color to UI for size:', size.id, 'color:', color);
        
        // البحث عن الحاوية بطريقة مبسطة وموثوقة
        let colorsContainer = null;
        
        // الطريقة 1: البحث بالـ ID المباشر
        colorsContainer = document.querySelector(`#size-colors-${size.id}`);
        console.log('Method 1 - Direct ID search:', colorsContainer ? 'Found' : 'Not found');
        
        // الطريقة 2: البحث في جميع الحاويات
        if (!colorsContainer) {
            const allContainers = document.querySelectorAll('.size-colors-container');
            console.log('Found', allContainers.length, 'color containers');
            
            for (let container of allContainers) {
                const sizeContainer = container.closest('.size-container');
                if (sizeContainer) {
                    const containerSizeId = sizeContainer.dataset.sizeId;
                    console.log('Container size ID:', containerSizeId, 'Looking for:', size.id);
                    
                    if (String(containerSizeId) === String(size.id)) {
                        colorsContainer = container;
                        console.log('Found matching container!');
                        break;
                    }
                }
            }
        }
        
        // الطريقة 3: البحث في جميع المقاسات
        if (!colorsContainer) {
            const allSizeContainers = document.querySelectorAll('.size-container');
            console.log('Found', allSizeContainers.length, 'size containers');
            
            for (let sizeContainer of allSizeContainers) {
                const containerSizeId = sizeContainer.dataset.sizeId;
                console.log('Size container ID:', containerSizeId, 'Looking for:', size.id);
                
                if (String(containerSizeId) === String(size.id)) {
                    const container = sizeContainer.querySelector('.size-colors-container');
                    if (container) {
                        colorsContainer = container;
                        console.log('Found container in size container!');
                        break;
                    }
                }
            }
        }
        
        // إذا لم نجد الحاوية، استخدم الحل البديل
        if (!colorsContainer) {
            console.error('Colors container not found for size:', size.id);
            console.log('Available size containers:', Array.from(document.querySelectorAll('.size-container')).map(c => c.dataset.sizeId));
            console.log('Available color containers:', Array.from(document.querySelectorAll('.size-colors-container')).length);
            
            // إعادة إنشاء المصفوفة كاملة كحل بديل
            console.log('Falling back to full matrix update');
            updateSizeColorMatrix();
            return;
        }
        
        console.log('Found colors container:', colorsContainer);
        
        // إنشاء عنصر اللون الجديد
        const colorItem = document.createElement('div');
        colorItem.className = 'color-item';
        colorItem.dataset.colorId = color.id;
        
        colorItem.innerHTML = `
            <select class="color-select" onchange="updateColorName(this, '${size.id}')">
                <option value="">اختر اللون...</option>
                ${availableColors.map(c => `
                    <option value="${c.id}" data-hex="${c.code || '#4A5568'}" ${c.id == color.id ? 'selected' : ''}>
                        ${c.name} ${c.description ? '- ' + c.description : ''}
                    </option>
                `).join('')}
            </select>
            
            <div class="color-inputs">
                <div class="input-group-sm">
                    <label>عدد القطع:</label>
                    <input type="number" 
                        name="stock[${size.id}][${color.id}]" 
                        placeholder="50"
                        min="0"
                        value="${color.stock || ''}"
                        required>
                </div>
                <div class="input-group-sm">
                    <label>السعر (ر.س):</label>
                    <input type="number" 
                        name="price[${size.id}][${color.id}]" 
                        placeholder="150"
                        step="0.01"
                        min="0"
                        value="${color.price || ''}">
                </div>
            </div>
            
            <button type="button" class="color-remove-btn" onclick="removeColorFromSize('${size.id}', '${color.id}')">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // إضافة العنصر للحاوية
        colorsContainer.appendChild(colorItem);
        console.log('Color item added successfully to container');
        
        // إضافة event listeners للحقول الجديدة
        const stockInput = colorItem.querySelector('input[name*="stock"]');
        const priceInput = colorItem.querySelector('input[name*="price"]');
        
        if (stockInput) {
            stockInput.addEventListener('input', function() {
                color.stock = this.value;
                console.log(`Updated stock for ${size.id}-${color.id}: ${this.value}`);
            });
        }
        
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                color.price = this.value;
                console.log(`Updated price for ${size.id}-${color.id}: ${this.value}`);
            });
        }
        
        console.log('Color added successfully to UI');
    }

    // تحديث اسم اللون
    function updateColorName(selectElement, sizeId) {
        const colorItem = selectElement.closest('.color-item');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption.value) {
            const colorId = selectedOption.value;
            const colorName = selectedOption.textContent;
            const colorHex = selectedOption.dataset.hex;
            
            // العثور على المقاس المحدد - البحث بالـ ID أو بالـ index
            let size = selectedSizes.find(s => s.id === sizeId);
            
            // إذا لم يتم العثور على المقاس، جرب البحث بالـ index
            if (!size) {
                const sizeIndex = parseInt(sizeId);
                if (!isNaN(sizeIndex) && sizeIndex >= 0 && sizeIndex < selectedSizes.length) {
                    size = selectedSizes[sizeIndex];
                }
            }
            
            // إذا لم يتم العثور على المقاس، جرب البحث بالـ string comparison
            if (!size) {
                size = selectedSizes.find(s => String(s.id) === String(sizeId));
            }
            
            // إذا لم يتم العثور على المقاس، جرب البحث في المقاسات المؤقتة
            if (!size && String(sizeId).includes('temp_')) {
                size = selectedSizes.find(s => s.id && s.id.toString().includes('temp_'));
            }
            
            // إذا لم يتم العثور على المقاس، استخدم آخر مقاس تم إضافته
            if (!size && selectedSizes.length > 0) {
                size = selectedSizes[selectedSizes.length - 1];
            }
            
            if (!size || !size.colors) {
                console.error('Size not found:', sizeId);
                console.error('Available sizes:', selectedSizes.map(s => ({ id: s.id, name: s.name })));
                alert('خطأ: لم يتم العثور على المقاس. يرجى إعادة تحميل الصفحة.');
                return;
            }
            
            // العثور على اللون المحدد
            const colorIndex = size.colors.findIndex(c => c.id === colorItem.dataset.colorId);
            if (colorIndex === -1) {
                return;
            }
            
            // التحقق من عدم تكرار اللون في نفس المقاس
            const existingColor = size.colors.find((c, index) => 
                index !== colorIndex && c.id == colorId
            );
            
            if (existingColor) {
                alert('هذا اللون موجود بالفعل في هذا المقاس');
                selectElement.value = size.colors[colorIndex].id || '';
                return;
            }
            
            // تحديث بيانات اللون
            size.colors[colorIndex].id = colorId;
            size.colors[colorIndex].name = colorName;
            
            // تحديث data-color-id
            colorItem.dataset.colorId = colorId;
            
            // تحديث أسماء الحقول مع الحفاظ على القيم المدخلة
            const colorStockInput = colorItem.querySelector('input[name*="stock"]');
            const priceInput = colorItem.querySelector('input[name*="price"]');
            
            // حفظ القيم الحالية
            const currentStockValue = colorStockInput ? colorStockInput.value : '';
            const currentPriceValue = priceInput ? priceInput.value : '';
            
            if (colorStockInput) {
                colorStockInput.name = `stock[${sizeId}][${colorId}]`;
                // إزالة event listeners القديمة لتجنب التكرار
                colorStockInput.replaceWith(colorStockInput.cloneNode(true));
                const newColorStockInput = colorItem.querySelector('input[name*="stock"]');
                newColorStockInput.value = currentStockValue;
                newColorStockInput.addEventListener('input', function() {
                    size.colors[colorIndex].stock = this.value;
                });
            }
            if (priceInput) {
                priceInput.name = `price[${sizeId}][${colorId}]`;
                // إزالة event listeners القديمة لتجنب التكرار
                priceInput.replaceWith(priceInput.cloneNode(true));
                const newPriceInput = colorItem.querySelector('input[name*="price"]');
                newPriceInput.value = currentPriceValue;
                newPriceInput.addEventListener('input', function() {
                    size.colors[colorIndex].price = this.value;
                });
            }
        }
    }

    // حذف لون من مقاس معين
    function removeColorFromSize(sizeId, colorId) {
        if (confirm('هل أنت متأكد من حذف هذا اللون؟')) {
            // البحث عن المقاس
            let size = selectedSizes.find(s => s.id === sizeId);
            
            if (!size) {
                const sizeIndex = parseInt(sizeId);
                if (!isNaN(sizeIndex) && sizeIndex >= 0 && sizeIndex < selectedSizes.length) {
                    size = selectedSizes[sizeIndex];
                }
            }
            
            if (!size) {
                size = selectedSizes.find(s => String(s.id) === String(sizeId));
            }
            
            if (size && size.colors) {
                // حذف اللون من المصفوفة
                const colorIndex = size.colors.findIndex(c => c.id === colorId);
                if (colorIndex !== -1) {
                    size.colors.splice(colorIndex, 1);
                    updateSizeColorMatrix();
                    console.log('Color removed from size:', sizeId, 'Color:', colorId);
                }
            }
        }
    }

    // إضافة event listener للنموذج عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="products"]');
        if (form) {
            form.addEventListener('submit', function(event) {
                // منع إرسال النموذج بشكل افتراضي
                event.preventDefault();
                
                // حفظ بيانات المخزون والأسعار المدخلة مباشرة من الحقول
                const stockPriceData = {};
                const colorItems = document.querySelectorAll('.color-item');
                colorItems.forEach(colorItem => {
                    const stockInput = colorItem.querySelector('input[placeholder="50"]');
                    const priceInput = colorItem.querySelector('input[placeholder="150"]');
                    const colorSelect = colorItem.querySelector('.color-select');
                    const sizeContainer = colorItem.closest('.size-container');
                    const sizeSelect = sizeContainer ? sizeContainer.querySelector('.size-select') : null;
                    
                    if (stockInput && priceInput && colorSelect && colorSelect.value && sizeSelect && sizeSelect.value) {
                        const sizeId = sizeSelect.value;
                        const colorId = colorSelect.value;
                        const stockValue = stockInput.value || '0';
                        const priceValue = priceInput.value || '';
                        
                        if (!stockPriceData[sizeId]) stockPriceData[sizeId] = {};
                        stockPriceData[sizeId][colorId] = {
                            stock: stockValue,
                            price: priceValue
                        };
                        
                        console.log(`Saved input values: size=${sizeId}, color=${colorId}, stock=${stockValue}, price=${priceValue}`);
                    }
                });
                
                // إظهار رسالة تحميل مع مؤشر تقدم
                const loadingAlert = document.createElement('div');
                loadingAlert.className = 'alert alert-info position-fixed top-0 start-50 translate-middle-x mt-4';
                loadingAlert.style.zIndex = '9999';
                loadingAlert.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <div>جاري حفظ التغييرات...</div>
                    </div>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                `;
                document.body.appendChild(loadingAlert);
                
                // تحديث شريط التقدم
                const progressBar = loadingAlert.querySelector('.progress-bar');
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    if (progress > 90) progress = 90;
                    progressBar.style.width = progress + '%';
                }, 300);
                
                // التحقق من صحة البيانات
                const result = handleSaveClickInternal();
                
                if (!result) {
                    loadingAlert.remove();
                    clearInterval(progressInterval);
                    return false;
                }
                
                // إعداد البيانات للمقاسات والألوان
                prepareFormDataForLaravel();
                
                // استعادة بيانات المخزون والأسعار المحفوظة بالشكل الجديد
                for (const sizeId in stockPriceData) {
                    for (const colorId in stockPriceData[sizeId]) {
                        const data = stockPriceData[sizeId][colorId];
                        
                        // إضافة أو تحديث حقل المخزون بالشكل الجديد
                        let stockInput = form.querySelector(`input[name="stock[${sizeId}][${colorId}]"]`);
                        if (!stockInput) {
                            stockInput = document.createElement('input');
                            stockInput.type = 'hidden';
                            stockInput.name = `stock[${sizeId}][${colorId}]`;
                            form.appendChild(stockInput);
                        }
                        stockInput.value = data.stock;
                        
                        // إضافة أو تحديث حقل السعر بالشكل الجديد
                        let priceInput = form.querySelector(`input[name="price[${sizeId}][${colorId}]"]`);
                        if (!priceInput) {
                            priceInput = document.createElement('input');
                            priceInput.type = 'hidden';
                            priceInput.name = `price[${sizeId}][${colorId}]`;
                            form.appendChild(priceInput);
                        }
                        priceInput.value = data.price;
                        
                        console.log(`Restored data: stock[${sizeId}][${colorId}]=${data.stock}, price[${sizeId}][${colorId}]=${data.price}`);
                    }
                }
                
                // جمع بيانات النموذج
                const formData = new FormData(form);
                
                // إضافة طريقة PUT للطلب
                formData.append('_method', 'PUT');
                
                // طباعة بيانات النموذج للتحقق
                console.log('Form data being sent:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
                // إرسال البيانات باستخدام AJAX
                fetch(form.action, {
                    method: 'POST', // دائماً نستخدم POST مع _method=PUT لتجنب مشاكل المتصفحات
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin' // للتأكد من إرسال ملفات تعريف الجلسة
                })
                .then(response => {
                    // إيقاف تحديث شريط التقدم
                    clearInterval(progressInterval);
                    
                    // تحديث شريط التقدم إلى 100%
                    progressBar.style.width = '100%';
                    progressBar.classList.remove('progress-bar-animated');
                    
                    if (!response.ok) {
                        if (response.status === 422) {
                            // أخطاء التحقق
                            return response.json().then(data => {
                                throw new Error(Object.values(data.errors).flat().join('\n'));
                            });
                        }
                        throw new Error('حدث خطأ أثناء حفظ البيانات (رمز الخطأ: ' + response.status + ')');
                    }
                    
                    return response.text();
                })
                .then(data => {
                    // إزالة رسالة التحميل
                    loadingAlert.remove();
                    
                    // إظهار رسالة نجاح
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-4';
                    successAlert.style.zIndex = '9999';
                    successAlert.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>تم حفظ التغييرات بنجاح</div>
                        </div>
                    `;
                    document.body.appendChild(successAlert);
                    
                    // إزالة الرسالة بعد ثانيتين
                    setTimeout(() => {
                        successAlert.remove();
                        // إعادة توجيه المستخدم إلى صفحة المنتجات
                        window.location.href = '{{ route("admin.products.index") }}';
                    }, 2000);
                })
                .catch(error => {
                    // إيقاف تحديث شريط التقدم وإزالة رسالة التحميل
                    clearInterval(progressInterval);
                    loadingAlert.remove();
                    
                    // إظهار رسالة خطأ
                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-4';
                    errorAlert.style.zIndex = '9999';
                    errorAlert.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>حدث خطأ أثناء حفظ البيانات: ${error.message}</div>
                        </div>
                    `;
                    document.body.appendChild(errorAlert);
                    
                    // إظهار القيم المحفوظة للتحقق
                    console.log('Saved stock/price data that was not sent:', stockPriceData);
                    
                    // إزالة الرسالة بعد 5 ثوان
                    setTimeout(() => {
                        errorAlert.remove();
                    }, 5000);
                    
                    console.error('Error:', error);
                });
            });
        }
    });
    
    function handleSaveClickInternal() {
        // البحث عن النموذج
        const form = document.querySelector('form[action*="products"]');
        
        if (!form) {
            console.error('Form not found!');
            alert('خطأ: لم يتم العثور على النموذج');
            return false;
        }
        
        // التحقق من البيانات الأساسية
        const nameInput = form.querySelector('input[name="name"]');
        const categoryInput = form.querySelector('select[name="category_id"]');
        const descriptionInput = form.querySelector('textarea[name="description"]');
        const stockInput = form.querySelector('input[name="stock"]');
        
        // التحقق من اسم المنتج
        if (!nameInput || !nameInput.value || !nameInput.value.trim()) {
            alert('يرجى إدخال اسم المنتج');
            if (nameInput) nameInput.focus();
            return false;
        }
        
        // التحقق من التصنيف
        if (!categoryInput || !categoryInput.value) {
            alert('يرجى اختيار التصنيف الرئيسي');
            if (categoryInput) categoryInput.focus();
            return false;
        }
        
        // التحقق من الوصف
        if (!descriptionInput || !descriptionInput.value.trim()) {
            alert('يرجى إدخال وصف المنتج');
            if (descriptionInput) descriptionInput.focus();
            return false;
        }
        
        // التحقق من المخزون
        if (!stockInput) {
            alert('خطأ: لم يتم العثور على حقل المخزون');
            return false;
        }
        
        const stockValue = parseInt(stockInput.value) || 0;
        if (stockValue < 0) {
            alert('يرجى إدخال قيمة صحيحة للمخزون (0 أو أكثر)');
            stockInput.focus();
            return false;
        }
        
        stockInput.value = Math.max(0, stockValue);
        console.log('Stock value updated to:', stockInput.value);
        
        console.log('Form validation passed');
        return true;
    }
    
    // دالة إعداد البيانات للمقاسات والألوان
    function prepareFormData(form) {
        console.log('🔍 [DEBUG] Preparing form data for sizes and colors...');
        
        // إزالة جميع الحقول المخفية المتعلقة بالمقاسات والألوان
        const oldInputs = form.querySelectorAll('input[name^="selected_sizes"], input[name^="selected_colors"], input[name^="stock["], input[name^="price["]');
        oldInputs.forEach(input => {
            console.log('Removing old input:', input.name, input.value);
            input.remove();
        });
        
        // جمع البيانات من DOM مباشرة - تحسين البحث
        const sizeContainers = document.querySelectorAll('.size-container');
        const collectedSizes = new Set();
        const collectedColors = new Set();
        const collectedStockData = {};
        const collectedPriceData = {};
        
        console.log('Found size containers:', sizeContainers.length);
        
        sizeContainers.forEach((container, index) => {
            const sizeSelect = container.querySelector('.size-select');
            if (sizeSelect && sizeSelect.value) {
                const sizeId = sizeSelect.value;
                collectedSizes.add(sizeId);
                console.log(`Processing size ${index + 1}:`, sizeId);
                
                // البحث عن الألوان بطرق مختلفة
                let colorItems = container.querySelectorAll('.color-item');
                
                // إذا لم نجد color-item، جرب البحث في size-colors-container
                if (colorItems.length === 0) {
                    const colorsContainer = container.querySelector('.size-colors-container');
                    if (colorsContainer) {
                        colorItems = colorsContainer.querySelectorAll('.color-item');
                        console.log(`Found ${colorItems.length} colors in size-colors-container`);
                    }
                }
                
                // إذا لم نجد color-item، جرب البحث في colors-section
                if (colorItems.length === 0) {
                    const colorsSection = container.querySelector('.colors-section');
                    if (colorsSection) {
                        colorItems = colorsSection.querySelectorAll('.color-item');
                        console.log(`Found ${colorItems.length} colors in colors-section`);
                    }
                }
                
                // إذا لم نجد color-item، جرب البحث في جميع العناصر التي تحتوي على color-select
                if (colorItems.length === 0) {
                    colorItems = container.querySelectorAll('[class*="color"]');
                    console.log(`Found ${colorItems.length} color-related elements`);
                }
                
                console.log(`Processing ${colorItems.length} color items for size ${sizeId}`);
                
                colorItems.forEach((colorItem, colorIndex) => {
                    const colorSelect = colorItem.querySelector('.color-select');
                    if (colorSelect && colorSelect.value) {
                        const colorId = colorSelect.value;
                        collectedColors.add(colorId);
                        console.log(`Found color ${colorIndex + 1}: ${colorId}`);
                        
                        // جمع بيانات المخزون والسعر
                        const stockInput = colorItem.querySelector('input[name*="stock"]');
                        const priceInput = colorItem.querySelector('input[name*="price"]');
                        
                        if (stockInput && stockInput.value) {
                            if (!collectedStockData[sizeId]) collectedStockData[sizeId] = {};
                            collectedStockData[sizeId][colorId] = stockInput.value;
                            console.log(`Collected stock: ${sizeId}-${colorId} = ${stockInput.value}`);
                        } else {
                            console.warn(`No stock value found for size ${sizeId}, color ${colorId}`);
                        }
                        
                        if (priceInput && priceInput.value) {
                            if (!collectedPriceData[sizeId]) collectedPriceData[sizeId] = {};
                            collectedPriceData[sizeId][colorId] = priceInput.value;
                            console.log(`Collected price: ${sizeId}-${colorId} = ${priceInput.value}`);
                        } else {
                            console.warn(`No price value found for size ${sizeId}, color ${colorId}`);
                        }
                    } else {
                        console.warn(`Color select not found or empty in color item ${colorIndex + 1}`);
                    }
                });
            } else {
                console.warn(`Size select not found or empty in container ${index + 1}`);
            }
        });
        
        // إضافة المقاسات المختارة
        Array.from(collectedSizes).forEach(sizeId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_sizes[]';
            input.value = sizeId;
            form.appendChild(input);
            console.log('Added size input:', sizeId);
        });
        
        // إضافة الألوان المختارة
        Array.from(collectedColors).forEach(colorId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_colors[]';
            input.value = colorId;
            form.appendChild(input);
            console.log('Added color input:', colorId);
        });
        
        // إضافة بيانات المخزون
        Object.keys(collectedStockData).forEach(sizeId => {
            Object.keys(collectedStockData[sizeId]).forEach(colorId => {
                const stockValue = collectedStockData[sizeId][colorId];
                const priceValue = (collectedPriceData[sizeId] && collectedPriceData[sizeId][colorId]) ? collectedPriceData[sizeId][colorId] : '';
                        
                        // إضافة المخزون
                        const stockInput = document.createElement('input');
                        stockInput.type = 'hidden';
                        stockInput.name = `stock[${sizeId}][${colorId}]`;
                        stockInput.value = stockValue;
                        form.appendChild(stockInput);
                console.log(`Added stock input: stock[${sizeId}][${colorId}] = ${stockValue}`);
                        
                // إضافة السعر إذا كان موجود
                if (priceValue) {
                        const priceInput = document.createElement('input');
                        priceInput.type = 'hidden';
                        priceInput.name = `price[${sizeId}][${colorId}]`;
                        priceInput.value = priceValue;
                        form.appendChild(priceInput);
                    console.log(`Added price input: price[${sizeId}][${colorId}] = ${priceValue}`);
                }
            });
        });
        
        // التحقق من البيانات النهائية
        const finalSizes = form.querySelectorAll('input[name="selected_sizes[]"]');
        const finalColors = form.querySelectorAll('input[name="selected_colors[]"]');
        const finalStock = form.querySelectorAll('input[name*="stock["]');
        const finalPrice = form.querySelectorAll('input[name*="price["]');
        
        console.log('🔍 [DEBUG] Final form data summary:');
        console.log('- Sizes:', finalSizes.length);
        console.log('- Colors:', finalColors.length);
        console.log('- Stock fields:', finalStock.length);
        console.log('- Price fields:', finalPrice.length);
        
        // طباعة تفاصيل البيانات
        finalSizes.forEach(input => console.log('Size:', input.value));
        finalColors.forEach(input => console.log('Color:', input.value));
        finalStock.forEach(input => console.log('Stock field:', input.name, '=', input.value));
        finalPrice.forEach(input => console.log('Price field:', input.name, '=', input.value));
        
        console.log('✅ Form data prepared successfully');
        
        // التأكد من أن حقل المخزون يحتوي على قيمة صحيحة
        const stockInput = form.querySelector('input[name="stock"]');
        if (stockInput) {
            const stockValue = parseInt(stockInput.value) || 0;
            stockInput.value = Math.max(0, stockValue);
            console.log('Stock value set to:', stockInput.value);
        }
        
        // إضافة CSRF token للطلبات AJAX
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : null;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }
        
        console.log('Form data prepared successfully');
        return true;
    }

    // دالة تحميل البيانات الموجودة من جدول product_size_color_inventory
    function loadExistingInventoryData(inventoryData) {
        console.log('loadExistingInventoryData called with:', inventoryData);
        
        if (!inventoryData || inventoryData.length === 0) {
            console.log('No existing inventory data to load');
            return;
        }
        
        // إعادة تعيين المصفوفة
        selectedSizes = [];
        
        // تجميع البيانات حسب المقاس
        const groupedData = {};
        inventoryData.forEach(item => {
            const sizeId = item.size_id;
            const colorId = item.color_id;
            
            if (!groupedData[sizeId]) {
                // البحث عن اسم المقاس
                const sizeInfo = availableSizes.find(s => s.id == sizeId);
                groupedData[sizeId] = {
                    id: sizeId,
                    name: sizeInfo ? sizeInfo.name : `Size ${sizeId}`,
                    colors: []
                };
            }
            
            // البحث عن اسم اللون
            const colorInfo = availableColors.find(c => c.id == colorId);
            const colorData = {
                id: colorId,
                name: colorInfo ? colorInfo.name : `Color ${colorId}`,
                stock: item.stock || 0,
                price: item.price || ''
            };
            
            groupedData[sizeId].colors.push(colorData);
        });
        
        // تحويل البيانات المجمعة إلى مصفوفة selectedSizes
        selectedSizes = Object.values(groupedData);
        
        console.log('Loaded selectedSizes:', selectedSizes);
        
        // تحديث واجهة المستخدم
        updateSizeColorMatrix();
    }

    // دالة تشخيص البيانات
    function debugFormData() {
        console.log('=== تشخيص البيانات ===');
        console.log('selectedSizes:', selectedSizes);
        console.log('availableSizes:', availableSizes);
        console.log('availableColors:', availableColors);
        
        const form = document.querySelector('form');
        if (form) {
            // استخراج البيانات من DOM مباشرة
            const sizeContainers = document.querySelectorAll('.size-container');
            const sizesInDOM = [];
            const colorsInDOM = [];
            
            console.log('Size containers found:', sizeContainers.length);
            
            sizeContainers.forEach((container, index) => {
                const sizeSelect = container.querySelector('.size-select');
                if (sizeSelect) {
                    const sizeId = sizeSelect.value;
                    const sizeName = (sizeSelect.options[sizeSelect.selectedIndex] && sizeSelect.options[sizeSelect.selectedIndex].text) ? sizeSelect.options[sizeSelect.selectedIndex].text : 'غير محدد';
                    
                    console.log(`Size ${index+1}: ID=${sizeId}, Name=${sizeName}`);
                    
                    if (sizeId) {
                        sizesInDOM.push({
                            id: sizeId,
                            name: sizeName
                        });
                        
                        // البحث عن الألوان في هذا المقاس
                        const colorItems = container.querySelectorAll('.color-item');
                        console.log(`- Colors for size ${sizeName}: ${colorItems.length} items`);
                        
                        colorItems.forEach((colorItem, colorIndex) => {
                            const colorSelect = colorItem.querySelector('.color-select');
                            if (colorSelect) {
                                const colorId = colorSelect.value;
                                const colorName = (colorSelect.options[colorSelect.selectedIndex] && colorSelect.options[colorSelect.selectedIndex].text) ? colorSelect.options[colorSelect.selectedIndex].text : 'غير محدد';
                                
                                if (colorId) {
                                    const stockInput = colorItem.querySelector('input[name*="stock"]') || 
                                                      colorItem.querySelector('input[placeholder="50"]');
                                    const priceInput = colorItem.querySelector('input[name*="price"]') || 
                                                      colorItem.querySelector('input[placeholder="150"]');
                                    
                                    const stockValue = stockInput ? stockInput.value : '0';
                                    const priceValue = priceInput ? priceInput.value : '';
                                    
                                    console.log(`  - Color ${colorIndex+1}: ID=${colorId}, Name=${colorName}, Stock=${stockValue}, Price=${priceValue}`);
                                    
                                    colorsInDOM.push({
                                        id: colorId,
                                        name: colorName,
                                        sizeId: sizeId,
                                        stock: stockValue,
                                        price: priceValue
                                    });
                                }
                            }
                        });
                    }
                }
            });
            
            // التحقق من الحقول الخفية المضافة للنموذج
            const selectedSizesInputs = form.querySelectorAll('input[name="selected_sizes[]"]');
            const selectedColorsInputs = form.querySelectorAll('input[name="selected_colors[]"]');
            const stockInputs = form.querySelectorAll('input[name*="stock["]');
            
            console.log('Hidden inputs found:');
            console.log('- selected_sizes[]:', selectedSizesInputs.length);
            console.log('- selected_colors[]:', selectedColorsInputs.length);
            console.log('- stock inputs:', stockInputs.length);
            
            if (selectedSizesInputs.length > 0) {
                console.log('Size input values:', Array.from(selectedSizesInputs).map(i => i.value));
            }
            
            if (selectedColorsInputs.length > 0) {
                console.log('Color input values:', Array.from(selectedColorsInputs).map(i => i.value));
            }
            
            if (stockInputs.length > 0) {
                console.log('Sample stock fields:', Array.from(stockInputs).slice(0, 3).map(i => `${i.name}=${i.value}`));
            }
            
            // المقاسات والألوان التي تم تحديدها في الذاكرة
            const validSizesCount = selectedSizes.filter(size => size.id && !String(size.id).includes('temp_')).length;
            const allColorsCount = selectedSizes.reduce((count, size) => {
                if (size.colors && Array.isArray(size.colors)) {
                    count += size.colors.filter(color => color.id && !String(color.id).includes('temp_')).length;
                }
                return count;
            }, 0);
            
            // إظهار النتائج للمستخدم
            alert(`
تشخيص النموذج:
- المقاسات في الذاكرة: ${selectedSizes.length} (${validSizesCount} مقاس صالح)
- المقاسات في واجهة المستخدم: ${sizesInDOM.length}
- مجموع الألوان المستخدمة في الذاكرة: ${allColorsCount}
- الألوان في واجهة المستخدم: ${colorsInDOM.length}
- حقول selected_sizes: ${selectedSizesInputs.length}
- حقول selected_colors: ${selectedColorsInputs.length}
- حقول stock: ${stockInputs.length}

راجع Console للتفاصيل الكاملة
            `);
            
            // التصحيح التلقائي إذا كان هناك مشكلة
            if (selectedSizesInputs.length === 0 || selectedColorsInputs.length === 0 || stockInputs.length === 0) {
                console.warn('تم اكتشاف مشكلة في البيانات. جاري تحضير البيانات تلقائياً...');
                
                // إعادة بناء هيكل البيانات من DOM إذا كانت متوفرة
                if (sizesInDOM.length > 0 && colorsInDOM.length > 0) {
                    console.log('Rebuilding selectedSizes from DOM data');
                    
                    // إعادة بناء هيكل البيانات
                    selectedSizes = sizesInDOM.map(size => {
                        return {
                            id: size.id,
                            name: size.name,
                            colors: colorsInDOM
                                .filter(color => color.sizeId === size.id)
                                .map(color => ({
                                    id: color.id,
                                    name: color.name,
                                    stock: color.stock || '0',
                                    price: color.price || ''
                                }))
                        };
                    });
                    
                    console.log('Rebuilt selectedSizes:', selectedSizes);
                    updateSizeColorMatrix(); // تحديث واجهة المستخدم
                }
                
                // تجهيز البيانات
                prepareFormData(form);
                
                // التحقق مرة أخرى
                const fixedSizesInputs = form.querySelectorAll('input[name="selected_sizes[]"]');
                const fixedColorsInputs = form.querySelectorAll('input[name="selected_colors[]"]');
                const fixedStockInputs = form.querySelectorAll('input[name*="stock["]');
                
                console.log('بعد التصحيح:');
                console.log('- selected_sizes[]:', fixedSizesInputs.length);
                console.log('- selected_colors[]:', fixedColorsInputs.length);
                console.log('- stock inputs:', fixedStockInputs.length);
                
                if (fixedSizesInputs.length > 0) {
                    console.log('Fixed size values:', Array.from(fixedSizesInputs).map(i => i.value));
                }
                
                if (fixedColorsInputs.length > 0) {
                    console.log('Fixed color values:', Array.from(fixedColorsInputs).map(i => i.value));
                }
                
                alert(`
تم تصحيح البيانات:
- حقول selected_sizes: ${fixedSizesInputs.length}
- حقول selected_colors: ${fixedColorsInputs.length}
- حقول stock: ${fixedStockInputs.length}

تم إضافة البيانات المفقودة. جرب حفظ المنتج الآن.
                `);
                
                // إذا لم تكن البيانات كاملة بعد، أضف بيانات افتراضية
                if (fixedSizesInputs.length === 0 || fixedColorsInputs.length === 0 || fixedStockInputs.length === 0) {
                    console.warn('Still missing data after fix. Adding emergency fallback data');
                    
                    // إضافة مقاس افتراضي إذا لم يكن هناك
                    if (fixedSizesInputs.length === 0 && availableSizes && availableSizes.length > 0) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_sizes[]';
                        input.value = availableSizes[0].id;
                        form.appendChild(input);
                        console.log('Added emergency size input:', availableSizes[0].id);
                    }
                    
                    // إضافة لون افتراضي إذا لم يكن هناك
                    if (fixedColorsInputs.length === 0 && availableColors && availableColors.length > 0) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_colors[]';
                        input.value = availableColors[0].id;
                        form.appendChild(input);
                        console.log('Added emergency color input:', availableColors[0].id);
                    }
                    
                    // إضافة مخزون افتراضي إذا لم يكن هناك
                    if (fixedStockInputs.length === 0 && fixedSizesInputs.length > 0 && fixedColorsInputs.length > 0) {
                        const sizeId = fixedSizesInputs[0].value;
                        const colorId = fixedColorsInputs[0].value;
                        
                        const stockInput = document.createElement('input');
                        stockInput.type = 'hidden';
                        stockInput.name = `stock[${sizeId}][${colorId}]`;
                        stockInput.value = 10;
                        form.appendChild(stockInput);
                        console.log(`Added emergency stock input: stock[${sizeId}][${colorId}] = 10`);
                        
                        const priceInput = document.createElement('input');
                        priceInput.type = 'hidden';
                        priceInput.name = `price[${sizeId}][${colorId}]`;
                        const basePriceInput = document.querySelector('input[name="base_price"]');
                        priceInput.value = basePriceInput ? basePriceInput.value : '';
                        form.appendChild(priceInput);
                    }
                }
            } else {
                console.log('جميع البيانات المطلوبة موجودة!');
            }
        }
    }

        // دالة إعداد البيانات قبل الإرسال
        function prepareFormData() {
            console.log('🔍 [DEBUG] Preparing form data...');
            console.log('Selected sizes:', selectedSizes);
            
            const form = document.getElementById('product-form');
            if (!form) {
                console.error('Form not found!');
                return false;
            }
            
            // إزالة البيانات القديمة
            const oldInputs = form.querySelectorAll('.dynamic-field');
            oldInputs.forEach(input => {
                console.log('Removing old input:', input.name, input.value);
                input.remove();
            });
            
            // جمع البيانات من DOM مباشرة - تحسين البحث
            const sizeContainers = document.querySelectorAll('.size-container');
            const collectedSizes = new Set();
            const collectedColors = new Set();
            const collectedStockData = {};
            const collectedPriceData = {};
            
            console.log('Found size containers:', sizeContainers.length);
            
            sizeContainers.forEach((container, index) => {
                const sizeSelect = container.querySelector('.size-select');
                if (sizeSelect && sizeSelect.value) {
                    const sizeId = sizeSelect.value;
                    collectedSizes.add(sizeId);
                    console.log(`Processing size ${index + 1}:`, sizeId);
                    
                    // البحث عن الألوان بطرق مختلفة
                    let colorItems = container.querySelectorAll('.color-item');
                    
                    // إذا لم نجد color-item، جرب البحث في size-colors-container
                    if (colorItems.length === 0) {
                        const colorsContainer = container.querySelector('.size-colors-container');
                        if (colorsContainer) {
                            colorItems = colorsContainer.querySelectorAll('.color-item');
                            console.log(`Found ${colorItems.length} colors in size-colors-container`);
                        }
                    }
                    
                    // إذا لم نجد color-item، جرب البحث في colors-section
                    if (colorItems.length === 0) {
                        const colorsSection = container.querySelector('.colors-section');
                        if (colorsSection) {
                            colorItems = colorsSection.querySelectorAll('.color-item');
                            console.log(`Found ${colorItems.length} colors in colors-section`);
                        }
                    }
                    
                    // إذا لم نجد color-item، جرب البحث في جميع العناصر التي تحتوي على color-select
                    if (colorItems.length === 0) {
                        colorItems = container.querySelectorAll('[class*="color"]');
                        console.log(`Found ${colorItems.length} color-related elements`);
                    }
                    
                    console.log(`Processing ${colorItems.length} color items for size ${sizeId}`);
                    
                    colorItems.forEach((colorItem, colorIndex) => {
                        const colorSelect = colorItem.querySelector('.color-select');
                        if (colorSelect && colorSelect.value) {
                            const colorId = colorSelect.value;
                            collectedColors.add(colorId);
                            console.log(`Found color ${colorIndex + 1}: ${colorId}`);
                            
                            // جمع بيانات المخزون والسعر
                            const stockInput = colorItem.querySelector('input[name*="stock"]');
                            const priceInput = colorItem.querySelector('input[name*="price"]');
                            
                            if (stockInput && stockInput.value) {
                                if (!collectedStockData[sizeId]) collectedStockData[sizeId] = {};
                                collectedStockData[sizeId][colorId] = stockInput.value;
                                console.log(`Collected stock: ${sizeId}-${colorId} = ${stockInput.value}`);
                            } else {
                                console.warn(`No stock value found for size ${sizeId}, color ${colorId}`);
                            }
                            
                            if (priceInput && priceInput.value) {
                                if (!collectedPriceData[sizeId]) collectedPriceData[sizeId] = {};
                                collectedPriceData[sizeId][colorId] = priceInput.value;
                                console.log(`Collected price: ${sizeId}-${colorId} = ${priceInput.value}`);
                            } else {
                                console.warn(`No price value found for size ${sizeId}, color ${colorId}`);
                            }
                        } else {
                            console.warn(`Color select not found or empty in color item ${colorIndex + 1}`);
                        }
                    });
                } else {
                    console.warn(`Size select not found or empty in container ${index + 1}`);
                }
            });
            
            // إضافة المقاسات المختارة
            Array.from(collectedSizes).forEach(sizeId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_sizes[]';
                input.value = sizeId;
                input.classList.add('dynamic-field');
                form.appendChild(input);
                console.log('Added size input:', sizeId);
            });
            
            // إضافة الألوان المختارة - التنسيق الصحيح للـ Controller
            Array.from(collectedColors).forEach(colorId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_colors[]';
                input.value = colorId;
                input.classList.add('dynamic-field');
                form.appendChild(input);
                console.log('Added color input:', colorId);
            });
            
            // إضافة بيانات المخزون
            Object.keys(collectedStockData).forEach(sizeId => {
                Object.keys(collectedStockData[sizeId]).forEach(colorId => {
                    const stockValue = collectedStockData[sizeId][colorId];
                    const priceValue = (collectedPriceData[sizeId] && collectedPriceData[sizeId][colorId]) ? collectedPriceData[sizeId][colorId] : '';
                    
                    // إضافة المخزون
                    const stockInput = document.createElement('input');
                    stockInput.type = 'hidden';
                    stockInput.name = `stock[${sizeId}][${colorId}]`;
                    stockInput.value = stockValue;
                    stockInput.classList.add('dynamic-field');
                    form.appendChild(stockInput);
                    console.log(`Added stock input: stock[${sizeId}][${colorId}] = ${stockValue}`);
                    
                    // إضافة السعر إذا كان موجود
                    if (priceValue) {
                        const priceInput = document.createElement('input');
                        priceInput.type = 'hidden';
                        priceInput.name = `price[${sizeId}][${colorId}]`;
                        priceInput.value = priceValue;
                        priceInput.classList.add('dynamic-field');
                        form.appendChild(priceInput);
                        console.log(`Added price input: price[${sizeId}][${colorId}] = ${priceValue}`);
                    }
                });
            });
            
            // التحقق من البيانات النهائية
            const finalSizes = form.querySelectorAll('input[name="selected_sizes[]"]');
            const finalColors = form.querySelectorAll('input[name="selected_colors[]"]');
            const finalStock = form.querySelectorAll('input[name*="stock["]');
            const finalPrice = form.querySelectorAll('input[name*="price["]');
            
            console.log('🔍 [DEBUG] Final form data summary:');
            console.log('- Sizes:', finalSizes.length);
            console.log('- Colors:', finalColors.length);
            console.log('- Stock fields:', finalStock.length);
            console.log('- Price fields:', finalPrice.length);
            
            // طباعة تفاصيل البيانات
            finalSizes.forEach(input => console.log('Size:', input.value));
            finalColors.forEach(input => console.log('Color:', input.value));
            finalStock.forEach(input => console.log('Stock field:', input.name, '=', input.value));
            finalPrice.forEach(input => console.log('Price field:', input.name, '=', input.value));
            
            console.log('✅ Form data prepared successfully');
            return true;
        }
        
        // إضافة event listener للنموذج عند التحميل - تم إزالتها لتجنب التضارب
        // document.addEventListener('DOMContentLoaded', function() {
        //     const form = document.querySelector('form');
        //     if (form) {
        //         form.addEventListener('submit', function(e) {
        //             console.log('Form submit detected, preparing data...');
        //             const success = prepareFormData();
        //             if (!success) {
        //                 e.preventDefault();
        //                 alert('حدث خطأ في إعداد البيانات. يرجى المحاولة مرة أخرى.');
        //                 return false;
        //             }
        //             console.log('Form data prepared, submitting...');
        //         });
        //     }
        // });

        // الكود الجديد لإعداد البيانات بالشكل المطلوب لـ Laravel
        function prepareFormDataForLaravel() {
            console.log('🔍 [DEBUG] prepareFormDataForLaravel called');
            console.log('selectedSizes:', selectedSizes);
            
            // امسح أي hidden inputs قديمة
            document.querySelectorAll(".dynamic-hidden").forEach(el => el.remove());

            let form = document.getElementById("product-form");
            if (!form) {
                console.error('Form not found!');
                return;
            }

            let totalSizes = 0;
            let totalColors = 0;
            let totalStock = 0;
            let totalPrice = 0;

            // إضافة البيانات بالشكل المتوافق مع Controller
            selectedSizes.forEach(size => {
                console.log(`Processing size: ${size.id} - ${size.name}`);
                totalSizes++;
                
                // hidden input للمقاس
                let sizeInput = document.createElement("input");
                sizeInput.type = "hidden";
                sizeInput.name = "selected_sizes[]";
                sizeInput.value = size.id;
                sizeInput.classList.add("dynamic-hidden");
                form.appendChild(sizeInput);
                console.log(`Added size input: ${size.id}`);

                // loop على الألوان
                if (size.colors && Array.isArray(size.colors)) {
                    size.colors.forEach(color => {
                        console.log(`Processing color: ${color.id} - ${color.name}`);
                        totalColors++;
                        
                        // hidden input للون - التنسيق الصحيح للـ Controller
                        let colorInput = document.createElement("input");
                        colorInput.type = "hidden";
                        colorInput.name = "selected_colors[]";
                        colorInput.value = color.id;
                        colorInput.classList.add("dynamic-hidden");
                        form.appendChild(colorInput);
                        console.log(`Added color input: ${color.id}`);

                        // إضافة البيانات بالشكل المتوافق مع Controller
                        // stock[size_id][color_id] - الشكل المطلوب للـ Controller
                        let stockInput = document.createElement("input");
                        stockInput.type = "hidden";
                        stockInput.name = `stock[${size.id}][${color.id}]`;
                        stockInput.value = color.stock || 0;
                        stockInput.classList.add("dynamic-hidden");
                        form.appendChild(stockInput);
                        totalStock++;
                        console.log(`Added stock input: stock[${size.id}][${color.id}] = ${color.stock || 0}`);

                        // price[size_id][color_id] - الشكل المطلوب للـ Controller
                        let priceInput = document.createElement("input");
                        priceInput.type = "hidden";
                        priceInput.name = `price[${size.id}][${color.id}]`;
                        priceInput.value = color.price || 0;
                        priceInput.classList.add("dynamic-hidden");
                        form.appendChild(priceInput);
                        totalPrice++;
                        console.log(`Added price input: price[${size.id}][${color.id}] = ${color.price || 0}`);
                    });
                } else {
                    console.warn(`No colors array for size: ${size.id}`);
                }
            });
            
            console.log(`✅ prepareFormDataForLaravel completed:`);
            console.log(`- Sizes added: ${totalSizes}`);
            console.log(`- Colors added: ${totalColors}`);
            console.log(`- Stock fields added: ${totalStock}`);
            console.log(`- Price fields added: ${totalPrice}`);
        }

        // اربطها قبل السبميت
        document.getElementById("product-form").addEventListener("submit", function(e) {
            console.log('🔍 Form submit event triggered');
            console.log('Preparing form data for Laravel...');
            prepareFormDataForLaravel();
            
            // تشخيص إضافي - عدد الحقول المُنشأة
            let hiddenInputs = document.querySelectorAll('.dynamic-hidden');
            console.log(`🔍 Total hidden inputs created: ${hiddenInputs.length}`);
            
            // عرض تفاصيل الحقول
            hiddenInputs.forEach((input, index) => {
                console.log(`Input ${index + 1}: ${input.name} = ${input.value}`);
            });
            
            console.log('✅ Form data prepared, submitting form...');
        });

        // دالة لملء البيانات الموجودة عند تحميل الصفحة
        function populateExistingData() {
            console.log('Populating existing data...');
            console.log('Selected sizes:', selectedSizes);
            
            if (selectedSizes.length > 0) {
                // تحديث المصفوفة في واجهة المستخدم
                updateSizeColorMatrix();
                
                // إضافة رسالة تأكيد
                console.log('✅ Data populated successfully');
                console.log('Total sizes loaded:', selectedSizes.length);
                selectedSizes.forEach((size, index) => {
                    console.log(`Size ${index + 1}:`, size.name, 'Colors:', size.colors.length);
                });
            } else {
                console.log('No existing data to populate');
            }
        }

        // تشغيل دالة ملء البيانات عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔍 DOMContentLoaded event fired');
            console.log('Initial selectedSizes:', selectedSizes);
            
            // تأخير قليل للتأكد من تحميل جميع العناصر
            setTimeout(function() {
                console.log('🔍 Calling populateExistingData after timeout');
                console.log('selectedSizes before populate:', selectedSizes);
                populateExistingData();
            }, 500);
        });

        // دالة تحديث مصفوفة المقاسات والألوان (مشابهة لصفحة الإنشاء)
        function updateSizeColorMatrix() {
            try {
                const matrixContainer = document.getElementById('sizeColorMatrix');
                if (!matrixContainer) {
                    console.error('Size color matrix container not found');
                    return;
                }
                
                // حفظ القيم المدخلة قبل إعادة التحديث
                const stockInputs = matrixContainer.querySelectorAll('input[name*="stock"]');
                const priceInputs = matrixContainer.querySelectorAll('input[name*="price"]');
                
                console.log('Saving current values before refresh...');
                
                // حفظ قيم المخزون في selectedSizes
                stockInputs.forEach(input => {
                    const matches = input.name.match(/stock\[([^\]]+)\]\[([^\]]+)\]/);
                    if (matches) {
                        const sizeId = matches[1];
                        const colorId = matches[2];
                        const value = input.value;
                        
                        console.log(`Trying to save stock: ${sizeId}-${colorId} = ${value}`);
                        
                        // العثور على المقاس واللون في selectedSizes
                        let size = selectedSizes.find(s => String(s.id) === String(sizeId));
                        
                        if (size && size.colors) {
                            let color = size.colors.find(c => String(c.id) === String(colorId));
                            
                            if (color) {
                                color.stock = value;
                                console.log(`✅ Saved stock: ${sizeId}-${colorId} = ${value}`);
                            } else {
                                console.warn(`❌ Color not found: ${colorId} in size: ${sizeId}`);
                            }
                        } else {
                            console.warn(`❌ Size not found: ${sizeId}`);
                        }
                    }
                });
                
                // حفظ قيم الأسعار في selectedSizes
                priceInputs.forEach(input => {
                    const matches = input.name.match(/price\[([^\]]+)\]\[([^\]]+)\]/);
                    if (matches) {
                        const sizeId = matches[1];
                        const colorId = matches[2];
                        const value = input.value;
                        
                        console.log(`Trying to save price: ${sizeId}-${colorId} = ${value}`);
                        
                        // العثور على المقاس واللون في selectedSizes
                        let size = selectedSizes.find(s => String(s.id) === String(sizeId));
                        
                        if (size && size.colors) {
                            let color = size.colors.find(c => String(c.id) === String(colorId));
                            
                            if (color) {
                                color.price = value;
                                console.log(`✅ Saved price: ${sizeId}-${colorId} = ${value}`);
                            } else {
                                console.warn(`❌ Color not found: ${colorId} in size: ${sizeId}`);
                            }
                        } else {
                            console.warn(`❌ Size not found: ${sizeId}`);
                        }
                    }
                });
                
                matrixContainer.innerHTML = '';
                console.log('Updating size color matrix with', selectedSizes ? selectedSizes.length : 0, 'sizes');
                
                // التأكد من وجود مصفوفة المقاسات
                if (!selectedSizes || !Array.isArray(selectedSizes)) {
                    console.warn('selectedSizes is not an array, initializing it');
                    selectedSizes = [];
                    return;
                }
                
                // إنشاء مستطيلات المقاسات
                selectedSizes.forEach((size, sizeIndex) => {
                    const sizeContainer = document.createElement('div');
                    sizeContainer.className = 'size-container active';
                    sizeContainer.dataset.sizeId = size.id;
                    
                    // الحصول على الألوان المختارة لهذا المقاس
                    const selectedColors = size.colors || [];
                    
                    sizeContainer.innerHTML = `
                        <div class="size-header">
                            <div class="size-title">
                                <i class="fas fa-ruler"></i>
                                المقاس ${sizeIndex + 1}
                                <span class="size-number">${sizeIndex + 1}</span>
                            </div>
                            <button type="button" class="size-remove-btn" onclick="removeSizeFromCard(${sizeIndex})">
                                <i class="fas fa-times"></i>
                                حذف المقاس
                            </button>
                        </div>
                        
                        <select class="size-select" onchange="updateSizeName(${sizeIndex}, this.value)">
                            <option value="">اختر المقاس...</option>
                            ${availableSizes.map(s => `
                                <option value="${s.id}" ${s.id == size.id ? 'selected' : ''}>
                                    ${s.name} - ${s.description || ''}
                                </option>
                            `).join('')}
                        </select>
                        
                        <div class="colors-section" id="colors-section-${size.id}">
                            <h6 class="mb-3" style="color: #007bff; font-weight: 600;">
                                <i class="fas fa-palette me-2"></i>
                                الألوان المتاحة
                            </h6>
                            <div class="size-colors-container" id="size-colors-${size.id}">
                                ${selectedColors.map(color => `
                                    <div class="color-item" data-color-id="${color.id}">
                                        <select class="color-select" onchange="updateColorName(this, '${size.id}')">
                                            <option value="">اختر اللون...</option>
                                            ${availableColors.map(c => `
                                                <option value="${c.id}" data-hex="${c.code || '#007bff'}" ${c.id == color.id ? 'selected' : ''}>
                                                    ${c.name} - ${c.description || ''}
                                                </option>
                                            `).join('')}
                                        </select>
                                        
                                        <div class="color-inputs">
                                            <div class="input-group-sm">
                                                <label>عدد القطع:</label>
                                                <input type="number" 
                                                    name="stock[${size.id}][${color.id}]" 
                                                    placeholder="50"
                                                    min="0"
                                                    value="${color.stock || ''}"
                                                    required>
                                            </div>
                                            <div class="input-group-sm">
                                                <label>السعر (اختياري):</label>
                                                <input type="number" 
                                                    name="price[${size.id}][${color.id}]" 
                                                    placeholder="150"
                                                    step="0.01"
                                                    min="0"
                                                    value="${color.price || ''}">
                                            </div>
                                        </div>
                                        
                                        <button type="button" class="color-remove-btn" onclick="removeColorFromSize('${size.id}', '${color.id}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                `).join('')}
                            </div>
                            <button type="button" class="add-color-btn" onclick="addColorToSize('${size.id}')">
                                <i class="fas fa-plus me-1"></i>
                                إضافة لون آخر
                            </button>
                        </div>
                    `;
                    
                    matrixContainer.appendChild(sizeContainer);
                    
                    // إضافة event listeners للحقول الجديدة
                    const stockInputs = sizeContainer.querySelectorAll('input[name*="stock"]');
                    const priceInputs = sizeContainer.querySelectorAll('input[name*="price"]');
                    
                    stockInputs.forEach(input => {
                        input.addEventListener('input', function() {
                            const matches = this.name.match(/stock\[([^\]]+)\]\[([^\]]+)\]/);
                            if (matches) {
                                const sizeId = matches[1];
                                const colorId = matches[2];
                                const value = this.value;
                                
                                // العثور على المقاس واللون في selectedSizes
                                let size = selectedSizes.find(s => String(s.id) === String(sizeId));
                                if (size && size.colors) {
                                    let color = size.colors.find(c => String(c.id) === String(colorId));
                                    if (color) {
                                        color.stock = value;
                                        console.log(`Updated stock: ${sizeId}-${colorId} = ${value}`);
                                    }
                                }
                            }
                        });
                    });
                    
                    priceInputs.forEach(input => {
                        input.addEventListener('input', function() {
                            const matches = this.name.match(/price\[([^\]]+)\]\[([^\]]+)\]/);
                            if (matches) {
                                const sizeId = matches[1];
                                const colorId = matches[2];
                                const value = this.value;
                                
                                // العثور على المقاس واللون في selectedSizes
                                let size = selectedSizes.find(s => String(s.id) === String(sizeId));
                                if (size && size.colors) {
                                    let color = size.colors.find(c => String(c.id) === String(colorId));
                                    if (color) {
                                        color.price = value;
                                        console.log(`Updated price: ${sizeId}-${colorId} = ${value}`);
                                    }
                                }
                            }
                        });
                    });
                });
                
                console.log('Size color matrix updated successfully');
            } catch (error) {
                console.error('Error in updateSizeColorMatrix:', error);
                alert('حدث خطأ أثناء تحديث مصفوفة المقاسات والألوان: ' + error.message);
            }
        }

        // دالة إضافة مقاس جديد
        function addNewSize() {
            try {
                console.log('Adding new size...');
                let newSize;
                
                // إذا كانت هناك مقاسات متاحة، استخدم أول مقاس
                if (availableSizes && availableSizes.length > 0) {
                    const firstSize = availableSizes[0];
                    newSize = {
                        id: firstSize.id,
                        name: firstSize.name,
                        colors: [] // مصفوفة فارغة للألوان
                    };
                    console.log('Using available size:', firstSize);
                } else {
                    // إذا لم تكن هناك مقاسات متاحة، أنشئ مقاس مؤقت
                    newSize = {
                        id: 'temp_' + Date.now(),
                        name: 'مقاس جديد',
                        colors: [] // مصفوفة فارغة للألوان
                    };
                    console.log('Created temporary size');
                }
                
                // التأكد من أن مصفوفة المقاسات المختارة موجودة
                if (!selectedSizes) {
                    selectedSizes = [];
                    console.log('Initialized selectedSizes array');
                }
                
                selectedSizes.push(newSize);
                console.log('New size added:', newSize);
                console.log('Total sizes:', selectedSizes.length);
                
                // تحديث المصفوفة في واجهة المستخدم
                updateSizeColorMatrix();
                
                return true;
            } catch (error) {
                console.error('Error in addNewSize:', error);
                alert('حدث خطأ أثناء إضافة مقاس جديد: ' + error.message);
                return false;
            }
        }

        // دالة حذف مقاس من البطاقة
        function removeSizeFromCard(sizeIndex) {
            if (confirm('هل أنت متأكد من حذف هذا المقاس؟')) {
                selectedSizes.splice(sizeIndex, 1);
                updateSizeColorMatrix();
            }
        }

        // دالة إضافة لون لمقاس معين
        function addColorToSize(sizeId) {
            console.log('Adding color to size:', sizeId);
            console.log('Available sizes:', selectedSizes);
            
            // البحث عن المقاس المحدد بطرق مختلفة
            let size = null;
            
            // الطريقة 1: البحث المباشر بالـ ID
            size = selectedSizes.find(s => s.id === sizeId);
            if (size) {
                console.log('Found size by direct ID match:', size);
            }
            
            // الطريقة 2: البحث بالـ string comparison
            if (!size) {
                size = selectedSizes.find(s => String(s.id) === String(sizeId));
                if (size) {
                    console.log('Found size by string comparison:', size);
                }
            }
            
            // الطريقة 3: البحث بالـ index إذا كان sizeId رقم
            if (!size) {
                const sizeIndex = parseInt(sizeId);
                if (!isNaN(sizeIndex) && sizeIndex >= 0 && sizeIndex < selectedSizes.length) {
                    size = selectedSizes[sizeIndex];
                    console.log('Found size by index:', sizeIndex, size);
                }
            }
            
            if (!size) {
                console.error('Size not found:', sizeId);
                console.error('Available sizes:', selectedSizes.map(s => ({ id: s.id, name: s.name })));
                alert('خطأ: لم يتم العثور على المقاس المحدد. يرجى إعادة المحاولة.');
                return;
            }
            
            // التأكد من وجود مصفوفة الألوان
            if (!size.colors) {
                size.colors = [];
            }
            
            let newColor;
            
            // إذا كانت هناك ألوان متاحة، استخدم أول لون
            if (availableColors.length > 0) {
                const firstColor = availableColors[0];
                newColor = {
                    id: firstColor.id,
                    name: firstColor.name,
                    stock: '',
                    price: ''
                };
            } else {
                // إذا لم تكن هناك ألوان متاحة، أنشئ لون مؤقت
                newColor = {
                    id: 'temp_' + Date.now(),
                    name: '',
                    stock: '',
                    price: ''
                };
            }
            
            size.colors.push(newColor);
            
            // تحديث المصفوفة في واجهة المستخدم
            updateSizeColorMatrix();
            
            console.log('Color added successfully to size:', size.id, 'Total colors:', size.colors.length);
            
            // تمرير التركيز إلى حقل المخزون الجديد
            setTimeout(() => {
                const newColorItem = document.querySelector(`[data-color-id="${newColor.id}"]`);
                if (newColorItem) {
                    const stockInput = newColorItem.querySelector('input[name*="stock"]');
                    if (stockInput) {
                        stockInput.focus();
                    }
                }
            }, 100);
        }

        // دالة حذف لون من مقاس معين
        function removeColorFromSize(sizeId, colorId) {
            if (confirm('هل أنت متأكد من حذف هذا اللون من المقاس؟')) {
                // العثور على المقاس المحدد بطرق مختلفة
                let size = null;
                
                // الطريقة 1: البحث المباشر بالـ ID
                size = selectedSizes.find(s => s.id === sizeId);
                
                // الطريقة 2: البحث بالـ string comparison
                if (!size) {
                    size = selectedSizes.find(s => String(s.id) === String(sizeId));
                }
                
                // الطريقة 3: البحث بالـ index إذا كان sizeId رقم
                if (!size) {
                    const sizeIndex = parseInt(sizeId);
                    if (!isNaN(sizeIndex) && sizeIndex >= 0 && sizeIndex < selectedSizes.length) {
                        size = selectedSizes[sizeIndex];
                    }
                }
                
                if (!size || !size.colors) {
                    console.error('Size not found:', sizeId);
                    console.error('Available sizes:', selectedSizes.map(s => ({ id: s.id, name: s.name })));
                    alert('خطأ: لم يتم العثور على المقاس. يرجى إعادة تحميل الصفحة.');
                    return;
                }
                
                // حذف اللون من المصفوفة
                const colorIndex = size.colors.findIndex(c => c.id === colorId);
                if (colorIndex !== -1) {
                    size.colors.splice(colorIndex, 1);
                    updateSizeColorMatrix();
                }
            }
        }

        // دالة تحديث اسم المقاس
        function updateSizeName(sizeIndex, sizeId) {
            if (sizeId) {
                // التحقق من عدم تكرار المقاس
                const existingSize = selectedSizes.find((size, index) => 
                    index !== sizeIndex && size.id == sizeId
                );
                
                if (existingSize) {
                    alert('هذا المقاس موجود بالفعل في منتج آخر');
                    // إعادة تعيين القيمة المختارة
                    const selectElement = event.target;
                    selectElement.value = selectedSizes[sizeIndex].id || '';
                    return;
                }
                
                const sizeOption = document.querySelector(`option[value="${sizeId}"]`);
                if (sizeOption) {
                    selectedSizes[sizeIndex].id = sizeId;
                    selectedSizes[sizeIndex].name = sizeOption.textContent;
                }
            }
        }

        // دالة تحديث اسم اللون
        function updateColorName(selectElement, sizeId) {
            const colorItem = selectElement.closest('.color-item');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            if (selectedOption.value) {
                const colorId = selectedOption.value;
                const colorName = selectedOption.textContent;
                
                // العثور على المقاس المحدد بطرق مختلفة
                let size = null;
                
                // الطريقة 1: البحث المباشر بالـ ID
                size = selectedSizes.find(s => s.id === sizeId);
                
                // الطريقة 2: البحث بالـ string comparison
                if (!size) {
                    size = selectedSizes.find(s => String(s.id) === String(sizeId));
                }
                
                // الطريقة 3: البحث بالـ index إذا كان sizeId رقم
                if (!size) {
                    const sizeIndex = parseInt(sizeId);
                    if (!isNaN(sizeIndex) && sizeIndex >= 0 && sizeIndex < selectedSizes.length) {
                        size = selectedSizes[sizeIndex];
                    }
                }
                
                if (!size || !size.colors) {
                    console.error('Size not found:', sizeId);
                    console.error('Available sizes:', selectedSizes.map(s => ({ id: s.id, name: s.name })));
                    alert('خطأ: لم يتم العثور على المقاس. يرجى إعادة تحميل الصفحة.');
                    return;
                }
                
                // العثور على اللون المحدد
                const colorIndex = size.colors.findIndex(c => c.id === colorItem.dataset.colorId);
                if (colorIndex === -1) {
                    return;
                }
                
                // التحقق من عدم تكرار اللون في نفس المقاس
                const existingColor = size.colors.find((c, index) => 
                    index !== colorIndex && c.id == colorId
                );
                
                if (existingColor) {
                    alert('هذا اللون موجود بالفعل في هذا المقاس');
                    // إعادة تعيين القيمة المختارة
                    selectElement.value = size.colors[colorIndex].id || '';
                    return;
                }
                
                // تحديث بيانات اللون
                size.colors[colorIndex].id = colorId;
                size.colors[colorIndex].name = colorName;
                
                // تحديث data-color-id
                colorItem.dataset.colorId = colorId;
                
                // تحديث أسماء الحقول
                const colorStockInput = colorItem.querySelector('input[name*="stock"]');
                const priceInput = colorItem.querySelector('input[name*="price"]');
                
                if (colorStockInput) {
                    colorStockInput.name = `stock[${sizeId}][${colorId}]`;
                }
                if (priceInput) {
                    priceInput.name = `price[${sizeId}][${colorId}]`;
                }
            }
        }

        // إضافة event listener لزر إضافة مقاس جديد
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة النظام الجديد لإدارة المخزون
            console.log('Initializing new inventory system for edit...');
            try {
                updateInventoryMatrix();
                console.log('New inventory system initialized successfully for edit');
            } catch (error) {
                console.error('Error initializing new inventory system for edit:', error);
            }
            
            const addSizeButton = document.getElementById('addSizeButton');
            if (addSizeButton) {
                addSizeButton.addEventListener('click', function() {
                    addNewSize();
                });
            }
        });

        // دالة إضافة صف مخزون جديد
        function addInventoryRow() {
            const matrixContainer = document.getElementById('inventoryMatrix');
            if (!matrixContainer) {
                console.error('Inventory matrix container not found');
                return;
            }
            
            const rowId = 'inventory-row-' + inventoryRowCounter++;
            
            const rowHtml = `
                <div class="inventory-row card mb-3" id="${rowId}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">المقاس</label>
                                <select class="form-select size-select" name="inventories[${rowId}][size_id]" required>
                                    <option value="">اختر المقاس...</option>
                                    ${availableSizes ? availableSizes.map(size => `
                                        <option value="${size.id}">${size.name} - ${size.description || ''}</option>
                                    `).join('') : ''}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">اللون</label>
                                <select class="form-select color-select" name="inventories[${rowId}][color_id]" required>
                                    <option value="">اختر اللون...</option>
                                    ${availableColors ? availableColors.map(color => `
                                        <option value="${color.id}">${color.name} - ${color.description || ''}</option>
                                    `).join('') : ''}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">المخزون</label>
                                <input type="number" 
                                       class="form-control" 
                                       name="inventories[${rowId}][stock]" 
                                       placeholder="50"
                                       min="0"
                                       required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">السعر (ر.س)</label>
                                <input type="number" 
                                       class="form-control" 
                                       name="inventories[${rowId}][price]" 
                                       placeholder="150"
                                       step="0.01"
                                       min="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger d-block w-100" onclick="removeInventoryRow('${rowId}')">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            matrixContainer.insertAdjacentHTML('beforeend', rowHtml);
            inventoryRows.push(rowId);
            
            console.log('Added inventory row:', rowId);
        }
        
        // دالة حذف صف مخزون
        function removeInventoryRow(rowId) {
            if (confirm('هل أنت متأكد من حذف هذا الصف؟')) {
                const row = document.getElementById(rowId);
                if (row) {
                    row.remove();
                    inventoryRows = inventoryRows.filter(id => id !== rowId);
                    console.log('Removed inventory row:', rowId);
                }
            }
        }
        
        // دالة تحديث مصفوفة المخزون مع البيانات الموجودة
        function updateInventoryMatrix() {
            const matrixContainer = document.getElementById('inventoryMatrix');
            matrixContainer.innerHTML = '';
            inventoryRows = [];
            inventoryRowCounter = 0;
            
            // إضافة البيانات الموجودة من قاعدة البيانات
            @if(isset($inventoryMap) && $inventoryMap->count() > 0)
                @foreach($inventoryMap as $inventory)
                    var existingRowId = 'inventory-row-' + inventoryRowCounter++;
                    var rowHtml = `
                        <div class="inventory-row card mb-3" id="${existingRowId}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <label class="form-label">المقاس</label>
                                        <select class="form-select size-select" name="inventories[{{ $inventory['size_id'] }}][{{ $inventory['color_id'] }}][size_id]" required>
                                            <option value="">اختر المقاس...</option>
                                            ${availableSizes.map(size => `
                                                <option value="${size.id}" ${size.id == '{{ $inventory['size_id'] }}' ? 'selected' : ''}>${size.name} - ${size.description || ''}</option>
                                            `).join('')}
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">اللون</label>
                                        <select class="form-select color-select" name="inventories[{{ $inventory['size_id'] }}][{{ $inventory['color_id'] }}][color_id]" required>
                                            <option value="">اختر اللون...</option>
                                            ${availableColors.map(color => `
                                                <option value="${color.id}" ${color.id == '{{ $inventory['color_id'] }}' ? 'selected' : ''}>${color.name} - ${color.description || ''}</option>
                                            `).join('')}
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">المخزون</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="inventories[{{ $inventory['size_id'] }}][{{ $inventory['color_id'] }}][stock]" 
                                               placeholder="50"
                                               min="0"
                                               value="{{ $inventory['stock'] }}"
                                               required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">السعر (ر.س)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="inventories[{{ $inventory['size_id'] }}][{{ $inventory['color_id'] }}][price]" 
                                               placeholder="150"
                                               step="0.01"
                                               min="0"
                                               value="{{ $inventory['price'] }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger d-block w-100" onclick="removeInventoryRow('${existingRowId}')">
                                            <i class="fas fa-trash"></i>
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    matrixContainer.insertAdjacentHTML('beforeend', rowHtml);
                    inventoryRows.push(existingRowId);
                @endforeach
            @else
                // إضافة صف افتراضي واحد إذا لم تكن هناك بيانات
                addInventoryRow();
            @endif
        }
        
        // دالة تشخيص البيانات
        window.debugFormData = function() {
            console.log('🔍 === تشخيص البيانات ===');
            console.log('selectedSizes:', selectedSizes);
            console.log('availableSizes:', availableSizes);
            console.log('availableColors:', availableColors);
            
            const form = document.querySelector('form');
            if (form) {
                const selectedSizesInputs = form.querySelectorAll('input[name="selected_sizes[]"]');
                const selectedColorsInputs = form.querySelectorAll('input[name="selected_colors[]"]');
                const stockInputs = form.querySelectorAll('input[name*="stock["]');
                const priceInputs = form.querySelectorAll('input[name*="price["]');
                const inventoriesInputs = form.querySelectorAll('input[name*="inventories["]');
                
                console.log('🔍 Inputs found:');
                console.log('- selected_sizes[]:', selectedSizesInputs.length);
                console.log('- selected_colors[]:', selectedColorsInputs.length);
                console.log('- stock inputs:', stockInputs.length);
                console.log('- price inputs:', priceInputs.length);
                console.log('- inventories inputs:', inventoriesInputs.length);
                
                // طباعة تفاصيل الحقول
                selectedSizesInputs.forEach((input, index) => {
                    console.log(`Size ${index + 1}:`, input.value);
                });
                
                selectedColorsInputs.forEach((input, index) => {
                    console.log(`Color ${index + 1}:`, input.value);
                });
                
                stockInputs.forEach((input, index) => {
                    console.log(`Stock ${index + 1}:`, input.name, '=', input.value);
                });
                
                priceInputs.forEach((input, index) => {
                    console.log(`Price ${index + 1}:`, input.name, '=', input.value);
                });
                
                inventoriesInputs.forEach((input, index) => {
                    console.log(`Inventory ${index + 1}:`, input.name, '=', input.value);
                });
                
                // إظهار النتائج للمستخدم
                alert(`
🔍 تشخيص النموذج:
- المقاسات في الذاكرة: ${selectedSizes.length}
- حقول selected_sizes: ${selectedSizesInputs.length}
- حقول selected_colors: ${selectedColorsInputs.length}
- حقول stock: ${stockInputs.length}
- حقول price: ${priceInputs.length}
- حقول inventories: ${inventoriesInputs.length}

راجع Console للتفاصيل الكاملة
                `);
            }
            
            // إعداد البيانات تلقائياً
            console.log('🔍 تحضير البيانات...');
            console.log('selectedSizes before prepareFormDataForLaravel:', selectedSizes);
            prepareFormDataForLaravel();
            console.log('✅ تم تحضير البيانات!');
            
            // التحقق من النتائج بعد التحضير
            const finalSizes = form.querySelectorAll('input[name="selected_sizes[]"]');
            const finalColors = form.querySelectorAll('input[name="selected_colors[]"]');
            const finalStock = form.querySelectorAll('input[name*="stock["]');
            const finalPrice = form.querySelectorAll('input[name*="price["]');
            const finalInventories = form.querySelectorAll('input[name*="inventories["]');
            
            console.log('🔍 Results after prepareFormDataForLaravel:');
            console.log('- selected_sizes[]:', finalSizes.length);
            console.log('- selected_colors[]:', finalColors.length);
            console.log('- stock inputs:', finalStock.length);
            console.log('- price inputs:', finalPrice.length);
            console.log('- inventories inputs:', finalInventories.length);
        };
</script>
@endsection
