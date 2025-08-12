@extends($adminLayout)

@section('title', 'نقل الطلبات')
@section('page_title', 'نقل الطلبات')

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
                                                    <i class="fas fa-exchange-alt me-2"></i>
                                                    نقل الطلبات
                                                </h4>
                                                <p class="text-muted mb-0">
                                                    إدارة طلبات نقل الطلبات بين الأدمن
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
                                            <h3 class="mb-0">{{ $stats['total_requests'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-primary-subtle text-primary">
                                            <i class="fas fa-exchange-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">طلبات في الانتظار</h6>
                                            <h3 class="mb-0">{{ $stats['pending_requests'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-warning-subtle text-warning">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات المرسلة</h6>
                                            <h3 class="mb-0">{{ $stats['sent_requests'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-info-subtle text-info">
                                            <i class="fas fa-paper-plane"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات المرفوضة</h6>
                                            <h3 class="mb-0">{{ $stats['rejected_requests'] ?? 0 }}</h3>
                                        </div>
                                        <div class="icon-circle bg-danger-subtle text-danger">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">فلاتر البحث</h5>
                                <button class="btn btn-light-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                                    <i class="fas fa-filter me-1"></i> إظهار/إخفاء الفلاتر
                                </button>
                            </div>
                            <div class="collapse" id="filtersCollapse">
                                <div class="card-body">
                                    <form method="GET" action="{{ route('admin.transfers.index') }}" class="row g-3">
                                        <div class="col-md-4">
                                            <label for="status" class="form-label">حالة الطلب</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="">جميع الحالات</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="order_number" class="form-label">رقم الطلب</label>
                                            <input type="text" class="form-control" id="order_number" name="order_number" value="{{ request('order_number') }}" placeholder="ابحث برقم الطلب">
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="fas fa-search me-1"></i> بحث
                                            </button>
                                            <a href="{{ route('admin.transfers.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i> إلغاء
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Requests Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom p-3">
                                <h5 class="mb-0">طلبات النقل</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>رقم الطلب</th>
                                                <th>من أدمن</th>
                                                <th>إلى أدمن</th>
                                                <th>سبب النقل</th>
                                                <th>الحالة</th>
                                                <th>تاريخ الطلب</th>
                                                <th>تاريخ الرد</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transferRequests as $request)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $request->order->uuid) }}" class="text-decoration-none">
                                                        <strong>{{ $request->order->order_number }}</strong>
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $request->order->user->name }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle bg-primary text-white me-2">
                                                            {{ substr($request->fromAdmin->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $request->fromAdmin->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $request->fromAdmin->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle bg-success text-white me-2">
                                                            {{ substr($request->toAdmin->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <strong>{{ $request->toAdmin->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $request->toAdmin->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($request->reason)
                                                        <span class="text-muted">{{ Str::limit($request->reason, 50) }}</span>
                                                    @else
                                                        <span class="text-muted">لا يوجد سبب</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $request->status_color }}-subtle text-{{ $request->status_color }} rounded-pill">
                                                        {{ $request->status_text }}
                                                    </span>
                                                </td>
                                                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    @if($request->responded_at)
                                                        {{ $request->responded_at->format('Y-m-d H:i') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @if($request->isPending())
                                                            @if($request->to_admin_id == auth()->id())
                                                                <!-- Target admin can approve/reject -->
                                                                <button type="button" class="btn btn-sm btn-success approve-transfer-btn" data-request-id="{{ $request->id }}" title="موافقة">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger reject-transfer-btn" data-request-id="{{ $request->id }}" title="رفض">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @elseif($request->from_admin_id == auth()->id())
                                                                <!-- Sender can cancel -->
                                                                <button type="button" class="btn btn-sm btn-warning cancel-transfer-btn" data-request-id="{{ $request->id }}" title="إلغاء">
                                                                    <i class="fas fa-ban"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                        
                                                        @if($request->isRejected() && $request->rejection_reason)
                                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="{{ $request->rejection_reason }}">
                                                                <i class="fas fa-info-circle"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-5">
                                                    <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">لا توجد طلبات نقل حالياً.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($transferRequests->hasPages())
                            <div class="card-footer bg-white p-3">
                                {{ $transferRequests->links('pagination::bootstrap-5') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>
                    رفض طلب النقل
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">سبب الرفض (اختياري)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" placeholder="اكتب سبب الرفض..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmReject">
                    <i class="fas fa-times me-2"></i>
                    رفض الطلب
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .orders-container .card {
        border-radius: 0.75rem;
    }
    .orders-container .card-header {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }
    .orders-container .card-footer {
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: bold;
    }
    .btn-group .btn {
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Approve transfer request
    $(document).on('click', '.approve-transfer-btn', function() {
        const requestId = $(this).data('request-id');
        const button = $(this);
        
        if (confirm('هل أنت متأكد من الموافقة على نقل هذا الطلب؟')) {
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: `{{ url('/admin/transfers') }}/${requestId}/approve`,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        showAlert('تمت الموافقة على نقل الطلب بنجاح', 'success');
                        location.reload();
                    } else {
                        showAlert(response.message || 'حدث خطأ أثناء الموافقة', 'error');
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-check"></i>');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'حدث خطأ أثناء الموافقة';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'error');
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-check"></i>');
                }
            });
        }
    });

    // Reject transfer request
    let currentRequestId = null;
    
    $(document).on('click', '.reject-transfer-btn', function() {
        currentRequestId = $(this).data('request-id');
        $('#rejectModal').modal('show');
    });

    $('#confirmReject').on('click', function() {
        const button = $(this);
        const reason = $('#rejection_reason').val();
        
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `{{ url('/admin/transfers') }}/${currentRequestId}/reject`,
            type: 'POST',
            data: {
                rejection_reason: reason,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('تم رفض طلب النقل بنجاح', 'success');
                    $('#rejectModal').modal('hide');
                    location.reload();
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء الرفض', 'error');
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-times me-2"></i> رفض الطلب');
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء الرفض';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
                button.prop('disabled', false);
                button.html('<i class="fas fa-times me-2"></i> رفض الطلب');
            }
        });
    });

    // Cancel transfer request
    $(document).on('click', '.cancel-transfer-btn', function() {
        const requestId = $(this).data('request-id');
        const button = $(this);
        
        if (confirm('هل أنت متأكد من إلغاء طلب النقل؟')) {
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: `{{ url('/admin/transfers') }}/${requestId}/cancel`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showAlert('تم إلغاء طلب النقل بنجاح', 'success');
                        location.reload();
                    } else {
                        showAlert(response.message || 'حدث خطأ أثناء الإلغاء', 'error');
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-ban"></i>');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'حدث خطأ أثناء الإلغاء';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'error');
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-ban"></i>');
                }
            });
        }
    });

    // Reset modal when closed
    $('#rejectModal').on('hidden.bs.modal', function() {
        $('#rejection_reason').val('');
        currentRequestId = null;
        $('#confirmReject').prop('disabled', false).html('<i class="fas fa-times me-2"></i> رفض الطلب');
    });

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('.content-wrapper').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endsection

