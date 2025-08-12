@extends($adminLayout)

@section('title', 'إنشاء كوبون خصم جديد')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>
                    إنشاء كوبون خصم جديد
                </h3>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-right me-1"></i> العودة إلى القائمة
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf

                @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="coupon-form-section">
                            <h5 class="form-section-title">معلومات الكوبون الأساسية</h5>

                            <div class="form-group mb-3">
                                <label for="code" class="form-label required">كود الخصم</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ old('code') }}" required placeholder="مثال: SALE2023">
                                    <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                        <i class="fas fa-sync-alt"></i> توليد
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item generate-code" data-type="alphanumeric" data-length="8" href="#">حروف وأرقام (8 أحرف)</a></li>
                                        <li><a class="dropdown-item generate-code" data-type="alphanumeric" data-length="10" href="#">حروف وأرقام (10 أحرف)</a></li>
                                        <li><a class="dropdown-item generate-code" data-type="alphabetic" data-length="8" href="#">حروف فقط (8 أحرف)</a></li>
                                        <li><a class="dropdown-item generate-code" data-type="numeric" data-length="8" href="#">أرقام فقط (8 أرقام)</a></li>
                                    </ul>
                                </div>
                                <small class="form-text text-muted">أدخل كود الخصم الذي سيستخدمه العملاء</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="name" class="form-label required">اسم الكوبون</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" required placeholder="مثال: خصم شهر رمضان">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description" class="form-label">الوصف (اختياري)</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="وصف مختصر للكوبون وكيفية استخدامه">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label required">نوع الخصم</label>
                                <div class="coupon-type-selector">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="type_percentage"
                                            value="percentage" {{ old('type') != 'fixed' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_percentage">
                                            <span class="badge-percentage">نسبة مئوية (%)</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="type_fixed"
                                            value="fixed" {{ old('type') == 'fixed' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_fixed">
                                            <span class="badge-fixed">قيمة ثابتة (ريال)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="value" class="form-label required">قيمة الخصم</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-percent" id="value_icon"></i></span>
                                    <input type="number" class="form-control" id="value" name="value"
                                        value="{{ old('value') }}" step="0.01" min="0" required>
                                    <span class="input-group-text" id="value_suffix">%</span>
                                </div>
                                <small class="form-text text-muted" id="value_help">
                                    أدخل نسبة الخصم (على سبيل المثال: 10 للحصول على خصم 10%)
                                </small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="min_order_amount" class="form-label">الحد الأدنى للطلب (اختياري)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-shopping-cart"></i></span>
                                    <input type="number" class="form-control" id="min_order_amount" name="min_order_amount"
                                        value="{{ old('min_order_amount') }}" step="0.01" min="0">
                                    <span class="input-group-text">ريال</span>
                                </div>
                                <small class="form-text text-muted">
                                    إذا كنت تريد تطبيق الكوبون فقط على الطلبات التي تتجاوز مبلغًا معينًا
                                </small>
                            </div>

                            <!-- Products Selection -->
                            <div class="row mb-4" id="productsSection" style="{{ old('applies_to_all_products') ? 'display: none;' : '' }}">
                                <label class="col-md-3 col-form-label">المنتجات</label>
                                <div class="col-md-9">
                                    <div class="product-checkbox-container border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                        @foreach($products as $product)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="products[]"
                                                    id="product_{{ $product->id }}" value="{{ $product->id }}"
                                                    {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="product_{{ $product->id }}">
                                                    {{ $product->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="form-text text-muted">اختر المنتجات التي يمكن استخدام الكوبون عليها</small>

                                    @error('products')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Categories Selection -->
                            <div class="row mb-4" id="categoriesSection" style="{{ old('applies_to_all_products') ? 'display: none;' : '' }}">
                                <label class="col-md-3 col-form-label">التصنيفات</label>
                                <div class="col-md-9">
                                    <div class="category-checkbox-container border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                        @foreach($categories as $category)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="categories[]"
                                                    id="category_{{ $category->id }}" value="{{ $category->id }}"
                                                    {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="category_{{ $category->id }}">
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="form-text text-muted">اختر التصنيفات التي يمكن استخدام الكوبون على منتجاتها</small>

                                    @error('categories')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="coupon-form-section">
                            <h5 class="form-section-title">إعدادات الصلاحية والاستخدام</h5>

                            <div class="form-group mb-3">
                                <label class="form-label">الحالة</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        كوبون نشط (متاح للاستخدام)
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="starts_at" class="form-label">تاريخ البدء (اختياري)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" class="form-control" id="starts_at" name="starts_at"
                                        value="{{ old('starts_at') }}">
                                </div>
                                <small class="form-text text-muted">
                                    إذا تركت هذا الحقل فارغًا، سيكون الكوبون صالحًا من الآن
                                </small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="expires_at" class="form-label">تاريخ الانتهاء (اختياري)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                                    <input type="date" class="form-control" id="expires_at" name="expires_at"
                                        value="{{ old('expires_at') }}">
                                </div>
                                <small class="form-text text-muted">
                                    إذا تركت هذا الحقل فارغًا، لن تنتهي صلاحية الكوبون تلقائيًا
                                </small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="max_uses" class="form-label">الحد الأقصى للاستخدام (اختياري)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-times-circle"></i></span>
                                    <input type="number" class="form-control" id="max_uses" name="max_uses"
                                        value="{{ old('max_uses') }}" min="0">
                                </div>
                                <small class="form-text text-muted">
                                    أقصى عدد مرات يمكن استخدام هذا الكوبون. اتركه فارغًا للاستخدام غير المحدود
                                </small>
                            </div>

                    
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> إنشاء الكوبون
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-light">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/coupons.css') }}">
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تغيير أيقونة ولاحقة حقل القيمة بناءً على نوع الخصم
        const typePercentage = document.getElementById('type_percentage');
        const typeFixed = document.getElementById('type_fixed');
        const valueIcon = document.getElementById('value_icon');
        const valueSuffix = document.getElementById('value_suffix');
        const valueHelp = document.getElementById('value_help');

        function updateValueField() {
            if (typePercentage.checked) {
                valueIcon.className = 'fas fa-percent';
                valueSuffix.textContent = '%';
                valueHelp.textContent = 'أدخل نسبة الخصم (على سبيل المثال: 10 للحصول على خصم 10%)';
                document.getElementById('value').setAttribute('max', '100');
            } else {
                valueIcon.className = 'fas fa-money-bill-wave';
                valueSuffix.textContent = 'ريال';
                valueHelp.textContent = 'أدخل قيمة الخصم بالريال (على سبيل المثال: 50 للحصول على خصم 50 ريال)';
                document.getElementById('value').removeAttribute('max');
            }
        }

        typePercentage.addEventListener('change', updateValueField);
        typeFixed.addEventListener('change', updateValueField);

        // توليد كود عشوائي عبر واجهة برمجة التطبيقات
        function generateCouponCode(type = 'alphanumeric', length = 8) {
            // عرض مؤشر التحميل أو تعطيل الزر
            const generateBtn = document.getElementById('generateCode');
            const originalBtnHtml = generateBtn.innerHTML;
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            generateBtn.disabled = true;

            // إنشاء نموذج بيانات
            const formData = new FormData();
            formData.append('type', type);
            formData.append('length', length);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("admin.coupons.generate-code") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('code').value = data.code;
                } else {
                    console.error('Error generating code:', data.message);
                    alert('حدث خطأ أثناء إنشاء الكود');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء إنشاء الكود');
            })
            .finally(() => {
                // استعادة حالة الزر
                generateBtn.innerHTML = originalBtnHtml;
                generateBtn.disabled = false;
            });
        }

        // زر توليد الكود الرئيسي
        document.getElementById('generateCode').addEventListener('click', function() {
            generateCouponCode('alphanumeric', 8);
        });

        // أزرار القائمة المنسدلة
        document.querySelectorAll('.generate-code').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const type = this.getAttribute('data-type');
                const length = parseInt(this.getAttribute('data-length'));
                generateCouponCode(type, length);
            });
        });

        // تنفيذ عند تحميل الصفحة
        updateValueField();

        // إخفاء/إظهار قسم المنتجات حسب خيار التطبيق على جميع المنتجات
        const appliesToAllCheckbox = document.getElementById('applies_to_all_products');
        const productsSection = document.getElementById('productsSection');
        const categoriesSection = document.getElementById('categoriesSection');

        appliesToAllCheckbox.addEventListener('change', function() {
            if (this.checked) {
                productsSection.style.display = 'none';
                categoriesSection.style.display = 'none';
            } else {
                productsSection.style.display = '';
                categoriesSection.style.display = '';
            }
        });

        // تهيئة Select2 للحقول الأخرى التي تحتاج إليها (إن وجدت)
        $('.select2:not([name="products[]"]):not([name="categories[]"])').select2({
            placeholder: 'اختر...',
            width: '100%',
            dir: 'rtl'
        });
    });
</script>
@endsection
