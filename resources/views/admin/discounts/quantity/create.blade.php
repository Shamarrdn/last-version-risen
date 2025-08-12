@extends($adminLayout)

@section('title', 'إضافة خصم كمية جديد')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white py-3" style="background-color: #212529; border-color: #212529;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            إضافة خصم كمية جديد
                        </h3>
                        <a href="{{ route('admin.quantity-discounts.index') }}"
                           class="btn btn-light">
                            <i class="fas fa-arrow-right me-2"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.quantity-discounts.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <!-- اختيار المنتج -->
                            <div class="col-12">
                                <div class="form-floating">
                                    <select name="product_id" id="product_id"
                                            class="form-select @error('product_id') is-invalid @enderror">
                                        <option value="">اختر المنتج</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="product_id">المنتج</label>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- نطاق الكمية -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" name="min_quantity" id="min_quantity"
                                           value="{{ old('min_quantity') }}"
                                           class="form-control @error('min_quantity') is-invalid @enderror"
                                           placeholder="الحد الأدنى">
                                    <label for="min_quantity">الحد الأدنى للكمية</label>
                                    @error('min_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" name="max_quantity" id="max_quantity"
                                           value="{{ old('max_quantity') }}"
                                           class="form-control @error('max_quantity') is-invalid @enderror"
                                           placeholder="الحد الأقصى">
                                    <label for="max_quantity">الحد الأقصى للكمية (اختياري)</label>
                                    @error('max_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- نوع وقيمة الخصم -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="type" id="type"
                                            class="form-select @error('type') is-invalid @enderror">
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                            نسبة مئوية
                                        </option>
                                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>
                                            مبلغ ثابت
                                        </option>
                                    </select>
                                    <label for="type">نوع الخصم</label>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" name="value" id="value"
                                           value="{{ old('value') }}"
                                           step="0.01"
                                           class="form-control @error('value') is-invalid @enderror"
                                           placeholder="قيمة الخصم">
                                    <label for="value">قيمة الخصم</label>
                                    <div class="input-group-text bg-light position-absolute start-0 top-0 h-100 border-0">
                                        <span id="value-suffix">%</span>
                                    </div>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- تفعيل الخصم -->
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="form-check-input" style="background-color: #212529; border-color: #212529;">
                                    <label class="form-check-label" for="is_active">تفعيل الخصم</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-lg px-5" style="background-color: #212529; border-color: #212529; color: white;">
                                <i class="fas fa-save me-2"></i>
                                حفظ الخصم
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/quantity-discounts.css') }}">
<style>
    .form-floating {
        position: relative;
    }
    .form-floating > .form-control,
    .form-floating > .form-select {
        height: calc(3.5rem + 2px);
        padding: 1rem 0.75rem;
    }
    .form-floating > label {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        padding: 1rem 0.75rem;
        pointer-events: none;
        border: 1px solid transparent;
        transform-origin: 100% 0;
        transition: opacity .1s ease-in-out,transform .1s ease-in-out;
    }
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label {
        opacity: .65;
        transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
    }
    .form-floating > .form-control:focus ~ label {
        color: #212529;
    }
    .form-floating > .form-control.is-invalid ~ label {
        color: #dc3545;
    }
    .form-switch {
        padding-right: 2.5em;
    }
    .form-switch .form-check-input {
        width: 3em;
        margin-right: -2.5em;
    }
    .form-check-input:checked {
        background-color: #212529 !important;
        border-color: #212529 !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #212529 !important;
        box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25) !important;
    }
    a {
        color: #212529;
        text-decoration: none;
    }
    a:hover {
        color: #000000;
    }
</style>
@endpush

@push('scripts')
<script>
    // تحديث لاحقة قيمة الخصم حسب النوع
    const typeSelect = document.getElementById('type');
    const valueSuffix = document.getElementById('value-suffix');

    function updateValueSuffix() {
        valueSuffix.textContent = typeSelect.value === 'percentage' ? '%' : 'ريال';
    }

    typeSelect.addEventListener('change', updateValueSuffix);
    updateValueSuffix(); // تحديث عند تحميل الصفحة
</script>
@endpush
@endsection
