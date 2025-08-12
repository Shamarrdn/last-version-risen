@extends($adminLayout)

@section('title', 'التقارير')
@section('page_title', 'التقارير')

@section('content')
<div class="container-fluid">
    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">تصفية التقارير</h5>
        </div>
        <div class="card-body">
            @include('admin.reports._filters')
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <!-- Sales Growth -->
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 {{ $salesReport['growth']['trend'] === 'up' ? 'bg-success' : 'bg-danger' }} rounded p-3">
                            <i class="fas {{ $salesReport['growth']['trend'] === 'up' ? 'fa-arrow-up' : 'fa-arrow-down' }} text-white"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">نمو المبيعات</h6>
                            <h4 class="mb-0">{{ number_format($salesReport['growth']['percentage'], 1) }}%</h4>
                            <small class="text-muted">مقارنة بالفترة السابقة</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary rounded p-3">
                            <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">إجمالي المبيعات</h6>
                            <h4 class="mb-0">{{ number_format($salesReport['total_sales'], 2) }} ر.س</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info rounded p-3">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">إجمالي الطلبات</h6>
                            <h4 class="mb-0">{{ $salesReport['orders_count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning rounded p-3">
                            <i class="fas fa-receipt text-white"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">متوسط قيمة الطلب</h6>
                            <h4 class="mb-0">{{ number_format($salesReport['average_order_value'], 2) }} ر.س</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Comparison -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">مقارنة الفترات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="p-4 bg-light rounded">
                        <h6 class="text-muted">الفترة الحالية</h6>
                        <h3 class="mb-0">{{ number_format($salesReport['growth']['current_amount'], 2) }} ر.س</h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-light rounded">
                        <h6 class="text-muted">الفترة السابقة</h6>
                        <h3 class="mb-0">{{ number_format($salesReport['growth']['previous_amount'], 2) }} ر.س</h3>
                    </div> 
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">اتجاه المبيعات</h5>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 400px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">أفضل المنتجات مبيعاً</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th class="text-end">الكمية المباعة</th>
                            <th class="text-end">الإيرادات</th>
                            <th class="text-end">الاتجاه</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesReport['top_products'] as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td class="text-end">{{ number_format($product['total_quantity']) }}</td>
                            <td class="text-end">{{ number_format($product['total_revenue'], 2) }} ر.س</td>
                            <td class="text-end">
                                @if($product['trend'] != 0)
                                <span class="badge {{ $product['trend'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product['trend'] > 0 ? '↑' : '↓' }} {{ abs($product['trend']) }}%
                                </span>
                                @else
                                <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">لا توجد بيانات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Peak Hours -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">ساعات الذروة</h5>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 300px;">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">أفضل العملاء</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>العميل</th>
                            <th class="text-end">عدد الطلبات</th>
                            <th class="text-end">إجمالي المشتريات</th>
                            <th class="text-end">متوسط الطلب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesReport['top_customers'] as $customer)
                        <tr>
                            <td>{{ $customer->user->name }}</td>
                            <td class="text-end">{{ $customer->orders }}</td>
                            <td class="text-end">{{ number_format($customer->total / 100, 2) }} ر.س</td>
                            <td class="text-end">{{ number_format(($customer->total / $customer->orders) / 100, 2) }} ر.س</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($salesReport['daily_data']);

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: Object.keys(salesData),
                datasets: [{
                    label: 'المبيعات',
                    data: Object.values(salesData).map(d => d.sales),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 3
                }, {
                    label: 'الطلبات',
                    data: Object.values(salesData).map(d => d.orders),
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: true
                    },
                    tooltip: {
                        rtl: true,
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label === 'المبيعات') {
                                    return `المبيعات: ${context.raw.toLocaleString('ar-SA')} ر.س`;
                                }
                                return `الطلبات: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('ar-SA');
                            }
                        }
                    }
                }
            }
        });

        // Peak Hours Chart
        const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
        const peakData = @json($salesReport['peak_hours']);

        new Chart(peakCtx, {
            type: 'bar',
            data: {
                labels: peakData.map(d => `${d.hour}:00`),
                datasets: [{
                    label: 'عدد الطلبات',
                    data: peakData.map(d => d.count),
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endsection

@section('styles')
<style>
    .chart-container {
        position: relative;
        margin: auto;
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endsection
