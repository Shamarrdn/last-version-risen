@extends($adminLayout)

@section('title', 'طلباتي المخصصة')
@section('page_title', 'طلباتي المخصصة')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="orders-container">
                        <!-- Page Header -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-1">
                                                    <i class="fas fa-user-check text-primary me-2"></i>
                                                    طلباتي المخصصة
                                                </h4>
                                                <p class="text-muted mb-0">الطلبات التي تم تخصيصها لك للمتابعة</p>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-arrow-right me-1"></i>
                                                    العودة لجميع الطلبات
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-primary me-3">
                                                <i class="fas fa-clipboard-list fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">طلباتي الإجمالية</h6>
                                                <h3 class="text-white mb-0">{{ $stats['total_orders'] }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-success h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-success me-3">
                                                <i class="fas fa-check-circle fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">الطلبات المكتملة</h6>
                                                <h3 class="text-white mb-0">{{ $stats['completed_orders'] }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-warning h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-warning me-3">
                                                <i class="fas fa-clock fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">قيد التنفيذ</h6>
                                                <h3 class="text-white mb-0">{{ $stats['processing_orders'] }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-info me-3">
                                                <i class="fas fa-dollar-sign fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي المبيعات</h6>
                                                <h3 class="text-white mb-0">{{ number_format($stats['total_revenue'], 2) }} ريال</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <form method="GET" action="{{ route('admin.orders.assigned') }}" class="row g-3 align-items-end">
                                            <div class="col-md-3">
                                                <div class="search-wrapper">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0">
                                                            <i class="fas fa-hashtag text-muted"></i>
                                                        </span>
                                                        <input type="text"
                                                               name="order_number"
                                                               class="form-control border-0 shadow-none ps-0"
                                                               placeholder="البحث برقم الطلب..."
                                                               value="{{ request('order_number') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="search-wrapper">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-0">
                                                            <i class="fas fa-search text-muted"></i>
                                                        </span>
                                                        <input type="text"
                                                               name="search"
                                                               class="form-control border-0 shadow-none ps-0"
                                                               placeholder="البحث في العملاء..."
                                                               value="{{ request('search') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <select name="order_status" class="form-select border-0 shadow-none bg-light">
                                                    <option value="">كل الحالات</option>
                                                    @foreach($orderStatuses as $value => $label)
                                                        <option value="{{ $value }}" {{ request('order_status') == $value ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2 d-flex align-items-center">
                                                <span class="me-2">من</span>
                                                <input type="date"
                                                       name="date_from"
                                                       class="form-control border-0 shadow-none bg-light"
                                                       placeholder="من تاريخ"
                                                       value="{{ request('date_from') }}">
                                            </div>

                                            <div class="col-md-2 d-flex align-items-center">
                                                <span class="me-2">إلى</span>
                                                <input type="date"
                                                       name="date_to"
                                                       class="form-control border-0 shadow-none bg-light"
                                                       placeholder="إلى تاريخ"
                                                       value="{{ request('date_to') }}">
                                            </div>

                                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search me-1"></i>فلترة
                                                </button>
                                                <a href="{{ route('admin.orders.assigned') }}" class="btn btn-light">
                                                    <i class="fas fa-times me-1"></i>مسح
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders List -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-0">
                                        <div class="table-wrapper">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th>العميل</th>
                                                            <th>المنتجات</th>
                                                            <th>السعر الأصلي</th>
                                                            <th>خصم الكمية</th>
                                                            <th>خصم الكوبون</th>
                                                            <th>الإجمالي</th>
                                                            <th>حالة الطلب</th>
                                                            <th>حالة الدفع</th>
                                                            <th>تاريخ الاستلام</th>
                                                            <th>التاريخ</th>
                                                            <th>الإجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($orders as $order)
                                                        <tr>
                                                            <td class="text-center">{{ $order['order_number'] }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-0">{{ $order['customer_name'] }}</h6>
                                                                        <small class="text-muted">{{ $order['customer_phone'] }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="small">
                                                                    @foreach($order['items'] as $item)
                                                                        <div class="mb-1">
                                                                            {{ $item['product_name'] }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                            <td>{{ number_format($order['original_amount'], 2) }} ريال</td>
                                                            <td>{{ number_format($order['quantity_discount'], 2) }} ريال</td>
                                                            <td>{{ number_format($order['coupon_discount'], 2) }} ريال</td>
                                                            <td>{{ number_format($order['total'], 2) }} ريال</td>
                                                            <td>
                                                                <span class="badge bg-{{ $order['status_color'] }}-subtle text-{{ $order['status_color'] }} rounded-pill">
                                                                    {{ $order['status_text'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $order['payment_status_color'] }}-subtle text-{{ $order['payment_status_color'] }} rounded-pill">
                                                                    {{ $order['payment_status_text'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small class="text-success">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    {{ $order['assigned_at'] }}
                                                                </small>
                                                            </td>
                                                            <td>{{ $order['created_at_formatted'] }}</td>
                                                            <td>
                                                                <div class="action-buttons d-flex gap-1">
                                                                    <a href="{{ route('admin.orders.show', $order['uuid']) }}"
                                                                       class="btn btn-light-info btn-sm"
                                                                       title="عرض التفاصيل">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    
                                                                    <button type="button" 
                                                                            class="btn btn-warning btn-sm unassign-order-btn"
                                                                            data-order-uuid="{{ $order['uuid'] }}"
                                                                            title="إلغاء الاستلام">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="12" class="text-center py-5">
                                                                <div class="empty-state">
                                                                    <div class="empty-icon bg-light rounded-circle mb-3">
                                                                        <i class="fas fa-clipboard-list text-muted fa-2x"></i>
                                                                    </div>
                                                                    <h5 class="text-muted mb-0">لا توجد طلبات مخصصة لك</h5>
                                                                    <p class="text-muted small mt-2">يمكنك استلام الطلبات من صفحة جميع الطلبات</p>
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
                        </div>

                        <!-- Pagination -->
                        @if(is_a($orders, \Illuminate\Pagination\LengthAwarePaginator::class) && $orders->hasPages())
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-center">
                                    {{ $orders->links() }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="/assets/css/admin/orders.css">
<style>
.stat-card {
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-3px);
}

.icon-circle {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.search-wrapper .input-group-text {
    border-right: 0;
}

.search-wrapper .form-control:focus {
    border-color: #ced4da;
    box-shadow: none;
}

.empty-state {
    text-align: center;
    padding: 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // CSRF token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Unassign Order Button
    $(document).on('click', '.unassign-order-btn', function() {
        const button = $(this);
        const orderUuid = button.data('order-uuid');
        const row = button.closest('tr');
        
        if (!confirm('هل أنت متأكد من إلغاء استلام هذا الطلب؟ سيعود الطلب إلى قائمة الطلبات غير المخصصة.')) {
            return;
        }
        
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `{{ url('/admin/orders') }}/${orderUuid}/unassign`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);
                    
                    // Remove the row from table
                    row.fadeOut(300, function() {
                        $(this).remove();
                        // Check if no more rows
                        if ($('tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    showAlert('error', response.message);
                    button.html('<i class="fas fa-times"></i>');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'حدث خطأ أثناء إلغاء استلام الطلب';
                showAlert('error', message);
                button.html('<i class="fas fa-times"></i>');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Alert function
    function showAlert(type, message) {
        const alertType = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertIcon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        
        const alert = $(`
            <div class="alert ${alertType} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="${alertIcon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
});
</script>
@endsection

