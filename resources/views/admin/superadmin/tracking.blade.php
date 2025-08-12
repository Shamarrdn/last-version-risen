@extends('layouts.superadmin')

@section('title', 'تتبع الأدمنز')
@section('page_title', 'تتبع الأدمنز')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="tracking-container">
                        <!-- Page Header -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-1">
                                                    <i class="fas fa-chart-line me-2"></i>
                                                    تتبع أداء الأدمنز
                                                </h4>
                                                <p class="text-muted mb-0">
                                                    مراقبة أداء الأدمنز وإحصائياتهم وتاريخ نشاطهم
                                                </p>
                                            </div>
                                            <div class="d-flex">
                                                <button type="button" class="btn btn-light-primary me-2" onclick="refreshData()">
                                                    <i class="fas fa-sync-alt me-1"></i> تحديث
                                                </button>
                                            </div>
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
                                <form method="GET" action="{{ route('superadmin.tracking') }}" class="row g-3">
                                    <div class="col-md-3">
                                        <select class="form-select" name="admin_id">
                                            <option value="">جميع الأدمنز</option>
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->id }}" {{ $adminId == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->name }} ({{ $admin->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="date_from" placeholder="من تاريخ" value="{{ $dateFrom }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="date_to" placeholder="إلى تاريخ" value="{{ $dateTo }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="tab">
                                            <option value="overview" {{ $tab == 'overview' ? 'selected' : '' }}>نظرة عامة</option>
                                            <option value="transfers" {{ $tab == 'transfers' ? 'selected' : '' }}>تاريخ النقلات</option>
                                            <option value="friends" {{ $tab == 'friends' ? 'selected' : '' }}>تاريخ الأصدقاء</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Overall Statistics -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">إجمالي الطلبات</h6>
                                            <h3 class="mb-0">{{ $overallStats['total_orders'] }}</h3>
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
                                            <h6 class="text-muted text-uppercase mb-2">الطلبات المخصصة</h6>
                                            <h3 class="mb-0">{{ $overallStats['assigned_orders'] }}</h3>
                                        </div>
                                        <div class="icon-circle bg-success-subtle text-success">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">إجمالي النقلات</h6>
                                            <h3 class="mb-0">{{ $overallStats['total_transfers'] }}</h3>
                                        </div>
                                        <div class="icon-circle bg-info-subtle text-info">
                                            <i class="fas fa-exchange-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="text-muted text-uppercase mb-2">الأصدقاء المضافون</h6>
                                            <h3 class="mb-0">{{ $overallStats['total_friends_added'] }}</h3>
                                        </div>
                                        <div class="icon-circle bg-warning-subtle text-warning">
                                            <i class="fas fa-user-friends"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom p-0">
                                <ul class="nav nav-tabs card-header-tabs" id="trackingTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $tab == 'overview' ? 'active' : '' }}" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                            <i class="fas fa-chart-pie me-2"></i>
                                            نظرة عامة
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $tab == 'transfers' ? 'active' : '' }}" id="transfers-tab" data-bs-toggle="tab" data-bs-target="#transfers" type="button" role="tab">
                                            <i class="fas fa-exchange-alt me-2"></i>
                                            تاريخ النقلات
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $tab == 'friends' ? 'active' : '' }}" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends" type="button" role="tab">
                                            <i class="fas fa-user-friends me-2"></i>
                                            تاريخ الأصدقاء
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content" id="trackingTabsContent">
                                    <!-- Overview Tab -->
                                    <div class="tab-pane fade {{ $tab == 'overview' ? 'show active' : '' }}" id="overview" role="tabpanel">
                                        <div class="p-4">
                                            <!-- Admin Performance Cards -->
                                            <div class="row g-4 mb-4">
                                                @foreach($adminStats as $admin)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="card-header bg-light border-bottom">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-circle me-3">
                                                                    <span class="avatar-text">{{ substr($admin->name, 0, 1) }}</span>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0">{{ $admin->name }}</h6>
                                                                    <small class="text-muted">{{ $admin->email }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row text-center">
                                                                <div class="col-6">
                                                                    <div class="stat-item">
                                                                        <h4 class="text-primary mb-1">{{ $admin->filtered_orders ?? $admin->total_orders }}</h4>
                                                                        <small class="text-muted">الطلبات</small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="stat-item">
                                                                        <h4 class="text-success mb-1">{{ $admin->filtered_completed ?? $admin->completed_orders }}</h4>
                                                                        <small class="text-muted">مكتملة</small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 mt-3">
                                                                    <div class="stat-item">
                                                                        <h4 class="text-info mb-1">{{ $admin->filtered_paid ?? $admin->paid_orders }}</h4>
                                                                        <small class="text-muted">مدفوعة</small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 mt-3">
                                                                    <div class="stat-item">
                                                                        <h4 class="text-warning mb-1">{{ number_format($admin->filtered_revenue ?? $admin->total_revenue, 2) }}</h4>
                                                                        <small class="text-muted">ريال</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-light">
                                                            <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="viewAdminDetails({{ $admin->id }})">
                                                                <i class="fas fa-eye me-1"></i>
                                                                عرض التفاصيل
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transfers Tab -->
                                    <div class="tab-pane fade {{ $tab == 'transfers' ? 'show active' : '' }}" id="transfers" role="tabpanel">
                                        <div class="p-4">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>الطلب</th>
                                                            <th>من</th>
                                                            <th>إلى</th>
                                                            <th>السبب</th>
                                                            <th>الحالة</th>
                                                            <th>التاريخ</th>
                                                            <th>الإجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($transferHistory as $transfer)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $transfer->order->order_number }}</strong>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-circle me-2">
                                                                        <span class="avatar-text">{{ substr($transfer->fromAdmin->name, 0, 1) }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <strong>{{ $transfer->fromAdmin->name }}</strong>
                                                                        <br>
                                                                        <small class="text-muted">{{ $transfer->fromAdmin->email }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-circle me-2">
                                                                        <span class="avatar-text">{{ substr($transfer->toAdmin->name, 0, 1) }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <strong>{{ $transfer->toAdmin->name }}</strong>
                                                                        <br>
                                                                        <small class="text-muted">{{ $transfer->toAdmin->email }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">{{ Str::limit($transfer->reason, 50) }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $transfer->status_color }} px-3 py-2">
                                                                    {{ $transfer->status_text }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">{{ $transfer->created_at->format('Y-m-d H:i') }}</small>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.orders.show', $transfer->order->uuid) }}" class="btn btn-sm btn-outline-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-5">
                                                                <div class="text-muted">
                                                                    <i class="fas fa-exchange-alt fa-3x mb-3 d-block"></i>
                                                                    <h5>لا توجد نقلات</h5>
                                                                    <p class="mb-0">لم يتم العثور على أي طلبات نقل</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            @if($transferHistory->hasPages())
                                            <div class="d-flex justify-content-center mt-4">
                                                {{ $transferHistory->appends(request()->query())->links() }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Friends Tab -->
                                    <div class="tab-pane fade {{ $tab == 'friends' ? 'show active' : '' }}" id="friends" role="tabpanel">
                                        <div class="p-4">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>الطلب</th>
                                                            <th>الأدمن</th>
                                                            <th>الصديق</th>
                                                            <th>البريد الإلكتروني</th>
                                                            <th>الحالة</th>
                                                            <th>التاريخ</th>
                                                            <th>الإجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($friendHistory as $friend)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $friend->order->order_number }}</strong>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-circle me-2">
                                                                        <span class="avatar-text">{{ substr($friend->user->name, 0, 1) }}</span>
                                                                    </div>
                                                                    <div>
                                                                        <strong>{{ $friend->user->name }}</strong>
                                                                        <br>
                                                                        <small class="text-muted">{{ $friend->user->email }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <strong>{{ $friend->friend_name }}</strong>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">{{ $friend->friend_email }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $friend->is_active ? 'success' : 'secondary' }} px-3 py-2">
                                                                    {{ $friend->is_active ? 'نشط' : 'غير نشط' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted">{{ $friend->created_at->format('Y-m-d H:i') }}</small>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.orders.show', $friend->order->uuid) }}" class="btn btn-sm btn-outline-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-5">
                                                                <div class="text-muted">
                                                                    <i class="fas fa-user-friends fa-3x mb-3 d-block"></i>
                                                                    <h5>لا توجد إضافات أصدقاء</h5>
                                                                    <p class="mb-0">لم يتم العثور على أي إضافات أصدقاء</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            @if($friendHistory->hasPages())
                                            <div class="d-flex justify-content-center mt-4">
                                                {{ $friendHistory->appends(request()->query())->links() }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Details Modal -->
<div class="modal fade" id="adminDetailsModal" tabindex="-1" aria-labelledby="adminDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminDetailsModalLabel">
                    <i class="fas fa-user me-2"></i>
                    تفاصيل الأدمن
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="adminDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.avatar-text {
    text-transform: uppercase;
}

.stat-item {
    padding: 10px;
    border-radius: 8px;
    background: #f8f9fa;
}

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
.bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
.bg-info-subtle { background-color: rgba(13, 202, 240, 0.1); }
.bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
.bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1); }
.bg-orange-subtle { background-color: rgba(253, 126, 20, 0.1); }

.text-orange { color: #fd7e14; }
</style>
@endpush

@push('scripts')
<script>
function refreshData() {
    location.reload();
}

function resetFilters() {
    window.location.href = '{{ route("superadmin.tracking") }}';
}

function viewAdminDetails(adminId) {
    // Load admin details via AJAX
    $.ajax({
        url: '{{ route("superadmin.tracking.performance") }}',
        type: 'GET',
        data: { admin_id: adminId, days: 30 },
        success: function(response) {
            $('#adminDetailsContent').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">جاري التحميل...</span>
                    </div>
                </div>
            `);
            
            // Here you can add chart rendering logic
            setTimeout(function() {
                $('#adminDetailsContent').html(`
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        سيتم إضافة الرسوم البيانية هنا قريباً
                    </div>
                `);
            }, 1000);
            
            $('#adminDetailsModal').modal('show');
        },
        error: function() {
            $('#adminDetailsContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    حدث خطأ أثناء تحميل البيانات
                </div>
            `);
            $('#adminDetailsModal').modal('show');
        }
    });
}

// Auto-refresh data every 5 minutes
setInterval(function() {
    // Only refresh if user is on the page
    if (!document.hidden) {
        // You can add auto-refresh logic here if needed
    }
}, 300000); // 5 minutes
</script>
@endpush
