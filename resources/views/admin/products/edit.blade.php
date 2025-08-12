@extends($adminLayout)

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
                            @method('PATCH')

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
                                                    value="{{ old('name', $product->name) }}">
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="category_id" class="form-label required">التصنيف الرئيسي</label>
                                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                                    <option value="">اختر التصنيف الرئيسي</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                                                    {{ in_array($category->id, old('categories', $selectedCategories)) ? 'checked' : '' }}>
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
                                                        name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isAvailable">متاح للبيع</label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">الرابط المختصر (Slug)</label>
                                                <input type="text" name="slug"
                                                    class="form-control shadow-sm @error('slug') is-invalid @enderror"
                                                    value="{{ old('slug', $product->slug) }}" readonly disabled>
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
                                                    rows="4">{{ old('description', $product->description) }}</textarea>
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
                                                        value="{{ old('base_price', $product->base_price) }}">
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
                                                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" placeholder="كمية المخزون" min="0" value="{{ old('stock', $product->stock) }}">
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
                                                    @if(old('detail_keys'))
                                                        @foreach(old('detail_keys') as $index => $key)
                                                            <div class="input-group mb-2 shadow-sm">
                                                                <input type="text" name="detail_keys[]" class="form-control" placeholder="الخاصية" value="{{ $key }}">
                                                                <input type="text" name="detail_values[]" class="form-control" placeholder="القيمة" value="{{ old('detail_values')[$index] ?? '' }}">
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

                                <!-- Colors -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-palette text-primary me-2"></i>
                                                    الألوان المتاحة
                                                </h5>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        id="hasColors"
                                                        name="has_colors"
                                                        value="1"
                                                        {{ $product->colors->count() > 0 || old('has_colors') ? 'checked' : '' }}
                                                        onchange="toggleColorsSection(this)">
                                                    <label class="form-check-label" for="hasColors">تفعيل الألوان</label>
                                                </div>
                                            </div>
                                            <div id="colorsSection" class="{{ $product->colors->count() > 0 ? 'section-expanded' : 'section-collapsed' }}">
                                                @error('colors.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('color_ids.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('color_available.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="colorsContainer">
                                                    @foreach($product->colors as $color)
                                                    <div class="input-group mb-2 shadow-sm">
                                                        <input type="hidden" name="color_ids[]" value="{{ $color->id }}">
                                                        <input type="text" name="colors[]" class="form-control" placeholder="اسم اللون" value="{{ $color->color }}">
                                                        <div class="input-group-text">
                                                            <label class="mb-0">
                                                                <input type="checkbox" name="color_available[]" value="1" {{ $color->is_available ? 'checked' : '' }} class="me-1">
                                                                متوفر
                                                            </label>
                                                        </div>
                                                        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm" onclick="addColorInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة لون
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sizes -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-ruler text-primary me-2"></i>
                                                    المقاسات المتاحة
                                                </h5>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        id="hasSizes"
                                                        name="has_sizes"
                                                        value="1"
                                                        {{ $product->sizes->count() > 0 || old('has_sizes') ? 'checked' : '' }}
                                                        onchange="toggleSizesSection(this)">
                                                    <label class="form-check-label" for="hasSizes">تفعيل المقاسات</label>
                                                </div>
                                            </div>
                                            <div id="sizesSection" class="{{ $product->sizes->count() > 0 ? 'section-expanded' : 'section-collapsed' }}">
                                                @error('sizes.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('size_ids.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('size_available.*')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="sizesContainer">
                                                    @foreach($product->sizes as $size)
                                                    <div class="input-group mb-2 shadow-sm">
                                                        <input type="hidden" name="size_ids[]" value="{{ $size->id }}">
                                                        <input type="text" name="sizes[]" class="form-control" placeholder="المقاس" value="{{ $size->size }}">
                                                        <input type="number" name="size_prices[]" class="form-control" placeholder="السعر" step="0.01" value="{{ $size->price }}">
                                                        <div class="input-group-text">
                                                            <label class="mb-0">
                                                                <input type="checkbox" name="size_available[]" value="1" {{ $size->is_available ? 'checked' : '' }} class="me-1">
                                                                متوفر
                                                            </label>
                                                        </div>
                                                        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm" onclick="addSizeInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة مقاس
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Options -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-cog text-primary me-2"></i>
                                                خيارات المنتج
                                            </h5>
                                            <div class="row g-3">
                                                <!-- Color Selection Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="enable_color_selection" name="enable_color_selection"
                                                            value="1" {{ $product->enable_color_selection ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="enable_color_selection">
                                                            <i class="fas fa-palette me-2"></i>
                                                            السماح باختيار اللون
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Custom Color Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="enable_custom_color" name="enable_custom_color"
                                                            value="1" {{ $product->enable_custom_color ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="enable_custom_color">
                                                            <i class="fas fa-paint-brush me-2"></i>
                                                            السماح بإضافة لون مخصص
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Size Selection Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="enable_size_selection" name="enable_size_selection"
                                                            value="1" {{ $product->enable_size_selection ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="enable_size_selection">
                                                            <i class="fas fa-ruler me-2"></i>
                                                            السماح باختيار المقاسات المحددة
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Custom Size Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="enable_custom_size" name="enable_custom_size"
                                                            value="1" {{ $product->enable_custom_size ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="enable_custom_size">
                                                            <i class="fas fa-ruler-combined me-2"></i>
                                                            السماح بإضافة مقاس مخصص
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>ملاحظة:</strong> هذه الإعدادات تتحكم في الخيارات المتاحة للعملاء عند طلب هذا المنتج.
                                            </div>
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
@endsection

@section('scripts')
<script>
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
</script>
@endsection
