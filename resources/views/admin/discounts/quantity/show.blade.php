@extends($adminLayout)

@section('title', 'تفاصيل خصم الكمية')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-dark text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            تفاصيل خصم الكمية
                        </h3>
                        <div class="btn-group">
                            <a href="{{ route('admin.quantity-discounts.edit', $discount) }}"
                               class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>
                                تعديل
                            </a>
                            <a href="{{ route('admin.quantity-discounts.index') }}"
                               class="btn btn-light">
                                <i class="fas fa-arrow-right me-2"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <!-- معلومات المنتج -->
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        @if($discount->product->image)
                                            <img src="{{ asset('storage/' . $discount->product->image) }}"
                                                 alt="{{ $discount->product->name }}"
                                                 class="rounded-circle me-3"
                                                 style="width: 64px; height: 64px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <h5 class="mb-1">{{ $discount->product->name }}</h5>
                                            <p class="text-muted mb-0">#{{ $discount->product->id }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- نطاق الكمية -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">نطاق الكمية</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info fs-6 me-2">
                                            {{ $discount->min_quantity }}
                                        </span>
                                        <i class="fas fa-arrow-right me-2"></i>
                                        <span class="badge bg-info fs-6">
                                            {{ $discount->max_quantity ?? '∞' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- نوع وقيمة الخصم -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">تفاصيل الخصم</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge {{ $discount->type === 'percentage' ? 'bg-purple' : 'bg-primary' }} fs-6 me-2">
                                            {{ $discount->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
                                        </span>
                                        <span class="fs-5 fw-bold {{ $discount->type === 'percentage' ? 'text-purple' : 'text-primary' }}">
                                            {{ $discount->type === 'percentage' ? $discount->value . '%' : number_format($discount->value, 2) . ' ريال' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الحالة -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">الحالة</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               {{ $discount->is_active ? 'checked' : '' }}
                                               disabled>
                                        <label class="form-check-label">
                                            {{ $discount->is_active ? 'مفعل' : 'معطل' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- التواريخ -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">التواريخ</h6>
                                    <div class="d-flex flex-column">
                                        <small class="text-muted mb-1">
                                            <i class="fas fa-calendar-plus me-2"></i>
                                            تاريخ الإنشاء: {{ $discount->created_at->format('Y-m-d H:i') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-check me-2"></i>
                                            آخر تحديث: {{ $discount->updated_at->format('Y-m-d H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ملخص الخصم -->
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">ملخص الخصم</h6>
                                    <p class="card-text">
                                        عند شراء <span class="fw-bold">{{ $discount->min_quantity }}</span> قطعة
                                        @if($discount->max_quantity)
                                            إلى <span class="fw-bold">{{ $discount->max_quantity }}</span> قطعة
                                        @else
                                            أو أكثر
                                        @endif
                                        من منتج <span class="fw-bold">{{ $discount->product->name }}</span>،
                                        سيتم تطبيق خصم
                                        <span class="fw-bold {{ $discount->type === 'percentage' ? 'text-purple' : 'text-primary' }}">
                                            {{ $discount->type === 'percentage' ? 'بنسبة ' . $discount->value . '%' : 'بقيمة ' . number_format($discount->value, 2) . ' ريال' }}
                                        </span>
                                        {{ $discount->is_active ? 'حالياً' : 'عند التفعيل' }}.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .badge {
        padding: 0.5rem 1rem;
    }
    .form-switch {
        padding-right: 2.5em;
    }
    .form-switch .form-check-input {
        width: 3em;
        margin-right: -2.5em;
    }
</style>
@endpush
@endsection
