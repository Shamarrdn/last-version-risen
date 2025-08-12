@extends($adminLayout)

@section('title', 'إدارة كوبونات الخصم')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>
                    كوبونات الخصم
                </h3>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>
                    إضافة كوبون جديد
                </a>
            </div>
        </div>

        <!-- /.card-header -->
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="coupon-table-container">
                <div class="table-responsive">
                    <table class="table table-hover align-middle coupon-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-end">الكود</th>
                                <th class="text-end">الاسم</th>
                                <th class="text-end">النوع</th>
                                <th class="text-end">القيمة</th>
                                <th class="text-end">الحد الأدنى للطلب</th>
                                <th class="text-center">تاريخ البدء</th>
                                <th class="text-center">تاريخ الانتهاء</th>
                                <th class="text-center">عدد الاستخدامات</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coupons as $coupon)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><span class="coupon-code-cell">{{ $coupon->code }}</span></td>
                                    <td>{{ $coupon->name }}</td>
                                    <td>
                                        @if ($coupon->type == 'percentage')
                                            <span class="badge-percentage">نسبة مئوية</span>
                                        @else
                                            <span class="badge-fixed">قيمة ثابتة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold {{ $coupon->type == 'percentage' ? 'text-info' : 'text-primary' }}">
                                            @if ($coupon->type == 'percentage')
                                                {{ $coupon->value }}%
                                            @else
                                                {{ $coupon->value }} ريال
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($coupon->min_order_amount)
                                            <div class="fw-semibold">{{ $coupon->min_order_amount }} ريال</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($coupon->starts_at)
                                            <div class="small">{{ $coupon->starts_at->format('Y-m-d') }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($coupon->expires_at)
                                            <div class="small">{{ $coupon->expires_at->format('Y-m-d') }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($coupon->max_uses)
                                            <div class="small fw-semibold">{{ $coupon->used_count }} / {{ $coupon->max_uses }}</div>
                                        @else
                                            <div class="small fw-semibold">{{ $coupon->used_count }} / ∞</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($coupon->is_active)
                                            <div class="coupon-status active">نشط</div>
                                        @else
                                            <div class="coupon-status inactive">غير نشط</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.coupons.show', $coupon->id) }}" class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11">
                                        <div class="coupon-empty-state">
                                            <i class="fas fa-ticket-alt"></i>
                                            <h4>لا توجد كوبونات حتى الآن</h4>
                                            <p>قم بإضافة كوبونات الخصم لتشجيع العملاء على الشراء</p>
                                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus-circle me-1"></i> إضافة كوبون جديد
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
                {{ $coupons->links() }}
            </div>
        </div>
        <!-- /.card-body -->
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/coupons.css') }}">
<style>
    .coupon-code-cell {
        font-family: monospace;
        font-weight: 600;
        font-size: 0.9rem;
        background-color: #f8f9fa;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
        display: inline-block;
        color: #0d6efd;
        letter-spacing: 1px;
    }

    .coupon-table th {
        padding: 1rem;
        font-weight: 600;
        color: #4B5563;
    }

    .coupon-status {
        width: 100px;
        margin: 0 auto;
        font-size: 0.875rem;
    }
</style>
@endsection
