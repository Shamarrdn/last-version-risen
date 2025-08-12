@extends($adminLayout)

@section('title', 'الطلبات')
@section('page_title', 'إدارة الطلبات')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="orders-container">
                        <!-- Tabs Navigation -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <ul class="nav nav-pills nav-fill" id="orderTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" 
                                                   href="{{ route('admin.orders.index', ['tab' => 'all'] + request()->except('tab')) }}">
                                                    <i class="fas fa-list me-1"></i>
                                                    جميع الطلبات
                                                    <span class="badge bg-secondary ms-1">{{ $tabCounts['all'] }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link {{ $tab === 'assigned' ? 'active' : '' }}" 
                                                   href="{{ route('admin.orders.index', ['tab' => 'assigned'] + request()->except('tab')) }}">
                                                    <i class="fas fa-user-check me-1"></i>
                                                    طلباتي المخصصة
                                                    <span class="badge bg-primary ms-1">{{ $tabCounts['assigned'] }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link {{ $tab === 'unassigned' ? 'active' : '' }}" 
                                                   href="{{ route('admin.orders.index', ['tab' => 'unassigned'] + request()->except('tab')) }}">
                                                    <i class="fas fa-user-times me-1"></i>
                                                    الطلبات غير المخصصة
                                                    <span class="badge bg-warning ms-1">{{ $tabCounts['unassigned'] }}</span>
                                                </a>
                                            </li>
                                        </ul>
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
                                                <i class="fas fa-shopping-cart fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي الطلبات</h6>
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
                                                <i class="fas fa-money-bill-wave fa-lg"></i>
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

                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <span class="icon-circle bg-primary text-white me-2">
                                                    <i class="fas fa-shopping-bag"></i>
                                                </span>
                                                إدارة الطلبات
                                            </h5>
                                            <p class="text-muted mb-0 fs-sm">إدارة ومتابعة طلبات العملاء</p>
                                        </div>
                                        <div class="actions d-flex gap-2">
                                            <button type="button" class="btn btn-light-success btn-wave" onclick="window.print()">
                                                <i class="fas fa-print me-2"></i>
                                                طباعة
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>

                        <!-- Search & Filters -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                <div class="card-body">
                                        <form action="{{ route('admin.orders.index') }}" method="GET" id="filters-form">
                                            <div class="row g-3">
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

                                                <div class="col-md-2">
                                                    <div class="filter-buttons">
                                                        <button type="submit" class="btn btn-primary btn-wave">
                                                            <i class="fas fa-filter me-2"></i>
                                                            تصفية
                                                        </button>
                                                        @if(request()->hasAny(['order_number', 'search', 'order_status', 'date_from', 'date_to']))
                                                            <a href="{{ route('admin.orders.index') }}"
                                                               class="btn btn-light-danger btn-wave"
                                                               title="إزالة الفلتر">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
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
                                                            <th>الأدمن المسؤول</th>
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
                                                                @if($order['is_assigned'])
                                                                    <div class="assigned-admin">
                                                                        <i class="fas fa-user-check text-success me-1"></i>
                                                                        <span class="fw-bold">{{ $order['assigned_admin_name'] }}</span>
                                                                        <small class="d-block text-muted">{{ $order['assigned_at'] }}</small>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">
                                                                        <i class="fas fa-user-times me-1"></i>
                                                                        غير مخصص
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $order['created_at_formatted'] }}</td>
                                                            <td>
                                                                <div class="action-buttons d-flex gap-1">
                                                                    <a href="{{ route('admin.orders.show', $order['uuid']) }}"
                                                                       class="btn btn-light-info btn-sm"
                                                                       title="عرض التفاصيل">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    
                                                                    @if(!$order['is_assigned'])
                                                                        <button type="button" 
                                                                                class="btn btn-success btn-sm assign-order-btn"
                                                                                data-order-uuid="{{ $order['uuid'] }}"
                                                                                title="استلام الطلب">
                                                                            <i class="fas fa-hand-paper"></i>
                                                                        </button>
                                                                    @elseif($order['is_assigned_to_me'])
                                                                        <button type="button" 
                                                                                class="btn btn-warning btn-sm unassign-order-btn"
                                                                                data-order-uuid="{{ $order['uuid'] }}"
                                                                                title="إلغاء الاستلام">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="12" class="text-center py-5">
                                                                <div class="empty-state">
                                                                    <div class="empty-icon bg-light rounded-circle mb-3">
                                                                        <i class="fas fa-shopping-cart text-muted fa-2x"></i>
                                                                    </div>
                                                                    <h5 class="text-muted mb-0">لا توجد طلبات</h5>
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
                        @if(isset($hasFilters) && !$hasFilters && $orders->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="صفحات الطلبات">
                                <ul class="pagination mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($orders->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">
                                                <i class="fas fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                        @if ($page == $orders->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($orders->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">
                                                <i class="fas fa-chevron-left"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>
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
@media print {
  body, html {
    background: #fff !important;
    width: 100vw !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }
  .content-wrapper, .orders-container, .card, .card-body, .table-wrapper, .table-responsive {
    all: unset !important;
    display: block !important;
    width: 100vw !important;
    max-width: 100vw !important;
    background: #fff !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }
  table {
    width: 100vw !important;
    max-width: 100vw !important;
    border-radius: 0 !important;
    background: #fff !important;
    page-break-inside: auto;
  }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr { page-break-inside: avoid; }
  ::-webkit-scrollbar { display: none !important; }
  .actions, .filter-buttons, .btn, nav.pagination, .sidebar, .navbar, .print-hide, .card-title, .card-header, .card-footer, .row.mb-4, .row.g-4.mb-4, .row.mb-4, .row.g-3, .search-wrapper, form#filters-form, .filter-buttons, .actions.d-flex, .pagination, .print-hide {
    display: none !important;
  }
  .row.g-4.mb-4 {
    display: flex !important;
    flex-wrap: wrap !important;
    margin-bottom: 1rem !important;
  }
  .stat-card {
    display: block !important;
    page-break-inside: avoid !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    background: #fff !important;
  }
}
</style>
@endsection

@section('scripts')
<script>
// Wait for DOM and jQuery to be ready
$(document).ready(function() {
    console.log('Orders page JavaScript loaded');
    console.log('jQuery available:', typeof $ !== 'undefined');
    console.log('CSRF token available:', $('meta[name="csrf-token"]').length > 0);
    
    // CSRF token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Check if assign buttons exist
    console.log('Assign buttons found:', $('.assign-order-btn').length);
    
    // Simple test button click
    $('.assign-order-btn').on('click', function(e) {
        e.preventDefault();
        console.log('Direct button clicked!');
        alert('تم الضغط على الزر!');
    });
    
    // Assign Order Button
    $(document).on('click', '.assign-order-btn', function(e) {
        e.preventDefault();
        console.log('Delegated button clicked!');
        
        const button = $(this);
        const orderUuid = button.data('order-uuid');
        const row = button.closest('tr');
        
        console.log('Order UUID:', orderUuid);
        console.log('Button element:', button);
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
        
        if (!orderUuid) {
            alert('Order UUID not found!');
            return;
        }
        
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `{{ url('/admin/orders') }}/${orderUuid}/assign`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);
                    
                    // Update the assigned admin column
                    const assignedCell = row.find('td').eq(9); // Assigned admin column
                    assignedCell.html(`
                        <div class="assigned-admin">
                            <i class="fas fa-user-check text-success me-1"></i>
                            <span class="fw-bold">${response.assigned_admin}</span>
                            <small class="d-block text-muted">${response.assigned_at}</small>
                        </div>
                    `);
                    
                    // Replace assign button with unassign button
                    button.removeClass('btn-success assign-order-btn')
                          .addClass('btn-warning unassign-order-btn')
                          .attr('title', 'إلغاء الاستلام')
                          .html('<i class="fas fa-times"></i>');
                } else {
                    showAlert('error', response.message);
                    button.html('<i class="fas fa-hand-paper"></i>');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                const message = xhr.responseJSON?.message || 'حدث خطأ أثناء استلام الطلب';
                showAlert('error', message);
                button.html('<i class="fas fa-hand-paper"></i>');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Unassign Order Button
    $(document).on('click', '.unassign-order-btn', function() {
        const button = $(this);
        const orderUuid = button.data('order-uuid');
        const row = button.closest('tr');
        
        if (!confirm('هل أنت متأكد من إلغاء استلام هذا الطلب؟')) {
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
                    
                    // Update the assigned admin column
                    const assignedCell = row.find('td').eq(9); // Assigned admin column
                    assignedCell.html(`
                        <span class="text-muted">
                            <i class="fas fa-user-times me-1"></i>
                            غير مخصص
                        </span>
                    `);
                    
                    // Replace unassign button with assign button
                    button.removeClass('btn-warning unassign-order-btn')
                          .addClass('btn-success assign-order-btn')
                          .attr('title', 'استلام الطلب')
                          .html('<i class="fas fa-hand-paper"></i>');
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
