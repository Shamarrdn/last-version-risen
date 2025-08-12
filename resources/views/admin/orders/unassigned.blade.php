@extends($adminLayout)

@section('title', 'الطلبات غير المخصصة')
@section('page_title', 'الطلبات غير المخصصة')

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
                                                    <i class="fas fa-clock me-2"></i>
                                                    الطلبات غير المخصصة
                                                </h4>
                                                <p class="text-muted mb-0">
                                                    الطلبات التي لم يتم تخصيصها لك أو مخصصة لأدمن آخر
                                                </p>
                                            </div>
                                            <div class="d-flex">
                                                <a href="{{ route('admin.orders.index') }}" class="btn btn-light-primary me-2">
                                                    <i class="fas fa-list me-1"></i> جميع الطلبات
                                                </a>
                                                <a href="{{ route('admin.orders.assigned') }}" class="btn btn-light-info">
                                                    <i class="fas fa-user-check me-1"></i> طلباتي المخصصة
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
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">إجمالي الطلبات</h6>
                                            <h3 class="mb-0">{{ $stats['total_orders'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-primary-subtle text-primary">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات المكتملة</h6>
                                            <h3 class="mb-0">{{ $stats['completed_orders'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-success-subtle text-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">قيد المعالجة</h6>
                                            <h3 class="mb-0">{{ $stats['processing_orders'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-warning-subtle text-warning">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">إجمالي الإيرادات</h6>
                                            <h3 class="mb-0">{{ number_format($stats['total_revenue'] ?? 0, 2) }} ر.س</h3>
                                        </div>
                                        <div class="icon-circle bg-info-subtle text-info">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Stats -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">غير مخصصة لأحد</h6>
                                            <h3 class="mb-0">{{ $stats['truly_unassigned'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-secondary-subtle text-secondary">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">مخصصة لأدمن آخر</h6>
                                            <h3 class="mb-0">{{ $stats['assigned_to_others'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-orange-subtle text-orange">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-filter me-2"></i>
                                    فلاتر البحث
                                </h5>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-light-primary btn-sm me-2" onclick="resetFilters()">
                                        <i class="fas fa-undo me-1"></i>
                                        إعادة تعيين
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.orders.unassigned') }}" class="row g-3">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="search" placeholder="بحث في رقم الطلب أو اسم العميل..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="order_status">
                                            <option value="">حالة الطلب</option>
                                            @foreach($orderStatuses as $value => $label)
                                                <option value="{{ $value }}" {{ request('order_status') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="payment_status">
                                            <option value="">حالة الدفع</option>
                                            @foreach($paymentStatuses as $value => $label)
                                                <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="date_from" placeholder="من تاريخ" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="date_to" placeholder="إلى تاريخ" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    قائمة الطلبات غير المخصصة
                                </h5>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">{{ $orders->count() ?? 0 }} طلب</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0">رقم الطلب</th>
                                                <th class="border-0">العميل</th>
                                                <th class="border-0">المنتجات</th>
                                                <th class="border-0">المبلغ</th>
                                                <th class="border-0">حالة الطلب</th>
                                                <th class="border-0">حالة الدفع</th>
                                                <th class="border-0">الأدمن المسؤول</th>
                                                <th class="border-0">التاريخ</th>
                                                <th class="border-0">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $order)
                                                <tr class="align-middle">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <i class="fas fa-shopping-cart text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <strong>{{ $order['order_number'] }}</strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $order['customer_name'] }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $order['customer_phone'] }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-info me-2">{{ $order['items_count'] }} منتج</span>
                                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#orderItemsModal{{ $order['id'] }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-end">
                                                            @if($order['original_amount'] != $order['total'])
                                                                <small class="text-muted text-decoration-line-through d-block">{{ number_format($order['original_amount'], 2) }} ر.س</small>
                                                            @endif
                                                            <strong class="text-success">{{ number_format($order['total'], 2) }} ر.س</strong>
                                                            @if($order['coupon_discount'] > 0)
                                                                <small class="text-success d-block">خصم: {{ number_format($order['coupon_discount'], 2) }} ر.س</small>
                                                            @endif
                                                            @if($order['quantity_discount'] > 0)
                                                                <small class="text-info d-block">خصم الكمية: {{ number_format($order['quantity_discount'], 2) }} ر.س</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $order['status_color'] }} px-3 py-2">
                                                            {{ $order['status_text'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $order['payment_status_color'] }} px-3 py-2">
                                                            {{ $order['payment_status_text'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($order['is_assigned'])
                                                            <div class="text-warning">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $order['assigned_admin_name'] }}
                                                            </div>
                                                            <small class="text-muted">{{ $order['assigned_at'] }}</small>
                                                        @else
                                                            <span class="text-secondary">
                                                                <i class="fas fa-question-circle me-1"></i>
                                                                غير مخصص
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div><strong>{{ $order['created_at_formatted'] }}</strong></div>
                                                        <small class="text-muted">{{ $order['created_at'] }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('admin.orders.show', $order['uuid']) }}" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye me-1"></i>
                                                                عرض
                                                            </a>
                                                            @if($order['is_available_for_assignment'])
                                                                <button type="button" class="btn btn-sm btn-success assign-order-btn" data-order-uuid="{{ $order['uuid'] }}">
                                                                    <i class="fas fa-hand-paper me-1"></i>
                                                                    استلام الطلب
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                                    <i class="fas fa-lock me-1"></i>
                                                                    مخصص لأدمن آخر
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>

                                    <!-- Order Items Modal -->
                                    <div class="modal fade" id="orderItemsModal{{ $order['id'] }}" tabindex="-1" aria-labelledby="orderItemsModalLabel{{ $order['id'] }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderItemsModalLabel{{ $order['id'] }}">
                                                        <i class="fas fa-shopping-cart me-2"></i>
                                                        تفاصيل الطلب {{ $order['order_number'] }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>المنتج</th>
                                                                    <th>الكمية</th>
                                                                    <th>السعر</th>
                                                                    <th>الإجمالي</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($order['items'] as $item)
                                                                    <tr>
                                                                        <td><strong>{{ $item['product_name'] }}</strong></td>
                                                                        <td><span class="badge bg-primary">{{ $item['quantity'] }}</span></td>
                                                                        <td>{{ number_format($item['price'], 2) }} ر.س</td>
                                                                        <td><strong>{{ number_format($item['total'], 2) }} ر.س</strong></td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                <h5>لا توجد طلبات غير مخصصة</h5>
                                                <p class="mb-0">جميع الطلبات مخصصة لك أو لأدمن آخر</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        {{ $orders->appends(request()->query())->links() }}
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Unassigned orders page loaded');
    
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Assign order button click
    $(document).on('click', '.assign-order-btn', function() {
        const orderUuid = $(this).data('order-uuid');
        const button = $(this);
        
        console.log('Assigning order:', orderUuid);
        
        // Disable button and show loading
        button.prop('disabled', true)
              .html('<i class="fas fa-spinner fa-spin me-1"></i> جاري الاستلام...');
        
        $.ajax({
            url: `{{ url('/admin/orders') }}/${orderUuid}/assign`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Assignment successful:', response);
                showAlert('تم استلام الطلب بنجاح', 'success');
                
                // Update button state
                button.removeClass('btn-success assign-order-btn')
                      .addClass('btn-secondary')
                      .prop('disabled', true)
                      .html('<i class="fas fa-lock me-1"></i> تم الاستلام');
                
                // Reload page after a short delay to update stats
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr, status, error) {
                console.error('Assignment failed:', xhr.responseText);
                let errorMessage = 'حدث خطأ أثناء استلام الطلب';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showAlert(errorMessage, 'error');
                
                // Re-enable button
                button.prop('disabled', false)
                      .html('<i class="fas fa-hand-paper me-1"></i> استلام الطلب');
            }
        });
    });

    // Reset filters function
    window.resetFilters = function() {
        window.location.href = '{{ route("admin.orders.unassigned") }}';
    };

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the content
        $('.orders-container').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush

