@extends($adminLayout)

@section('title', 'إدارة المخزون')
@section('page_title', 'إدارة المخزون')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="inventory-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-boxes text-primary me-2"></i>
                                            إدارة المخزون
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>
                                                إضافة مخزون جديد
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Alerts -->
                        @php
                            $lowStockItems = $inventory->filter(function($item) {
                                return $item->available_stock <= 5 && $item->available_stock > 0;
                            });
                            $outOfStockItems = $inventory->filter(function($item) {
                                return $item->available_stock <= 0;
                            });
                        @endphp

                        @if($lowStockItems->count() > 0 || $outOfStockItems->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="fas fa-exclamation-triangle me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">تنبيهات المخزون</h6>
                                        @if($lowStockItems->count() > 0)
                                            <p class="mb-1">
                                                <strong>{{ $lowStockItems->count() }}</strong> عنصر بمخزون منخفض (5 قطع أو أقل)
                                            </p>
                                        @endif
                                        @if($outOfStockItems->count() > 0)
                                            <p class="mb-0">
                                                <strong>{{ $outOfStockItems->count() }}</strong> عنصر نفذ مخزونه تماماً
                                            </p>
                                        @endif
                                    </div>
                                    <button class="btn btn-sm btn-outline-warning" onclick="$('#stockFilter').val('low'); applyFilters();">
                                        <i class="fas fa-filter me-1"></i>عرض المنخفض
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <!-- Quick Stats -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">إجمالي العناصر</h5>
                                                <h3 class="mb-0">{{ $inventory->count() }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-boxes fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">متاح</h5>
                                                <h3 class="mb-0">{{ $inventory->where('available_stock', '>', 5)->count() }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">منخفض</h5>
                                                <h3 class="mb-0">{{ $inventory->whereBetween('available_stock', [1, 5])->count() }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">نفذ</h5>
                                                <h3 class="mb-0">{{ $inventory->where('available_stock', '<=', 0)->count() }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-times-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search and Filter -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">البحث في المنتجات</label>
                                        <input type="text" class="form-control" id="searchInput" placeholder="اسم المنتج...">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">حالة المخزون</label>
                                        <select class="form-select" id="stockFilter">
                                            <option value="">الكل</option>
                                            <option value="available">متاح</option>
                                            <option value="low">منخفض</option>
                                            <option value="out">نفذ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">المقاس</label>
                                        <select class="form-select" id="sizeFilter">
                                            <option value="">الكل</option>
                                            @foreach(\App\Models\ProductSize::all() as $size)
                                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">اللون</label>
                                        <select class="form-select" id="colorFilter">
                                            <option value="">الكل</option>
                                            @foreach(\App\Models\ProductColor::all() as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-primary" id="applyFilters">
                                                <i class="fas fa-search me-1"></i>تطبيق
                                            </button>
                                            <button class="btn btn-outline-secondary" id="clearFilters">
                                                <i class="fas fa-times me-1"></i>مسح
                                            </button>
                                            <button class="btn btn-outline-info" id="refreshData">
                                                <i class="fas fa-sync-alt me-1"></i>تحديث
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cubes text-primary me-2"></i>
                                        إجمالي العناصر: <span id="totalItems">{{ $inventory->total() }}</span>
                                    </h6>
                                    <div class="text-muted small">
                                        آخر تحديث: <span id="lastUpdate">{{ now()->format('H:i:s') }}</span>
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="inventoryTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>المنتج</th>
                                                <th>المقاس</th>
                                                <th>اللون</th>
                                                <th>المخزون الكلي</th>
                                                <th>المخزون المستهلك</th>
                                                <th>المخزون المتاح</th>
                                                <th>السعر</th>
                                                <th>الحالة</th>
                                                <th>تحديث سريع</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($inventory as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item->product)
                                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                            <div>
                                                                <div class="fw-bold">{{ $item->product->name }}</div>
                                                                <small class="text-muted">{{ Str::limit($item->product->description, 30) }}</small>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">منتج غير موجود</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($item->size)
                                                        <span class="badge bg-info">{{ $item->size->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->color)
                                                        <div class="d-flex align-items-center">
                                                            <span class="color-circle me-2" style="background-color: {{ $item->color->code ?? '#ccc' }}"></span>
                                                            <span>{{ $item->color->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ $item->stock }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-warning fw-bold">{{ $item->consumed_stock }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $item->available_stock > 20 ? 'bg-success' : ($item->available_stock > 5 ? 'bg-warning' : 'bg-danger') }}" id="available-{{ $item->id }}">
                                                        {{ $item->available_stock }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="price-cell">
                                                        <span class="fw-bold text-primary">{{ $item->price ? number_format($item->price, 2) . ' ر.س' : '-' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="status-cell" id="status-{{ $item->id }}">
                                                        @if($item->is_available && $item->available_stock > 0)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle me-1"></i>متاح
                                                            </span>
                                                        @elseif($item->available_stock <= 5 && $item->available_stock > 0)
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>منخفض
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle me-1"></i>نفذ
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="quick-update-controls">
                                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="adjustStock({{ $item->id }}, -1)">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <input type="number" class="form-control text-center" id="stock-input-{{ $item->id }}" value="{{ $item->stock }}" min="0">
                                                            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="adjustStock({{ $item->id }}, 1)">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            <button class="btn btn-primary btn-sm" type="button" onclick="updateStock({{ $item->id }})">
                                                                <i class="fas fa-save"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel{{ $item->id }}">تأكيد الحذف</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    هل أنت متأكد من حذف هذا المخزون؟
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                                    <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger">حذف</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
                                                    <div class="alert alert-info mb-0">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        لا يوجد مخزون حالياً
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $inventory->links() }}
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
    .color-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    
    .quick-update-controls {
        min-width: 200px;
    }
    
    .status-cell .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    
    .price-cell {
        min-width: 100px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .loading-overlay {
        position: relative;
    }
    
    .loading-overlay::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #007bff;
    }
    
    .loading-overlay.loading::after {
        display: flex;
        content: '\f110';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        animation: fa-spin 2s infinite linear;
    }
    
    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .alert-floating {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endsection

@section('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
$(document).ready(function() {
    // إعداد CSRF token للـ AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // تطبيق الفلاتر
    $('#applyFilters').on('click', function() {
        applyFilters();
    });
    
    // مسح الفلاتر
    $('#clearFilters').on('click', function() {
        clearFilters();
    });
    
    // تحديث البيانات
    $('#refreshData').on('click', function() {
        refreshInventoryData();
    });
    
    // البحث المباشر
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });
    
    // تحديث تلقائي كل 30 ثانية
    setInterval(refreshInventoryData, 30000);
});

// دالة تحديث المخزون
function updateStock(inventoryId) {
    const stockInput = document.getElementById(`stock-input-${inventoryId}`);
    const newStock = parseInt(stockInput.value) || 0;
    
    if (newStock < 0) {
        showAlert('الكمية لا يمكن أن تكون أقل من صفر', 'warning');
        return;
    }
    
    // إظهار مؤشر التحميل
    const row = stockInput.closest('tr');
    row.classList.add('loading-overlay', 'loading');
    
    $.ajax({
        url: '{{ route("admin.inventory.quick-update") }}',
        method: 'POST',
        data: {
            inventory_id: inventoryId,
            stock: newStock
        },
        success: function(response) {
            if (response.success) {
                // تحديث العرض
                updateRowDisplay(inventoryId, response.inventory);
                showAlert('تم تحديث المخزون بنجاح', 'success');
                
                // تحديث آخر وقت تحديث
                updateLastUpdateTime();
            } else {
                showAlert(response.message || 'حدث خطأ أثناء التحديث', 'error');
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'حدث خطأ غير متوقع';
            showAlert(errorMessage, 'error');
        },
        complete: function() {
            // إخفاء مؤشر التحميل
            row.classList.remove('loading-overlay', 'loading');
        }
    });
}

// دالة تعديل المخزون (زيادة/نقصان)
function adjustStock(inventoryId, adjustment) {
    const stockInput = document.getElementById(`stock-input-${inventoryId}`);
    const currentValue = parseInt(stockInput.value) || 0;
    const newValue = Math.max(0, currentValue + adjustment);
    stockInput.value = newValue;
}

// دالة تحديث عرض الصف
function updateRowDisplay(inventoryId, inventory) {
    // تحديث المخزون المتاح
    const availableBadge = document.getElementById(`available-${inventoryId}`);
    if (availableBadge) {
        availableBadge.textContent = inventory.available_stock;
        availableBadge.className = `badge ${inventory.available_stock > 20 ? 'bg-success' : (inventory.available_stock > 5 ? 'bg-warning' : 'bg-danger')}`;
    }
    
    // تحديث الحالة
    const statusCell = document.getElementById(`status-${inventoryId}`);
    if (statusCell) {
        let statusHTML = '';
        if (inventory.is_available && inventory.available_stock > 0) {
            statusHTML = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>متاح</span>';
        } else if (inventory.available_stock <= 5 && inventory.available_stock > 0) {
            statusHTML = '<span class="badge bg-warning"><i class="fas fa-exclamation-triangle me-1"></i>منخفض</span>';
        } else {
            statusHTML = '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>نفذ</span>';
        }
        statusCell.innerHTML = statusHTML;
    }
    
    // تحديث input
    const stockInput = document.getElementById(`stock-input-${inventoryId}`);
    if (stockInput) {
        stockInput.value = inventory.stock;
    }
}

// دالة تطبيق الفلاتر
function applyFilters() {
    const searchTerm = $('#searchInput').val();
    const stockFilter = $('#stockFilter').val();
    const sizeFilter = $('#sizeFilter').val();
    const colorFilter = $('#colorFilter').val();
    
    $.ajax({
        url: '{{ route("admin.inventory.filter") }}',
        method: 'GET',
        data: {
            search: searchTerm,
            stock_status: stockFilter,
            size_id: sizeFilter,
            color_id: colorFilter
        },
        beforeSend: function() {
            $('#inventoryTable tbody').addClass('loading-overlay loading');
        },
        success: function(response) {
            if (response.success) {
                $('#inventoryTable tbody').html(response.html);
                $('#totalItems').text(response.total);
                updateLastUpdateTime();
            }
        },
        error: function() {
            showAlert('حدث خطأ أثناء تطبيق الفلاتر', 'error');
        },
        complete: function() {
            $('#inventoryTable tbody').removeClass('loading-overlay loading');
        }
    });
}

// دالة مسح الفلاتر
function clearFilters() {
    $('#searchInput').val('');
    $('#stockFilter').val('');
    $('#sizeFilter').val('');
    $('#colorFilter').val('');
    applyFilters();
}

// دالة تحديث البيانات
function refreshInventoryData() {
    applyFilters();
    showAlert('تم تحديث البيانات', 'info');
}

// دالة تحديث وقت آخر تحديث
function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('ar-SA', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    });
    $('#lastUpdate').text(timeString);
}

// دالة إظهار التنبيهات
function showAlert(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 
                       type === 'error' ? 'alert-danger' : 
                       type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-floating alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // إزالة التنبيهات القديمة
    $('.alert-floating').remove();
    
    // إضافة التنبيه الجديد
    $('body').append(alertHtml);
    
    // إزالة التنبيه تلقائياً بعد 5 ثوان
    setTimeout(() => {
        $('.alert-floating').fadeOut();
    }, 5000);
}
</script>
@endsection