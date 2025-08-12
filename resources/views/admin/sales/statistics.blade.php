@extends($adminLayout)

@section('title', 'إحصائيات المبيعات حسب المنتج')

@section('styles')
<style>
    .statistics-card {
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .chart-container {
        height: 400px;
    }
    .filters-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .form-control, .form-select {
        margin-bottom: 10px;
    }
    .table-responsive {
        margin-top: 20px;
    }
    .stats-summary-card {
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        color: white;
        text-align: center;
    }
    .stats-summary-card h3 {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stats-summary-card p {
        font-size: 0.9rem;
        margin-bottom: 0;
        opacity: 0.9;
    }
    .items-sold {
        background: linear-gradient(45deg, #3f51b5, #7986cb);
    }
    .products-sold {
        background: linear-gradient(45deg, #009688, #4db6ac);
    }
    .total-sales {
        background: linear-gradient(45deg, #f44336, #ef9a9a);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">إحصائيات المبيعات حسب المنتج</h4>

            <div class="filters-card">
                <form action="{{ route('admin.sales.statistics') }}" method="GET" class="row">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">من تاريخ</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">إلى تاريخ</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">تطبيق الفلتر</button>
                    </div>
                </form>
            </div>

            <!-- ملخص الإحصائيات -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-summary-card items-sold">
                        <h3>{{ number_format($totalItemsSold) }}</h3>
                        <p>إجمالي القطع المباعة</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-summary-card products-sold">
                        <h3>{{ number_format($totalProducts) }}</h3>
                        <p>عدد المنتجات المباعة</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-summary-card total-sales">
                        <h3>{{ number_format($totalSales, 2) }} ريال</h3>
                        <p>إجمالي المبيعات</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="statistics-card">
                        <div class="card-header p-3">
                            <h5 class="mb-0">الكميات المباعة من كل منتج</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="productSalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">تفاصيل المبيعات حسب المنتج</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">المنتج</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">الكمية المباعة</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">إجمالي المبيعات</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">متوسط السعر</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">نسبة من الإجمالي</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salesTable as $index => $item)
                                        <tr>
                                            <td>
                                                <div class="px-2 py-1">
                                                    {{ $index + 1 }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">{{ number_format($item['quantity']) }} قطعة</span>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">{{ number_format($item['total_sales'], 2) }} ريال</span>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">{{ number_format($item['average_price'], 2) }} ريال</span>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">{{ number_format(($item['quantity'] / $totalItemsSold) * 100, 1) }}%</span>
                                            </td>
                                            <td>
                                            <a href="{{ route('admin.products.show', parameters: \App\Models\Product::find($item['id'])) }}" class="btn btn-sm btn-info">تفاصيل المنتج</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">لا توجد بيانات مبيعات متاحة للفترة المحددة</td>
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
    </div>
</div>

<input type="hidden" id="chart-labels" value="{{ json_encode($chartLabels) }}">
<input type="hidden" id="chart-data" value="{{ json_encode($chartData) }}">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحويل البيانات من PHP إلى JavaScript
    const chartLabels = JSON.parse(document.getElementById('chart-labels').value);
    const chartData = JSON.parse(document.getElementById('chart-data').value);

    // إنشاء المخطط
    const ctx = document.getElementById('productSalesChart').getContext('2d');
    const productSalesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'الكمية المباعة (قطع)',
                data: chartData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'عدد القطع المباعة'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'المنتجات'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'إحصائيات الكميات المباعة حسب المنتج'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' قطعة';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
