@extends($adminLayout)

@section('title', 'خصومات الكمية')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-percentage me-2"></i>
                    خصومات الكمية
                </h3>
                <a href="{{ route('admin.quantity-discounts.create') }}"
                   class="btn btn-light btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>
                    إضافة خصم جديد
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-body">
            <div class="discount-table-container">
                <div class="table-responsive">
                    <table class="table table-hover align-middle discount-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-end">المنتج</th>
                                <th class="text-center">نطاق الكمية</th>
                                <th class="text-center">نوع الخصم</th>
                                <th class="text-center">قيمة الخصم</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discounts as $discount)
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            @if($discount->product->image)
                                                <img src="{{ asset('storage/' . $discount->product->image) }}"
                                                     alt="{{ $discount->product->name }}"
                                                     class="product-thumbnail">
                                            @endif
                                            <div class="product-details">
                                                <h6 class="mb-0">{{ $discount->product->name }}</h6>
                                                <small class="text-muted">#{{ $discount->product->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="quantity-range">
                                            {{ $discount->min_quantity }} - {{ $discount->max_quantity ?? '∞' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="{{ $discount->type === 'percentage' ? 'badge-percentage' : 'badge-fixed' }}">
                                            {{ $discount->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="discount-value {{ $discount->type === 'percentage' ? 'percentage' : 'fixed' }}">
                                            {{ $discount->type === 'percentage' ? $discount->value . '%' : number_format($discount->value, 2) . ' ريال' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch discount-toggle d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                   {{ $discount->is_active ? 'checked' : '' }}
                                                   disabled>
                                            <label class="form-check-label me-3">
                                                {{ $discount->is_active ? 'مفعل' : 'معطل' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.quantity-discounts.show', $discount) }}"
                                               class="btn btn-sm btn-primary"
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.quantity-discounts.edit', $discount) }}"
                                               class="btn btn-sm btn-warning"
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.quantity-discounts.destroy', $discount) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الخصم؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="discount-empty-state">
                                            <i class="fas fa-percentage"></i>
                                            <h4>لا توجد خصومات كمية مضافة حالياً</h4>
                                            <p>قم بإضافة خصومات الكمية لتشجيع العملاء على شراء كميات أكبر</p>
                                            <a href="{{ route('admin.quantity-discounts.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus-circle me-1"></i> إضافة خصم جديد
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $discounts->links() }}
            </div>
        </div>
    </div>
</div>

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/quantity-discounts.css') }}">
@endsection

@section('scripts')
<script>
    // تفعيل tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.forEach(function(element) {
            new bootstrap.Tooltip(element);
        });
    });
</script>
@endsection
@endsection
