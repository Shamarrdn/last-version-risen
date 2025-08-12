@extends($adminLayout)

@section('title', 'مخزون المنتجات')
@section('page_title', 'مراقبة المخزون')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 d-flex align-items-center">
                                <span class="icon-circle bg-primary text-white me-2">
                                    <i class="fas fa-warehouse"></i>
                                </span>
                                حالة المخزون
                            </h5>
                            <div class="actions">
                                <button class="btn btn-light-success btn-wave" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>طباعة
                                </button>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-wave">
                                    <i class="fas fa-plus-circle me-2"></i>إضافة منتج
                                </a>
                            </div>
                        </div>

                        <div class="alert alert-warning m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>ملاحظة:</strong>
                            <ul class="mb-0 mt-2">
                                <li>المنتجات المميزة باللون <span class="text-danger fw-bold">الأحمر</span> نفذ مخزونها تماماً</li>
                                <li>المنتجات المميزة باللون <span class="text-warning fw-bold">البرتقالي</span> يقل مخزونها عن 20 قطعة</li>
                                <li>المنتجات المميزة باللون <span class="text-info fw-bold">الأزرق</span> يقل مخزونها عن 30% من إجمالي المخزون</li>
                            </ul>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="border-top-0">#</th>
                                            <th class="border-top-0">اسم المنتج</th>
                                            <th class="border-top-0">إجمالي المخزون</th>
                                            <th class="border-top-0">المستهلك</th>
                                            <th class="border-top-0">المتاح</th>
                                            <th class="border-top-0">نسبة الاستهلاك</th>
                                            <th class="border-top-0">حالة المنتج</th>
                                            <th class="border-top-0">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $index => $product)
                                        <tr class="{{ $product['status'] === 'out_of_stock' ? 'table-danger' : ($product['status'] === 'low' ? 'table-warning' : ($product['status'] === 'medium' ? 'table-info' : '')) }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product['name'] }}</td>
                                            <td>
                                                <span class="fw-bold">{{ $product['total_stock'] }} قطعة</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $product['consumed_stock'] }} قطعة</span>
                                            </td>
                                            <td>
                                                @if($product['status'] === 'out_of_stock')
                                                    <span class="text-danger fw-bold">0 قطعة</span>
                                                @elseif($product['status'] === 'low')
                                                    <span class="text-warning fw-bold">{{ $product['available_stock'] }} قطعة</span>
                                                @elseif($product['status'] === 'medium')
                                                    <span class="text-info fw-bold">{{ $product['available_stock'] }} قطعة</span>
                                                @else
                                                    <span class="text-success fw-bold">{{ $product['available_stock'] }} قطعة</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $product['consumption_percentage'] > 80 ? 'bg-danger' : ($product['consumption_percentage'] > 60 ? 'bg-warning' : 'bg-success') }}"
                                                         role="progressbar"
                                                         style="width: {{ $product['consumption_percentage'] }}%"
                                                         aria-valuenow="{{ $product['consumption_percentage'] }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $product['consumption_percentage'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($product['is_available'])
                                                    <span class="badge bg-success">متاح</span>
                                                @else
                                                    <span class="badge bg-danger">غير متاح</span>
                                                @endif

                                                @if($product['status'] === 'out_of_stock')
                                                    <span class="badge bg-danger text-white me-1">نفذ المخزون</span>
                                                @elseif($product['status'] === 'low')
                                                    <span class="badge bg-warning text-dark me-1">مخزون منخفض</span>
                                                @elseif($product['status'] === 'medium')
                                                    <span class="badge bg-info text-white me-1">مخزون متوسط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.products.edit', $product['slug']) }}" class="btn btn-sm btn-light-primary me-2" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.products.show', $product['slug']) }}" class="btn btn-sm btn-light-info" title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-icon bg-light rounded-circle mb-3">
                                                        <i class="fas fa-box-open text-muted fa-2x"></i>
                                                    </div>
                                                    <h5 class="text-muted">لا توجد منتجات</h5>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ملخص حالة المخزون -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-muted">المنتجات المتوفرة</h6>
                                    <h3 class="mb-0">{{ $products->where('is_available', true)->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-danger text-white me-3">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-muted">نفذ المخزون</h6>
                                    <h3 class="mb-0">{{ $products->where('status', 'out_of_stock')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-muted">مخزون منخفض</h6>
                                    <h3 class="mb-0">{{ $products->where('status', 'low')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-muted">مخزون متوسط</h6>
                                    <h3 class="mb-0">{{ $products->where('status', 'medium')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .icon-circle {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.2rem;
    }

    /* تنسيق لصفحة الطباعة */
    @media print {
        .actions, .btn, nav, .sidebar, header, .page-header, footer {
            display: none !important;
        }

        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: none !important;
        }

        .table-responsive {
            overflow-x: visible !important;
        }

        .table-danger td {
            background-color: #ffcccc !important;
            color: #dc3545 !important;
        }

        .table-warning td {
            background-color: #fff3cd !important;
            color: #856404 !important;
        }

        .table-info td {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
        }

        .progress {
            border: 1px solid #ddd !important;
        }

        .progress-bar {
            border: none !important;
        }
    }
</style>
@endsection
