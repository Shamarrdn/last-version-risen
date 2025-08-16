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
                        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
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
                                

                                <!-- إدارة المقاسات والألوان -->
                                <div class="col-12 mt-4">
                                    <div class="card card-body shadow-sm border-0">
                                        <div class="card-title d-flex align-items-center justify-content-between">
                                            <h5>
                                                <i class="fas fa-palette me-2 text-primary"></i>
                                                إدارة المقاسات والألوان
                                                </h5>
                                            <small class="text-muted">اختر المقاسات والألوان المتاحة من قاعدة البيانات</small>
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

                                        <!-- تفاصيل المقاسات والألوان -->
                                        <div id="sizeColorDetails" class="mt-4">
                                            <h6 class="fw-bold mb-3">
                                                <i class="fas fa-cogs me-2"></i>
                                                تفاصيل المقاسات والألوان
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
    
    // متغيرات عامة
    let selectedSizes = [];
    let availableSizes = [];
    let availableColors = [];

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
        if (selectedSizes.length === 0) {
            addNewSize();
        }
        
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
            
            // حفظ القيم المدخلة قبل إعادة التحديث
            const currentValues = {};
            const stockInputs = matrixContainer.querySelectorAll('input[name*="stock"]');
            const priceInputs = matrixContainer.querySelectorAll('input[name*="price"]');
            
            stockInputs.forEach(input => {
                const name = input.name;
                currentValues[name] = input.value;
            });
            
            priceInputs.forEach(input => {
                const name = input.name;
                currentValues[name] = input.value;
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
                                            value="${currentValues[`stock[${size.id}][${color.id}]`] || color.stock || ''}"
                                            required>
                                    </div>
                                    <div class="input-group-sm">
                                        <label>السعر (ر.س):</label>
                                        <input type="number" 
                                            name="price[${size.id}][${color.id}]" 
                                            placeholder="150"
                                            step="0.01"
                                            min="0"
                                            value="${currentValues[`price[${size.id}][${color.id}]`] || color.price || ''}">
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
        updateSizeColorMatrix();
        console.log('Color added successfully to size:', size.id, 'Total colors:', size.colors.length);
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
                
                // إظهار رسالة تحميل
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
                `;
                document.body.appendChild(loadingAlert);
                
                // التحقق من صحة البيانات
                const result = handleSaveClickInternal();
                
                if (!result) {
                    loadingAlert.remove();
                    return false;
                }
                
                // إعداد البيانات للمقاسات والألوان
                prepareFormData(form);
                
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
                    loadingAlert.remove();
                    
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
            nameInput?.focus();
            return false;
        }
        
        // التحقق من التصنيف
        if (!categoryInput || !categoryInput.value) {
            alert('يرجى اختيار التصنيف الرئيسي');
            categoryInput?.focus();
            return false;
        }
        
        // التحقق من الوصف
        if (!descriptionInput || !descriptionInput.value.trim()) {
            alert('يرجى إدخال وصف المنتج');
            descriptionInput?.focus();
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
        console.log('Preparing form data for sizes and colors...');
        
        // إزالة جميع الحقول المخفية المتعلقة بالمقاسات والألوان
        const oldInputs = form.querySelectorAll('input[name^="selected_sizes"], input[name^="selected_colors"], input[name^="stock["], input[name^="price["]');
        oldInputs.forEach(input => {
            console.log('Removing old input:', input.name, input.value);
            input.remove();
        });
        
        // تحويل المقاسات والألوان إلى تنسيق JSON
        const sizesData = selectedSizes.map(s => s.id);
        const colorsData = selectedSizes.flatMap(s => s.colors || []).map(c => c.id);
        
        console.log('Selected sizes data:', sizesData);
        console.log('Selected colors data:', colorsData);
        
        // إضافة كل مقاس كحقل منفصل
        sizesData.forEach(sizeId => {
            if (!sizeId || String(sizeId).includes('temp_')) {
                console.log('Skipping temporary size:', sizeId);
                return;
            }
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_sizes[]';
            input.value = sizeId;
            form.appendChild(input);
            console.log('Added size input:', sizeId);
        });
        
        // إضافة الألوان المختارة
        colorsData.forEach(colorId => {
            if (!colorId || String(colorId).includes('temp_')) {
                console.log('Skipping temporary color:', colorId);
                return;
            }
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_colors[]';
            input.value = colorId;
            form.appendChild(input);
            console.log('Added color input:', colorId);
        });
        
        // إضافة بيانات المخزون والأسعار بشكل صريح
        selectedSizes.forEach(size => {
            if (!size.id || String(size.id).includes('temp_')) {
                console.log('Skipping stock/price for temporary size:', size.id);
                return;
            }
            
            if (size.colors && size.colors.length > 0) {
                size.colors.forEach(color => {
                    if (!color.id || String(color.id).includes('temp_')) {
                        console.log('Skipping stock/price for temporary color:', color.id);
                        return;
                    }
                    
                    // إضافة المخزون
                    const stockInput = document.createElement('input');
                    stockInput.type = 'hidden';
                    stockInput.name = `stock[${size.id}][${color.id}]`;
                    stockInput.value = color.stock || 0;
                    form.appendChild(stockInput);
                    console.log(`Added stock input: stock[${size.id}][${color.id}] = ${color.stock || 0}`);
                    
                    // إضافة السعر
                    const priceInput = document.createElement('input');
                    priceInput.type = 'hidden';
                    priceInput.name = `price[${size.id}][${color.id}]`;
                    priceInput.value = color.price || '';
                    form.appendChild(priceInput);
                    console.log(`Added price input: price[${size.id}][${color.id}] = ${color.price || ''}`);
                });
            }
        });
        
        // التأكد من أن حقل المخزون يحتوي على قيمة صحيحة
        const stockInput = form.querySelector('input[name="stock"]');
        if (stockInput) {
            const stockValue = parseInt(stockInput.value) || 0;
            stockInput.value = Math.max(0, stockValue);
            console.log('Stock value set to:', stockInput.value);
        }
        
        // إضافة CSRF token للطلبات AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }
        
        console.log('Form data prepared successfully');
    }
</script>
@endsection
