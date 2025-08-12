@extends($adminLayout)

@section('title', 'تفاصيل كوبون الخصم')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>
                    تفاصيل كوبون الخصم
                </h3>
                    <div>
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-light">
                        <i class="fas fa-edit me-1"></i> تعديل
                        </a>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-right me-1"></i> العودة إلى القائمة
                        </a>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="coupon-details-header">
                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <div class="coupon-details-code-container">
                            <label class="form-label text-muted mb-2">كود الخصم</label>
                            <span class="coupon-details-code">{{ $coupon->code }}</span>
                        </div>
                    </div>
                </div>
            </div>

                    <div class="row">
                        <div class="col-md-6">
                    <div class="coupon-details-section">
                        <h5 class="form-section-title">معلومات الكوبون الأساسية</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                        <tr>
                                        <td class="coupon-details-label">اسم الكوبون</td>
                                        <td class="coupon-details-value">{{ $coupon->name }}</td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">الوصف</td>
                                        <td class="coupon-details-value">{{ $coupon->description ?: 'لا يوجد وصف' }}</td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">نوع الخصم</td>
                                            <td>
                                                @if ($coupon->type == 'percentage')
                                                <span class="badge-percentage">نسبة مئوية</span>
                                                @else
                                                <span class="badge-fixed">قيمة ثابتة</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">قيمة الخصم</td>
                                            <td>
                                            <div class="coupon-value {{ $coupon->type == 'percentage' ? 'percentage' : 'fixed' }}">
                                                @if ($coupon->type == 'percentage')
                                                    {{ $coupon->value }}%
                                                @else
                                                    {{ $coupon->value }} ريال
                                                @endif
                                            </div>
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">الحد الأدنى للطلب</td>
                                        <td class="coupon-details-value">
                                                @if ($coupon->min_order_amount)
                                                    <strong>{{ $coupon->min_order_amount }} ريال</strong>
                                                @else
                                                    <span class="text-muted">لا يوجد حد أدنى</span>
                                                @endif
                                            </td>
                                        </tr>
                                </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                    <div class="coupon-details-section">
                        <h5 class="form-section-title">معلومات الصلاحية والاستخدام</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                        <tr>
                                        <td class="coupon-details-label">الحالة</td>
                                            <td>
                                                @if ($coupon->is_active)
                                                <div class="coupon-status active">نشط</div>
                                                @else
                                                <div class="coupon-status inactive">غير نشط</div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">تاريخ البدء</td>
                                        <td class="coupon-details-value">
                                                @if ($coupon->starts_at)
                                                    {{ $coupon->starts_at->format('Y-m-d') }}
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">تاريخ الانتهاء</td>
                                        <td class="coupon-details-value">
                                                @if ($coupon->expires_at)
                                                    {{ $coupon->expires_at->format('Y-m-d') }}
                                                @else
                                                    <span class="text-muted">غير محدد</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">الحد الأقصى للاستخدام</td>
                                        <td class="coupon-details-value">
                                                @if ($coupon->max_uses)
                                                    {{ $coupon->max_uses }} مرة
                                                @else
                                                    <span class="text-muted">غير محدود</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">عدد مرات الاستخدام</td>
                                        <td class="coupon-details-value">
                                                <strong>{{ $coupon->used_count }} مرة</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                        <td class="coupon-details-label">تاريخ الإنشاء</td>
                                        <td class="coupon-details-value">{{ $coupon->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

            <div class="coupon-details-section">
                <h5 class="form-section-title">حالة الصلاحية</h5>

                <div class="card-body {{ $coupon->isValid() ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border-start border-4 {{ $coupon->isValid() ? 'border-success' : 'border-danger' }} rounded-end p-3">
                    <div class="d-flex align-items-center">
                        <i class="fas {{ $coupon->isValid() ? 'fa-check-circle' : 'fa-times-circle' }} fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-1">{{ $coupon->isValid() ? 'الكوبون صالح للاستخدام' : 'الكوبون غير صالح للاستخدام' }}</h5>
                            @if (!$coupon->isValid())
                                <ul class="mb-0 mt-2">
                                    @if (!$coupon->is_active)
                                        <li>الكوبون غير نشط</li>
                                    @endif
                                    @if ($coupon->starts_at && now()->lt($coupon->starts_at))
                                        <li>لم يبدأ وقت الكوبون بعد</li>
                                    @endif
                                    @if ($coupon->expires_at && now()->gt($coupon->expires_at))
                                        <li>انتهت صلاحية الكوبون</li>
                                    @endif
                                    @if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses)
                                        <li>تم استنفاذ الحد الأقصى لاستخدام الكوبون</li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>
                        </div>
                    </div>

            <div class="action-buttons">
                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> تعديل الكوبون
                </a>
                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                        <i class="fas fa-trash me-1"></i> حذف الكوبون
                            </button>
                        </form>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>

            <!-- Linked products section -->
            @if(!$coupon->applies_to_all_products)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4 d-flex align-items-center">
                            <i class="fas fa-box text-primary me-2"></i>
                            المنتجات المرتبطة
                        </h5>

                        @if($coupon->products->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>اسم المنتج</th>
                                            <th>السعر</th>
                                            <th>التصنيف</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($coupon->products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td>{{ $product->price }}</td>
                                            <td>{{ $product->category->name ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                لا يوجد منتجات مرتبطة بهذا الكوبون
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Linked categories section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4 d-flex align-items-center">
                            <i class="fas fa-tags text-primary me-2"></i>
                            التصنيفات المرتبطة
                        </h5>

                        @if($coupon->categories->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>اسم التصنيف</th>
                                            <th>الوصف</th>
                                            <th>عدد المنتجات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($coupon->categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.categories.show', $category) }}">
                                                    {{ $category->name }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-success rounded-pill">
                                                    {{ $category->products_count ?? $category->products->count() }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                لا يوجد تصنيفات مرتبطة بهذا الكوبون
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-success">
                    هذا الكوبون يطبق على جميع المنتجات
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/coupons.css') }}">
<style>
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .border-success {
        border-color: rgba(25, 135, 84, 0.5) !important;
    }

    .border-danger {
        border-color: rgba(220, 53, 69, 0.5) !important;
    }
</style>
@endsection
