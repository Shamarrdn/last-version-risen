@extends('layouts.superadmin')

@section('title', 'تقرير مبيعات المشرفين')

@section('page_title', 'تقرير مبيعات المشرفين')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="activity-section bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="activity-title mb-0">إحصائيات مبيعات المشرفين</h5>
                <div>
                    <button class="btn btn-sm btn-outline-primary me-2" id="printReport">
                        <i class="fas fa-print"></i> طباعة التقرير
                    </button>
                    <button class="btn btn-sm btn-outline-success" id="exportExcel">
                        <i class="fas fa-file-excel"></i> تصدير Excel
                    </button>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="p-3 border-bottom">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="dateRange" class="form-label">الفترة الزمنية</label>
                        <select class="form-select" id="dateRange" name="dateRange">
                            <option value="all">جميع الفترات</option>
                            <option value="today">اليوم</option>
                            <option value="week">هذا الأسبوع</option>
                            <option value="month">هذا الشهر</option>
                            <option value="year">هذا العام</option>
                            <option value="custom">مخصص</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="adminFilter" class="form-label">المشرف</label>
                        <select class="form-select" id="adminFilter" name="adminFilter">
                            <option value="all">جميع المشرفين</option>
                            @foreach($adminUsers as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="orderStatus" class="form-label">حالة الطلبات</label>
                        <select class="form-select" id="orderStatus" name="orderStatus">
                            <option value="all">جميع الحالات</option>
                            <option value="pending">معلق</option>
                            <option value="processing">قيد المعالجة</option>
                            <option value="completed">مكتمل</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">تطبيق الفلتر</button>
                    </div>
                </form>
            </div>
            
            <!-- Admin Sales Summary -->
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">إجمالي المبيعات</h6>
                                <h3 class="card-text">{{ number_format(collect($adminSalesData)->sum('totalSales')) }} ريال</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">مبيعات اليوم</h6>
                                <h3 class="card-text">{{ number_format(collect($adminSalesData)->sum('todaySales')) }} ريال</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">إجمالي الطلبات المعلقة</h6>
                                <h3 class="card-text">{{ collect($adminSalesData)->sum('pendingOrders') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">إجمالي الطلبات المكتملة</h6>
                                <h3 class="card-text">{{ collect($adminSalesData)->sum('completedOrders') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Admin Sales Table -->
            <div class="table-responsive">
                <table class="table table-hover mb-0 admin-sales-table">
                    <thead>
                        <tr>
                            <th>المشرف</th>
                            <th>البريد الإلكتروني</th>
                            <th>إجمالي المبيعات</th>
                            <th>مبيعات اليوم</th>
                            <th>الطلبات المعلقة</th>
                            <th>الطلبات قيد المعالجة</th>
                            <th>الطلبات المكتملة</th>
                            <th>نسبة المبيعات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adminUsers as $admin)
                            <tr data-admin-id="{{ $admin->id }}" 
                                data-total-sales="{{ $adminSalesData[$admin->id]['totalSales'] ?? 0 }}"
                                data-today-sales="{{ $adminSalesData[$admin->id]['todaySales'] ?? 0 }}"
                                data-pending-orders="{{ $adminSalesData[$admin->id]['pendingOrders'] ?? 0 }}"
                                data-processing-orders="{{ $adminSalesData[$admin->id]['processingOrders'] ?? 0 }}"
                                data-completed-orders="{{ $adminSalesData[$admin->id]['completedOrders'] ?? 0 }}">
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ number_format($adminSalesData[$admin->id]['totalSales'] ?? 0) }} ريال</td>
                                <td>{{ number_format($adminSalesData[$admin->id]['todaySales'] ?? 0) }} ريال</td>
                                <td>
                                    <span class="badge bg-warning">
                                        {{ $adminSalesData[$admin->id]['pendingOrders'] ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $adminSalesData[$admin->id]['processingOrders'] ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $adminSalesData[$admin->id]['completedOrders'] ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $totalSalesSum = collect($adminSalesData)->sum('totalSales');
                                        $percentage = $totalSalesSum > 0 ? 
                                            round(($adminSalesData[$admin->id]['totalSales'] ?? 0) / $totalSalesSum * 100, 1) : 0;
                                    @endphp
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%">
                                            {{ $percentage }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary view-details" data-admin-id="{{ $admin->id }}">
                                        <i class="fas fa-eye"></i> التفاصيل
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">لا يوجد مشرفين</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Admin Sales Charts -->
    <div class="col-12">
        <div class="chart-container bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom">
                <h5 class="activity-title">مقارنة مبيعات المشرفين</h5>
            </div>
            <div class="chart-wrapper position-relative" style="height: 400px;">
                <canvas id="adminSalesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Admin Details Modal -->
<div class="modal fade" id="adminDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل مبيعات المشرف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="adminDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Admin Sales Chart
    const adminSalesChart = new Chart(
        document.getElementById('adminSalesChart').getContext('2d'),
        {
            type: 'bar',
            data: {
                labels: [
                    @foreach($adminUsers as $admin)
                        '{{ $admin->name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'إجمالي المبيعات (ريال)',
                    data: [
                        @foreach($adminUsers as $admin)
                            {{ $adminSalesData[$admin->id]['totalSales'] ?? 0 }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgb(13, 110, 253)',
                    borderWidth: 2,
                    borderRadius: 5,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' ريال';
                            },
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' ريال';
                            }
                        }
                    }
                }
            }
        }
    );
    
    // Admin Details Modal
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const adminId = this.getAttribute('data-admin-id');
            const adminName = this.closest('tr').querySelector('td:first-child').textContent;
            
            // Show modal with loading spinner
            const modal = new bootstrap.Modal(document.getElementById('adminDetailsModal'));
            modal.show();
            
                    // Get admin data from the table
        const adminId = this.getAttribute('data-admin-id');
        const adminRow = document.querySelector(`tr[data-admin-id="${adminId}"]`);
        
        if (adminRow) {
            const totalSales = adminRow.getAttribute('data-total-sales') || '0';
            const todaySales = adminRow.getAttribute('data-today-sales') || '0';
            const pendingOrders = adminRow.getAttribute('data-pending-orders') || '0';
            const processingOrders = adminRow.getAttribute('data-processing-orders') || '0';
            const completedOrders = adminRow.getAttribute('data-completed-orders') || '0';
            
            // Update the details content
            document.getElementById('adminTotalSales').textContent = Number(totalSales).toLocaleString() + ' ريال';
            document.getElementById('adminTodaySales').textContent = Number(todaySales).toLocaleString() + ' ريال';
            document.getElementById('adminTotalOrders').textContent = Number(pendingOrders) + Number(processingOrders) + Number(completedOrders);
            
            // Update chart data
            const chartData = [Number(pendingOrders), Number(processingOrders), Number(completedOrders)];
            if (window.adminOrderStatusChart) {
                window.adminOrderStatusChart.data.datasets[0].data = chartData;
                window.adminOrderStatusChart.update();
            }
        }
        
        setTimeout(() => {
                document.getElementById('adminDetailsContent').innerHTML = `
                    <h4 class="mb-4 text-center">تفاصيل مبيعات المشرف: ${adminName}</h4>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>إحصائيات المبيعات</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>إجمالي المبيعات</th>
                                    <td id="adminTotalSales">جاري التحميل...</td>
                                </tr>
                                <tr>
                                    <th>مبيعات اليوم</th>
                                    <td id="adminTodaySales">جاري التحميل...</td>
                                </tr>
                                <tr>
                                    <th>عدد الطلبات</th>
                                    <td id="adminTotalOrders">جاري التحميل...</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>توزيع حالات الطلبات</h5>
                            <canvas id="adminOrderStatusChart" height="200"></canvas>
                        </div>
                    </div>
                    <h5>المنتجات الأكثر مبيعاً</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية</th>
                                <th>الإيرادات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>منتج تجريبي 1</td>
                                <td>24</td>
                                <td>1,200 ريال</td>
                            </tr>
                            <tr>
                                <td>منتج تجريبي 2</td>
                                <td>18</td>
                                <td>900 ريال</td>
                            </tr>
                            <tr>
                                <td>منتج تجريبي 3</td>
                                <td>12</td>
                                <td>600 ريال</td>
                            </tr>
                        </tbody>
                    </table>
                `;
                
                // Create order status chart for the admin
                window.adminOrderStatusChart = new Chart(
                    document.getElementById('adminOrderStatusChart').getContext('2d'),
                    {
                        type: 'pie',
                        data: {
                            labels: ['معلق', 'قيد المعالجة', 'مكتمل'],
                            datasets: [{
                                data: [0, 0, 0], // سيتم تحديثها عبر JavaScript
                                backgroundColor: [
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(13, 202, 240, 0.8)',
                                    'rgba(25, 135, 84, 0.8)'
                                ],
                                borderColor: [
                                    'rgb(255, 193, 7)',
                                    'rgb(13, 202, 240)',
                                    'rgb(25, 135, 84)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    }
                );
            }, 500);
        });
    });
    
    // Print report functionality
    document.getElementById('printReport').addEventListener('click', function() {
        window.print();
    });
    
    // Export to Excel functionality (simulated)
    document.getElementById('exportExcel').addEventListener('click', function() {
        alert('سيتم تصدير البيانات إلى ملف Excel');
        // In a real application, you would implement the Excel export here
    });
</script>

@if(isset($error))
<script>
    // عرض رسالة الخطأ إذا وجدت
    Swal.fire({
        title: 'خطأ!',
        text: '{{ $error }}',
        icon: 'error',
        confirmButtonText: 'حسناً'
    });
</script>
@endif
@endsection

@section('styles')
<style>
    /* Admin Sales Table */
    .admin-sales-table th, 
    .admin-sales-table td {
        text-align: center;
        vertical-align: middle;
    }
    
    .admin-sales-table .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    
    .progress {
        height: 20px;
    }
    
    .progress-bar {
        line-height: 20px;
        font-size: 0.75rem;
    }
    
    /* Print Styles */
    @media print {
        .navbar, .sidebar, .btn, form, .modal, .footer {
            display: none !important;
        }
        
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        
        .chart-wrapper {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
