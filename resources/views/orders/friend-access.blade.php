@extends('layouts.customer')

@section('title', 'تتبع الطلب - ' . $friend->order->order_number)

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-friends"></i>
                        تتبع الطلب - {{ $friend->order->order_number }}
                    </h4>
                    <small>مرحباً {{ $friend->friend_name }}، تم دعوتك لتتبع هذا الطلب</small>
                </div>
                <div class="card-body">
                    <!-- Order Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>معلومات الطلب</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>رقم الطلب:</strong></td>
                                    <td>{{ $friend->order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>صاحب الطلب:</strong></td>
                                    <td>{{ $friend->order->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>تاريخ الطلب:</strong></td>
                                    <td>{{ $friend->order->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>المبلغ الإجمالي:</strong></td>
                                    <td>{{ number_format($friend->order->total_amount, 2) }} ر.س</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>حالة الطلب</h5>
                            <div class="text-center">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'out_for_delivery' => 'primary',
                                        'on_the_way' => 'info',
                                        'delivered' => 'success',
                                        'returned' => 'danger'
                                    ];
                                    
                                    $statusTexts = [
                                        'pending' => 'معلق',
                                        'processing' => 'قيد المعالجة',
                                        'completed' => 'مكتمل',
                                        'cancelled' => 'ملغي',
                                        'out_for_delivery' => 'جاري التوصيل',
                                        'on_the_way' => 'في الطريق',
                                        'delivered' => 'تم التوصيل',
                                        'returned' => 'مرتجع'
                                    ];
                                @endphp
                                
                                <div class="alert alert-{{ $statusColors[$friend->order->order_status] ?? 'secondary' }}">
                                    <h4>{{ $statusTexts[$friend->order->order_status] ?? 'غير معروف' }}</h4>
                                </div>
                                
                                <!-- Status Update Form (if allowed) -->
                                <form id="statusUpdateForm" class="mt-3">
                                    <div class="form-group">
                                        <label for="order_status">تحديث حالة الطلب:</label>
                                        <select class="form-control" id="order_status" name="order_status">
                                            @foreach($statusTexts as $value => $text)
                                                <option value="{{ $value }}" {{ $friend->order->order_status == $value ? 'selected' : '' }}>
                                                    {{ $text }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">
                                        <i class="fas fa-save"></i>
                                        تحديث الحالة
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="row">
                        <div class="col-12">
                            <h5>المنتجات المطلوبة</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الكمية</th>
                                            <th>السعر</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($friend->order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item->product->images->count() > 0)
                                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                                 alt="{{ $item->product->name }}" 
                                                                 class="img-thumbnail mr-3" 
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item->product->name }}</strong>
                                                            @if($item->color)
                                                                <br><small class="text-muted">اللون: {{ $item->color }}</small>
                                                            @endif
                                                            @if($item->size)
                                                                <br><small class="text-muted">المقاس: {{ $item->size }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->product->price, 2) }} ر.س</td>
                                                <td>{{ number_format($item->quantity * $item->product->price, 2) }} ر.س</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>تفاصيل إضافية</h5>
                            <table class="table table-borderless">
                                @if($friend->order->original_amount != $friend->order->total_amount)
                                    <tr>
                                        <td><strong>المبلغ الأصلي:</strong></td>
                                        <td><del>{{ number_format($friend->order->original_amount, 2) }} ر.س</del></td>
                                    </tr>
                                @endif
                                @if($friend->order->coupon_discount > 0)
                                    <tr>
                                        <td><strong>خصم الكوبون:</strong></td>
                                        <td class="text-success">-{{ number_format($friend->order->coupon_discount, 2) }} ر.س</td>
                                    </tr>
                                @endif
                                @if($friend->order->quantity_discount > 0)
                                    <tr>
                                        <td><strong>خصم الكمية:</strong></td>
                                        <td class="text-info">-{{ number_format($friend->order->quantity_discount, 2) }} ر.س</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>طريقة الدفع:</strong></td>
                                    <td>{{ $friend->order->payment_method == 'cash' ? 'نقداً' : 'بطاقة ائتمان' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>حالة الدفع:</strong></td>
                                    <td>
                                        @if($friend->order->payment_status == 'paid')
                                            <span class="badge badge-success">مدفوع</span>
                                        @elseif($friend->order->payment_status == 'pending')
                                            <span class="badge badge-warning">معلق</span>
                                        @else
                                            <span class="badge badge-danger">فشل</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>معلومات التوصيل</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>العنوان:</strong></td>
                                    <td>{{ $friend->order->shipping_address }}</td>
                                </tr>
                                <tr>
                                    <td><strong>رقم الهاتف:</strong></td>
                                    <td>{{ $friend->order->phone }}</td>
                                </tr>
                                @if($friend->order->notes)
                                    <tr>
                                        <td><strong>ملاحظات:</strong></td>
                                        <td>{{ $friend->order->notes }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Admin Assignment Info -->
                    @if($friend->order->assignedAdmin)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-user-tie"></i> الأدمن المسؤول</h6>
                                    <p class="mb-0">
                                        <strong>{{ $friend->order->assignedAdmin->name }}</strong>
                                        <br>
                                        <small class="text-muted">تم التعيين في: {{ $friend->order->assigned_at->format('Y-m-d H:i') }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Access Info -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-secondary">
                                <h6><i class="fas fa-info-circle"></i> معلومات الوصول</h6>
                                <p class="mb-0">
                                    <strong>اسمك:</strong> {{ $friend->friend_name }}
                                    <br>
                                    <strong>بريدك الإلكتروني:</strong> {{ $friend->friend_email }}
                                    @if($friend->friend_phone)
                                        <br>
                                        <strong>رقم هاتفك:</strong> {{ $friend->friend_phone }}
                                    @endif
                                    <br>
                                    <strong>آخر وصول:</strong> {{ $friend->last_accessed_at ? $friend->last_accessed_at->format('Y-m-d H:i') : 'الآن' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

    // Status update form submission
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();
        
        const newStatus = $('#order_status').val();
        const accessToken = '{{ $friend->access_token }}';
        
        $.ajax({
            url: '{{ route("orders.friend-update-status", $friend->access_token) }}',
            type: 'POST',
            data: {
                order_status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('تم تحديث حالة الطلب بنجاح', 'success');
                    
                    // Update the status display
                    updateStatusDisplay(newStatus);
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء تحديث الحالة', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء تحديث الحالة';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            }
        });
    });

    function updateStatusDisplay(status) {
        const statusColors = {
            'pending': 'warning',
            'processing': 'info',
            'completed': 'success',
            'cancelled': 'danger',
            'out_for_delivery': 'primary',
            'on_the_way': 'info',
            'delivered': 'success',
            'returned': 'danger'
        };
        
        const statusTexts = {
            'pending': 'معلق',
            'processing': 'قيد المعالجة',
            'completed': 'مكتمل',
            'cancelled': 'ملغي',
            'out_for_delivery': 'جاري التوصيل',
            'on_the_way': 'في الطريق',
            'delivered': 'تم التوصيل',
            'returned': 'مرتجع'
        };
        
        const alertDiv = $('.alert:has(h4)');
        alertDiv.removeClass().addClass(`alert alert-${statusColors[status] || 'secondary'}`);
        alertDiv.find('h4').text(statusTexts[status] || 'غير معروف');
    }

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('.card-body').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection

