@extends($adminLayout)

@section('title', 'الطلبات المدعوة')
@section('page_title', 'الطلبات المدعوة')

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
                                                    <i class="fas fa-user-friends me-2"></i>
                                                    الطلبات المدعوة
                                                </h4>
                                                <p class="text-muted mb-0">
                                                    الطلبات التي تمت دعوتك لمتابعتها كصديق
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
                                            <h6 class="text-muted text-uppercase mb-2">إجمالي الطلبات المدعوة</h6>
                                            <h3 class="mb-0" id="totalInvitedOrders">0</h3>
                                        </div>
                                        <div class="icon-circle bg-primary-subtle text-primary">
                                            <i class="fas fa-user-friends"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات النشطة</h6>
                                            <h3 class="mb-0" id="activeOrders">0</h3>
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
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات المكتملة</h6>
                                            <h3 class="mb-0" id="completedOrders">0</h3>
                                        </div>
                                        <div class="icon-circle bg-info-subtle text-info">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات الملغية</h6>
                                            <h3 class="mb-0" id="cancelledOrders">0</h3>
                                        </div>
                                        <div class="icon-circle bg-danger-subtle text-danger">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    قائمة الطلبات المدعوة
                                </h5>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-light-primary btn-sm me-2" onclick="loadInvitedOrders()">
                                        <i class="fas fa-sync-alt me-1"></i>
                                        تحديث
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="invitedOrdersTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-0">رقم الطلب</th>
                                                <th class="border-0">العميل</th>
                                                <th class="border-0">المبلغ الإجمالي</th>
                                                <th class="border-0">حالة الطلب</th>
                                                <th class="border-0">حالة الدفع</th>
                                                <th class="border-0">الأدمن المسؤول</th>
                                                <th class="border-0">تاريخ الطلب</th>
                                                <th class="border-0">الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invitedOrdersTableBody">
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="sr-only">جاري التحميل...</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i>
                    تفاصيل الطلب
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" class="btn btn-primary" id="viewFullOrderBtn" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    عرض الطلب كاملاً
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Page loaded, starting to load invited orders...');
    loadInvitedOrders();
    
    // Load invited orders
    function loadInvitedOrders() {
        console.log('Loading invited orders...');
        const url = '{{ route("admin.orders.invited.data") }}';
        console.log('Request URL:', url);
        
        $.ajax({
            url: url,
            type: 'GET',
            timeout: 10000, // 10 seconds timeout
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Response received:', response);
                if (response.success) {
                    displayInvitedOrders(response.orders);
                    updateStats(response.orders);
                    if (response.debug) {
                        console.log('Debug info:', response.debug);
                    }
                } else {
                    console.error('API returned error:', response.message);
                    showAlert(response.message || 'حدث خطأ أثناء تحميل الطلبات المدعوة', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusText: xhr.statusText,
                    statusCode: xhr.status
                });
                
                let errorMessage = 'حدث خطأ أثناء تحميل الطلبات المدعوة';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (status === 'timeout') {
                    errorMessage = 'انتهت مهلة الاتصال، يرجى المحاولة مرة أخرى';
                } else if (xhr.status === 401) {
                    errorMessage = 'غير مصرح لك بالوصول لهذه الصفحة';
                } else if (xhr.status === 403) {
                    errorMessage = 'غير مصرح لك بالوصول لهذه الصفحة';
                } else if (xhr.status === 404) {
                    errorMessage = 'الصفحة غير موجودة';
                } else if (xhr.status === 500) {
                    errorMessage = 'خطأ في الخادم، يرجى المحاولة مرة أخرى';
                }
                
                showAlert(errorMessage, 'error');
            }
        });
    }
    
    // Display invited orders in table
    function displayInvitedOrders(orders) {
        console.log('Displaying orders:', orders);
        
        if (!orders || orders.length === 0) {
            $('#invitedOrdersTableBody').html(`
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <h5>لا توجد طلبات مدعوة لك</h5>
                            <p class="mb-0">عندما يتم دعوتك لتتبع طلب، سيظهر هنا</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }
        
        let html = '';
        orders.forEach(function(item, index) {
            console.log('Processing order item:', index, item);
            const order = item.order;
            html += '<tr class="align-middle">';
            html += '<td><div class="d-flex align-items-center"><div class="me-3"><i class="fas fa-shopping-cart text-primary"></i></div><div><strong>' + (order.order_number || 'غير محدد') + '</strong></div></div></td>';
            html += '<td><div><strong>' + (order.customer?.name || 'غير محدد') + '</strong><br><small class="text-muted">' + (order.customer?.email || 'غير محدد') + '</small></div></td>';
            html += '<td><span class="fw-bold text-success">' + (order.total_amount || 0) + ' ريال</span></td>';
            html += '<td><span class="badge bg-' + getStatusColor(order.order_status) + ' px-3 py-2">' + getStatusText(order.order_status) + '</span></td>';
            html += '<td><span class="badge bg-' + getPaymentStatusColor(order.payment_status) + ' px-3 py-2">' + getPaymentStatusText(order.payment_status) + '</span></td>';
            html += '<td><span class="text-muted">' + (order.assigned_admin ? order.assigned_admin.name : 'غير محدد') + '</span></td>';
            html += '<td><small class="text-muted">' + (order.created_at || 'غير محدد') + '</small></td>';
            html += '<td><div class="d-flex gap-2">';
            html += '<button type="button" class="btn btn-sm btn-outline-info view-order-btn" data-order-uuid="' + (order.uuid || '') + '">';
            html += '<i class="fas fa-eye me-1"></i> عرض';
            html += '</button>';
            html += '<a href="' + '{{ route("admin.orders.show", "") }}/' + (order.uuid || '') + '" class="btn btn-sm btn-primary" target="_blank">';
            html += '<i class="fas fa-external-link-alt me-1"></i> فتح';
            html += '</a>';
            html += '</div></td>';
            html += '</tr>';
        });
        
        $('#invitedOrdersTableBody').html(html);
    }
    
    // Update statistics
    function updateStats(orders) {
        const total = orders ? orders.length : 0;
        $('#totalInvitedOrders').text(total);
        
        const activeOrders = orders ? orders.filter(order => 
            ['pending', 'processing', 'out_for_delivery', 'on_the_way'].includes(order.order?.order_status)
        ).length : 0;
        $('#activeOrders').text(activeOrders);
        
        const completedOrders = orders ? orders.filter(order => 
            ['completed', 'delivered'].includes(order.order?.order_status)
        ).length : 0;
        $('#completedOrders').text(completedOrders);
        
        const cancelledOrders = orders ? orders.filter(order => 
            ['cancelled', 'returned'].includes(order.order?.order_status)
        ).length : 0;
        $('#cancelledOrders').text(cancelledOrders);
        
        console.log('Stats updated - Total:', total, 'Active:', activeOrders, 'Completed:', completedOrders, 'Cancelled:', cancelledOrders);
    }
    
    // Get status color
    function getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'processing': 'info',
            'completed': 'success',
            'cancelled': 'danger',
            'out_for_delivery': 'primary',
            'on_the_way': 'primary',
            'delivered': 'success',
            'returned': 'secondary'
        };
        return colors[status] || 'secondary';
    }
    
    // Get status text
    function getStatusText(status) {
        const texts = {
            'pending': 'قيد الانتظار',
            'processing': 'قيد المعالجة',
            'completed': 'مكتمل',
            'cancelled': 'ملغي',
            'out_for_delivery': 'خارج للتوصيل',
            'on_the_way': 'في الطريق',
            'delivered': 'تم التوصيل',
            'returned': 'مرتجع'
        };
        return texts[status] || status;
    }
    
    // Get payment status color
    function getPaymentStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'paid': 'success',
            'failed': 'danger',
            'refunded': 'info'
        };
        return colors[status] || 'secondary';
    }
    
    // Get payment status text
    function getPaymentStatusText(status) {
        const texts = {
            'pending': 'قيد الانتظار',
            'paid': 'مدفوع',
            'failed': 'فشل',
            'refunded': 'مسترد'
        };
        return texts[status] || status;
    }
    
    // View order details
    $(document).on('click', '.view-order-btn', function() {
        const orderUuid = $(this).data('order-uuid');
        const viewUrl = '{{ route("admin.orders.show", "") }}/' + orderUuid;
        
        // Set the full order link
        $('#viewFullOrderBtn').attr('href', viewUrl);
        
        // Load order details in modal
        $('#orderDetailsContent').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">جاري التحميل...</span>
                </div>
            </div>
        `);
        
        $('#orderDetailsModal').modal('show');
        
        // Load order details via AJAX
        $.ajax({
            url: viewUrl,
            type: 'GET',
            success: function(response) {
                $('#orderDetailsContent').html(`
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        يمكنك عرض تفاصيل الطلب الكاملة من خلال الضغط على زر "عرض الطلب كاملاً"
                    </div>
                `);
            },
            error: function() {
                $('#orderDetailsContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        حدث خطأ أثناء تحميل تفاصيل الطلب
                    </div>
                `);
            }
        });
    });
    
    // Show alert function
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the card body
        $('.card-body').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush
