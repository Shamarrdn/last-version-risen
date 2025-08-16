@extends($adminLayout)

@section('title', 'إضافة مخزون جديد')
@section('page_title', 'إضافة مخزون جديد')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="inventory-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-plus text-primary me-2"></i>
                                            إضافة مخزون جديد
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للمخزون
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <h5 class="alert-heading mb-2">يوجد أخطاء في النموذج:</h5>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('admin.inventory.store') }}" method="POST">
                            @csrf

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                معلومات المخزون
                                            </h5>
                                            
                                            <div class="mb-3">
                                                <label for="product_id" class="form-label required">المنتج</label>
                                                <select id="product_id" name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                                    <option value="">اختر المنتج</option>
                                                    @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('product_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="size_id" class="form-label">المقاس</label>
                                                <select id="size_id" name="size_id" class="form-select @error('size_id') is-invalid @enderror">
                                                    <option value="">بدون مقاس</option>
                                                    @foreach($sizes as $size)
                                                    <option value="{{ $size->id }}" {{ old('size_id') == $size->id ? 'selected' : '' }}>
                                                        {{ $size->name }} {{ $size->description ? '- ' . $size->description : '' }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('size_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">اترك هذا الحقل فارغاً إذا كان المنتج لا يحتوي على مقاسات</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="color_id" class="form-label">اللون</label>
                                                <select id="color_id" name="color_id" class="form-select @error('color_id') is-invalid @enderror">
                                                    <option value="">بدون لون</option>
                                                    @foreach($colors as $color)
                                                    <option value="{{ $color->id }}" {{ old('color_id') == $color->id ? 'selected' : '' }}>
                                                        {{ $color->name }} {{ $color->description ? '- ' . $color->description : '' }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('color_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">اترك هذا الحقل فارغاً إذا كان المنتج لا يحتوي على ألوان</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-boxes text-primary me-2"></i>
                                                تفاصيل المخزون
                                            </h5>
                                            
                                            <div class="mb-3">
                                                <label for="stock" class="form-label required">الكمية المتاحة</label>
                                                <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0" required>
                                                @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="price" class="form-label">السعر (اختياري)</label>
                                                <div class="input-group">
                                                    <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" min="0" step="0.01">
                                                    <span class="input-group-text">ر.س</span>
                                                </div>
                                                @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">اترك هذا الحقل فارغاً لاستخدام السعر الأساسي للمنتج</small>
                                            </div>
                                            
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>ملاحظة:</strong> إذا كان المنتج لا يحتوي على مقاسات أو ألوان، يمكنك ترك حقول المقاس واللون فارغة.
                                            </div>
                                        </div>
                                    </div>
</div>

                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                حفظ المخزون
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