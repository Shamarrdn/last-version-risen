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
@endphp

@section('title', 'إضافة منتج جديد')
@section('page_title', 'إضافة منتج جديد')

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
                                            <i class="fas fa-plus text-primary me-2"></i>
                                            إضافة منتج جديد
                                        </h5>
                                        <div class="actions">
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
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

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
                                                <input type="text" name="name" class="form-control shadow-sm @error('name') is-invalid @enderror" value="{{ safeOld('name', '') }}" required>
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="category_id" class="form-label required">التصنيف الرئيسي</label>
                                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                                    <option value="">اختر التصنيف الرئيسي</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ safeOld('category_id') == $category->id ? 'selected' : '' }}>
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
                                                                    {{ is_array(old('categories')) && in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
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
                                                        name="is_available" value="1" {{ safeOld('is_available', '1') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isAvailable">متاح للبيع</label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">الرابط المختصر (Slug)</label>
                                                <input type="text" name="slug"
                                                    class="form-control shadow-sm @error('slug') is-invalid @enderror"
                                                    value="{{ safeOld('slug', '') }}" readonly disabled>
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
                                                <textarea name="description" class="form-control shadow-sm @error('description') is-invalid @enderror" rows="4" required>{{ safeOld('description', '') }}</textarea>
                                                @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-tag text-primary me-2"></i>
                                                    السعر الأساسي
                                                </label>
                                                <div class="input-group shadow-sm">
                                                    <input type="number" name="base_price" class="form-control @error('base_price') is-invalid @enderror"
                                                        placeholder="السعر الأساسي" step="0.01" min="0" value="{{ safeOld('base_price', '') }}">
                                                    <span class="input-group-text">ر.س</span>
                                                </div>
                                                <small class="text-muted">سيتم استخدام هذا السعر إذا لم تكن هناك مقاسات بأسعار محددة</small>
                                                @error('base_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-boxes text-primary me-2"></i>
                                                    المخزون
                                                </label>
                                                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" placeholder="كمية المخزون" min="0" step="1" value="{{ safeOldInt('stock', 0) }}" required onchange="this.value = Math.max(0, parseInt(this.value) || 0)">
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
                                                    @if(old('detail_keys') && is_array(old('detail_keys')))
                                                        @foreach(old('detail_keys') as $index => $key)
                                                            <div class="input-group mb-2 shadow-sm">
                                                                <input type="text" name="detail_keys[]" class="form-control" placeholder="الخاصية" value="{{ $key }}">
                                                                <input type="text" name="detail_values[]" class="form-control" placeholder="القيمة" value="{{ is_array(old('detail_values')) ? (old('detail_values')[$index] ?? '') : '' }}">
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

                                            <div class="mb-3">
                                                <label class="form-label">صور المنتج</label>
                                                @error('images.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('is_primary.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="imagesContainer">
                                                    <div class="mb-2">
                                                        <div class="input-group shadow-sm">
                                                            <input type="file" name="images[]" class="form-control" accept="image/*">
                                                            <div class="input-group-text">
                                                                <label class="mb-0">
                                                                    <input type="radio" name="is_primary[0]" value="1" class="me-1">
                                                                    صورة رئيسية
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm mt-2" onclick="addImageInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة صورة
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>





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
                                        
                                        @if($availableSizes->isEmpty() || $availableColors->isEmpty())
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>تنبيه:</strong>
                                            @if($availableSizes->isEmpty())
                                                لا توجد مقاسات متاحة. 
                                            @endif
                                            @if($availableColors->isEmpty())
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
                                                حفظ المنتج
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
        background: #007bff;
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
    
    /* تصميم المقاسات الجديد */
    .size-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .size-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4);
    }
    
    .size-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        color: white;
    }
    
    .size-name {
        font-size: 1.25rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .size-remove-btn {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .size-remove-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }
    
    .colors-section {
        background: rgba(255,255,255,0.1);
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .color-item {
        background: white;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .color-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .color-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .color-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        margin-left: 10px;
    }
    
    .color-name {
        font-weight: 600;
        color: #333;
        flex-grow: 1;
    }
    
    .color-remove-btn {
        background: #ff6b6b;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.3s ease;
    }
    
    .color-remove-btn:hover {
        background: #ff5252;
        transform: scale(1.1);
    }
    
    .color-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    .input-group-sm {
        margin-bottom: 8px;
    }
    
    .input-group-sm label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 4px;
        display: block;
    }
    
    .input-group-sm input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: border-color 0.3s ease;
    }
    
    .input-group-sm input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .add-color-btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        margin-top: 10px;
    }
    
    .add-color-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-1px);
    }
    
    .no-colors-message {
        text-align: center;
        color: rgba(255,255,255,0.8);
        font-style: italic;
        padding: 20px;
    }
    
    /* التصميم الجديد المبسط */
    .size-container {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
    }
    
    .size-container.active {
        border-color: #007bff;
        background: #f8f9ff;
    }
    
    .size-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .size-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
    }
    
    .size-title i {
        margin-left: 8px;
        color: #007bff;
    }
    
    .size-remove-btn {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .size-remove-btn:hover {
        background: #c82333;
    }
    
    .size-remove-btn i {
        font-size: 0.75rem;
    }
    
    .size-select {
        width: 200px;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .colors-section {
        margin-top: 15px;
        padding-right: 20px;
        border-right: 3px solid #007bff;
    }
    
    .color-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 12px;
        padding: 10px;
        background: white;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    
    .color-select {
        width: 150px;
        padding: 6px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    
    .color-inputs {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-grow: 1;
    }
    
    .input-group-sm {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .input-group-sm label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
        white-space: nowrap;
    }
    
    .input-group-sm input {
        width: 80px;
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 0.8rem;
    }
    
    .color-remove-btn {
        background: #ff6b6b;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    .color-remove-btn:hover {
        background: #ff5252;
    }
    
    .add-color-btn {
        background: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }
    
    .add-color-btn:hover {
        background: #218838;
    }
    
    .add-size-btn {
        background: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 12px 24px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 20px;
        width: 100%;
    }
    
    .add-size-btn:hover {
        background: #0056b3;
    }
    
    .size-number {
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 8px;
    }
</style>
@endsection

@section('scripts')
<script>
    // المتغيرات العامة
    let selectedSizes = [];
    let availableSizes = [];
    let availableColors = [];
    let imageCount = 1;

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



    function addImageInput() {
        const container = document.getElementById('imagesContainer');
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.innerHTML = `
        <div class="input-group shadow-sm">
            <input type="file" name="images[]" class="form-control" accept="image/*">
            <div class="input-group-text">
                <label class="mb-0">
                    <input type="radio" name="is_primary[${imageCount}]" value="1" class="me-1">
                    صورة رئيسية
                </label>
            </div>
            <button type="button" class="btn btn-light-danger" onclick="this.closest('.mb-2').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
        container.appendChild(div);
        imageCount++;
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





    // تحميل البيانات عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
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
                    // التأكد من القيمة الصحيحة عند التحميل
        const initialValue = parseInt(mainStockInput.value) || 0;
        mainStockInput.value = Math.max(0, initialValue);
        console.log('Stock input initialized with value:', mainStockInput.value);
            
            mainStockInput.addEventListener('blur', function() {
                const value = parseInt(this.value) || 0;
                this.value = Math.max(0, value);
                console.log('Stock input blur event - value set to:', this.value);
            });
            
            mainStockInput.addEventListener('input', function() {
                // السماح فقط بالأرقام
                this.value = this.value.replace(/[^0-9]/g, '');
                console.log('Stock input input event - value:', this.value);
            });
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
        console.log('Checking if we need to add a default size...');
        try {
            if (!selectedSizes || selectedSizes.length === 0) {
                console.log('Adding default size on page load');
                setTimeout(function() {
                    addNewSize();
                }, 500);
            }
        } catch (error) {
            console.error('Error adding default size:', error);
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
        
        // إضافة رسائل في القوائم المنسدلة إذا لم تكن هناك خيارات
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
        }, 100);
        
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
        
        // إضافة مستمع حدث لزر إضافة مقاس جديد
        document.getElementById('addSizeButton').addEventListener('click', function() {
            addNewSize();
        });
    });

    // تحديث مصفوفة المقاسات والألوان - التصميم الجديد المبسط
    function updateSizeColorMatrix() {
        try {
            const matrixContainer = document.getElementById('sizeColorMatrix');
            if (!matrixContainer) {
                console.error('Size color matrix container not found');
                return;
            }
            
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
        });
        
        console.log('Size color matrix updated successfully');
        } catch (error) {
            console.error('Error in updateSizeColorMatrix:', error);
            alert('حدث خطأ أثناء تحديث مصفوفة المقاسات والألوان: ' + error.message);
        }
    }

    // تحديث بيانات المصفوفة
    function updateMatrixData(checkbox) {
        const sizeId = checkbox.dataset.size;
        const colorId = checkbox.dataset.color;
        const detailsDiv = document.getElementById(`details-${sizeId}-${colorId}`);

        if (checkbox.checked) {
            detailsDiv.style.display = 'block';
        } else {
            detailsDiv.style.display = 'none';
            // مسح القيم
            detailsDiv.querySelectorAll('input').forEach(input => input.value = '');
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
                // حفظ القيمة في البيانات
                colorStockInput.addEventListener('input', function() {
                    size.colors[colorIndex].stock = this.value;
                });
            }
            if (priceInput) {
                priceInput.name = `price[${sizeId}][${colorId}]`;
                // حفظ القيمة في البيانات
                priceInput.addEventListener('input', function() {
                    size.colors[colorIndex].price = this.value;
                });
            }
        }
    }

    // حذف لون من مقاس معين
    function removeColorFromSize(sizeId, colorId) {
        if (confirm('هل أنت متأكد من حذف هذا اللون من المقاس؟')) {
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
            
            // حذف اللون من المصفوفة
            const colorIndex = size.colors.findIndex(c => c.id === colorId);
            if (colorIndex !== -1) {
                size.colors.splice(colorIndex, 1);
                updateSizeColorMatrix();
            }
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
                selectedSizes[sizeIndex].id = sizeId;
                selectedSizes[sizeIndex].name = sizeOption.textContent;
            }
        }
    }



    // دالة معالجة النقر على زر الحفظ

    
    function validateForm() {
        // التحقق من صحة النموذج
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
        
        // تحديث قيمة المخزون في النموذج
        stockInput.value = Math.max(0, stockValue);
        console.log('Stock value updated to:', stockInput.value);
        
        return true;
    }

    // إعداد البيانات قبل الإرسال
    function prepareFormData() {
        console.log('Preparing form data...');
        console.log('Selected sizes:', selectedSizes);
        
        const form = document.querySelector('form');
        if (!form) {
            console.error('Form not found!');
            return false;
        }
        
        // التأكد من أن حقل المخزون يحتوي على قيمة صحيحة
        const stockInput = form.querySelector('input[name="stock"]');
        if (stockInput) {
            const stockValue = parseInt(stockInput.value) || 0;
            stockInput.value = Math.max(0, stockValue);
            console.log('Stock value set to:', stockInput.value);
        }
        
        // إزالة البيانات القديمة
        form.querySelectorAll('input[name^="selected_sizes"], input[name^="stock["], input[name^="price["]').forEach(input => {
            input.remove();
        });
        
        // إضافة المقاسات المختارة
        selectedSizes.forEach(size => {
            // تجاهل المقاسات المؤقتة التي لم يتم اختيار مقاس حقيقي لها
            if (size.id && !String(size.id).startsWith('temp_')) {
                console.log('Adding size:', size.id, size.name);
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_sizes[]';
                input.value = size.id;
                form.appendChild(input);
                
                // إضافة الألوان المختارة لهذا المقاس
                if (size.colors && size.colors.length > 0) {
                    size.colors.forEach(color => {
                        if (color.id && !String(color.id).startsWith('temp_') && color.stock) {
                            console.log('Adding color:', color.id, color.name, 'stock:', color.stock);
                            
                            // إضافة الكمية
                            const stockInput = document.createElement('input');
                            stockInput.type = 'hidden';
                            stockInput.name = `stock[${size.id}][${color.id}]`;
                            stockInput.value = color.stock;
                            form.appendChild(stockInput);
                            
                            // إضافة السعر إذا كان موجود
                            if (color.price) {
                                const priceInput = document.createElement('input');
                                priceInput.type = 'hidden';
                                priceInput.name = `price[${size.id}][${color.id}]`;
                                priceInput.value = color.price;
                                form.appendChild(priceInput);
                            }
                        }
                    });
                }
            } else if (size.id && String(size.id).startsWith('temp_')) {
                console.log('Skipping temporary size:', size.id);
            }
        });
        
        console.log('Form data prepared successfully');
        return true; // إرجاع true للتأكد من نجاح العملية
    }

    // إعداد النموذج قبل الإرسال عند التحميل
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
            
            // إعداد النموذج قبل الإرسال
            const form = document.querySelector('form');
            if (form) {
                console.log('Form found, adding submit listener...');
                form.addEventListener('submit', function(e) {
                    console.log('Form submitted, preparing data...');
                    const success = prepareFormData();
                    if (success) {
                        console.log('Data prepared successfully, form will be submitted');
                    } else {
                        console.error('Failed to prepare form data');
                        e.preventDefault();
                    }
                });
            } else {
                console.error('Form not found!');
            }
        } catch (error) {
            console.error('Error in DOMContentLoaded event:', error);
        }
        
        // إضافة event listener للنموذج نفسه
        const formElement = document.querySelector('form');
        if (formElement) {
            formElement.addEventListener('submit', function(e) {
                // منع إرسال النموذج بشكل افتراضي
                e.preventDefault();
                
                // إظهار رسالة تحميل
                const loadingAlert = document.createElement('div');
                loadingAlert.className = 'alert alert-info position-fixed top-0 start-50 translate-middle-x mt-4';
                loadingAlert.style.zIndex = '9999';
                loadingAlert.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <div>جاري حفظ المنتج...</div>
                    </div>
                `;
                document.body.appendChild(loadingAlert);
                
                // التحقق من صحة البيانات
                if (!validateForm()) {
                    loadingAlert.remove();
                    return false;
                }
                
                // إعداد البيانات للمقاسات والألوان
                prepareFormData();
                
                // جمع بيانات النموذج
                const formData = new FormData(formElement);
                
                // إرسال البيانات باستخدام AJAX
                fetch(formElement.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
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
                        throw new Error('حدث خطأ أثناء حفظ المنتج (رمز الخطأ: ' + response.status + ')');
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
                            <div>تم إضافة المنتج بنجاح</div>
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
                            <div>حدث خطأ أثناء حفظ المنتج: ${error.message}</div>
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

        // Add at least one detail input field if none exists
        if (document.querySelectorAll('#detailsContainer .input-group').length === 0) {
            addDetailInput();
        }
        
        // التأكد من أن حقل المخزون يحتوي على قيمة صحيحة
        const stockInput3 = document.querySelector('input[name="stock"]');
        if (stockInput3) {
            const currentValue = parseInt(stockInput3.value) || 0;
            stockInput3.value = Math.max(0, currentValue);
            console.log('Stock input value corrected to:', stockInput3.value);
        }
        
        console.log('Form setup completed');
        
        // اختبار النموذج
        setTimeout(function() {
            console.log('Testing form elements...');
            const form = document.querySelector('form');
            const saveButton = document.querySelector('button[type="submit"]');
            console.log('Form:', form);
            console.log('Save button:', saveButton);
            console.log('Selected sizes:', selectedSizes);
            
            // اختبار زر الحفظ
            if (saveButton) {
                console.log('Testing save button...');
                saveButton.onclick = function() {
                    console.log('Save button clicked via onclick!');
                    prepareFormData();
                };
            }
            
            // التأكد من أن حقل المخزون يحتوي على قيمة صحيحة
            const stockInput = document.querySelector('input[name="stock"]');
            if (stockInput) {
                const currentValue = parseInt(stockInput.value) || 0;
                stockInput.value = Math.max(0, currentValue);
                console.log('Final stock input value check:', stockInput.value);
            }
        }, 1000);
    });
</script>
@endsection
