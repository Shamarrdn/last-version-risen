@extends($adminLayout)

@php
function safeOld($key, $default = '') {
    $value = old($key, $default);
    if (is_array($value)) {
        return $value[0] ?? $default;
    }
    return $value;
}

function safeOldInt($key, $default = 0) {
    $value = safeOld($key, $default);
    return intval($value);
}

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


                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                                                        <button type="submit" class="btn btn-primary">
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
<script src="{{ asset('assets/js/admin/products.js') }}"></script>
<script>
    window.availableSizes = @json($availableSizes ?? []);
    window.availableColors = @json($availableColors ?? []);

    window.existingInventoryData = @json($inventoryMap ?? []);

    document.addEventListener('DOMContentLoaded', function() {


        if (window.existingInventoryData && Object.keys(window.existingInventoryData).length > 0) {
            loadExistingInventoryDataForEdit();
        } else {
            if (window.addInventoryRow) {
                window.addInventoryRow();
            }
        }
    });

    function loadExistingInventoryDataForEdit() {
        Object.entries(window.existingInventoryData).forEach(([key, inventory]) => {
            const rowId = 'inventory-row-' + window.inventoryRowCounter++;
            const matrixContainer = document.getElementById('inventoryMatrix');

            const rowHtml = `
                <div class="inventory-row card mb-3" id="${rowId}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">المقاس</label>
                                <select class="form-select size-select" name="inventories[${rowId}][size_id]" required>
                                    <option value="">اختر المقاس...</option>
                                    ${window.availableSizes.map(size => `
                                        <option value="${size.id}" ${size.id == inventory.size_id ? 'selected' : ''}>${size.name} - ${size.description || ''}</option>
                                    `).join('')}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">اللون</label>
                                <select class="form-select color-select" name="inventories[${rowId}][color_id]" required>
                                    <option value="">اختر اللون...</option>
                                    ${window.availableColors.map(color => `
                                        <option value="${color.id}" ${color.id == inventory.color_id ? 'selected' : ''}>${color.name} - ${color.description || ''}</option>
                                    `).join('')}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">المخزون</label>
                                <input type="number"
                                       class="form-control"
                                       name="inventories[${rowId}][stock]"
                                       value="${inventory.stock || 0}"
                                       placeholder="50"
                                       min="0"
                                       required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">السعر (ر.س)</label>
                                <input type="number"
                                       class="form-control"
                                       name="inventories[${rowId}][price]"
                                       value="${inventory.price || ''}"
                                       placeholder="150"
                                       min="0"
                                       step="0.01">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeInventoryRow('${rowId}')">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            matrixContainer.insertAdjacentHTML('beforeend', rowHtml);
        });
    }
</script>
@endsection
