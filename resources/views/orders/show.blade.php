@extends('layouts.customer')

@section('title', 'تفاصيل الطلب #' . $order->order_number)

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="/assets/css/customer/orders.css">
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

    // Load friends list on page load
    loadFriendsList();

    // Add friend form submission
    $('#addFriendForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            friend_name: $('#friend_name').val(),
            friend_email: $('#friend_email').val(),
            friend_phone: $('#friend_phone').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '{{ route("orders.friends.add", $order->uuid) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('تم إضافة الصديق بنجاح وتم إرسال رابط الوصول له', 'success');
                    $('#addFriendForm')[0].reset();
                    loadFriendsList(); // Reload friends list
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء إضافة الصديق', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء إضافة الصديق';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            }
        });
    });

    // Load friends list
    function loadFriendsList() {
        $.ajax({
            url: '{{ route("orders.friends.list", $order->uuid) }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayFriendsList(response.friends);
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء تحميل قائمة الأصدقاء', 'error');
                }
            },
            error: function(xhr) {
                showAlert('حدث خطأ أثناء تحميل قائمة الأصدقاء', 'error');
            }
        });
    }

    // Display friends list
    function displayFriendsList(friends) {
        if (friends.length === 0) {
            $('#friendsList').html(`
                <div class="text-center text-muted">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>لا يوجد أصدقاء مضافة لهذا الطلب</p>
                </div>
            `);
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-hover">';
        html += '<thead class="thead-light"><tr>';
        html += '<th>الاسم</th>';
        html += '<th>البريد الإلكتروني</th>';
        html += '<th>رقم الهاتف</th>';
        html += '<th>تاريخ الإضافة</th>';
        html += '<th>آخر وصول</th>';
        html += '<th>الإجراءات</th>';
        html += '</tr></thead><tbody>';

        friends.forEach(function(friend) {
            html += '<tr>';
            html += '<td><strong>' + friend.name + '</strong></td>';
            html += '<td>' + friend.email + '</td>';
            html += '<td>' + (friend.phone || '-') + '</td>';
            html += '<td>' + friend.created_at + '</td>';
            html += '<td>' + (friend.last_accessed || 'لم يصل بعد') + '</td>';
            html += '<td>';
            html += '<button type="button" class="btn btn-sm btn-danger remove-friend-btn" data-friend-id="' + friend.id + '">';
            html += '<i class="fas fa-user-times"></i> إزالة';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        $('#friendsList').html(html);
    }

    // Remove friend
    $(document).on('click', '.remove-friend-btn', function() {
        const friendId = $(this).data('friend-id');
        
        if (confirm('هل أنت متأكد من إزالة هذا الصديق؟')) {
            $.ajax({
                url: '{{ route("orders.friends.remove", $order->uuid) }}'.replace('{friend}', friendId),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('تم إزالة الصديق بنجاح', 'success');
                        loadFriendsList(); // Reload friends list
                    } else {
                        showAlert(response.message || 'حدث خطأ أثناء إزالة الصديق', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'حدث خطأ أثناء إزالة الصديق';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'error');
                }
            });
        }
    });

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
        
        $('.container').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection

@section('content')
<header class="header-container">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">تفاصيل الطلب #{{ $order->order_number }}</h2>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right"></i>
                العودة للطلبات
            </a>
        </div>
    </div>
</header>

<main class="container py-4">
    <div class="order-card">
        <div class="order-header">
            <div class="status-section">
                <h3 class="section-title">حالة الطلب</h3>
                <span class="status-badge status-{{ $order->order_status }}">
                    {{ match($order->order_status) {
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                        'processing' => 'قيد المعالجة',
                        'pending' => 'قيد الانتظار',
                        'out_for_delivery' => 'جاري التوصيل',
                        'on_the_way' => 'في الطريق',
                        'delivered' => 'تم التوصيل',
                        'returned' => 'مرتجع',
                        default => 'غير معروف'
                    } }}
                </span>
            </div>
            <div class="order-info mt-3">
                <p class="order-date">تاريخ الطلب: {{ $order->created_at->format('Y/m/d') }}</p>
            </div>
            @if($order->notes)
            <div class="order-notes mt-3">
                <h4>ملاحظات:</h4>
                <p>{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        <div class="order-details">
            <div class="row">
                <!-- معلومات الشحن -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h3 class="section-title">معلومات الشحن</h3>
                        <div class="shipping-info">
                            <div class="info-item">
                                <span class="info-label">العنوان:</span>
                                <span class="info-value">{{ $order->shipping_address }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">رقم الهاتف:</span>
                                <span class="info-value">{{ $order->phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ملخص الطلب -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h3 class="section-title">ملخص الطلب</h3>
                        <div class="order-items">
                            @foreach($order->items as $item)
                            <div class="order-item">
                                @if($item->product->images->first())
                                <img src="{{ url('storage/' . $item->product->images->first()->image_path) }}"
                                    alt="{{ $item->product->name }}"
                                    class="item-image">
                                @endif
                                <div class="item-details">
                                    <h4 class="item-name">{{ $item->product->name }}</h4>
                                    <p class="item-price">
                                        {{ $item->unit_price }} ريال × {{ $item->quantity }}
                                    </p>
                                    @if($item->color || $item->size)
                                    <p class="item-options">
                                        @if($item->color)
                                        <span class="item-color">اللون: {{ $item->color }}</span>
                                        @endif
                                        @if($item->size)
                                        <span class="item-size">المقاس: {{ $item->size }}</span>
                                        @endif
                                    </p>
                                    @endif
                                    <p class="item-subtotal">
                                        الإجمالي: {{ $item->subtotal }} ريال
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="order-summary mt-4">
                            <h5 class="mb-3 fw-bold">ملخص الطلب</h5>
                            <div class="card">
                                <div class="card-body p-4">
                                    <div class="summary-items">
                                        <div class="summary-item d-flex justify-content-between mb-3">
                                            <span>السعر الأصلي:</span>
                                            <span class="fw-bold">{{ number_format($order->original_amount, 2) }} ريال</span>
                                        </div>

                                        @if($order->quantity_discount > 0)
                                        <div class="summary-item d-flex justify-content-between mb-3 text-success">
                                            <span>خصم الكمية:</span>
                                            <span class="fw-bold">- {{ number_format($order->quantity_discount, 2) }} ريال</span>
                                        </div>
                                        @endif

                                        @if($order->coupon_discount > 0)
                                        <div class="summary-item d-flex justify-content-between mb-3 text-success">
                                            <span>خصم الكوبون:</span>
                                            <span class="fw-bold">- {{ number_format($order->coupon_discount, 2) }} ريال</span>
                                        </div>

                                        @if($order->coupon_code)
                                        <div class="summary-item d-flex justify-content-between mb-3">
                                            <span>كود الخصم:</span>
                                            <span class="badge badge-primary">{{ $order->coupon_code }}</span>
                                        </div>
                                        @endif
                                        @endif

                                        <div class="summary-item d-flex justify-content-between fw-bold total-row">
                                            <span>الإجمالي:</span>
                                            <span>{{ number_format($order->total_amount, 2) }} ريال</span>
                                        </div>
                                    </div>

                                    @if($order->quantity_discount > 0 || $order->coupon_discount > 0)
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        @if($order->quantity_discount > $order->coupon_discount)
                                            <span>تم تطبيق خصم الكمية ({{ number_format($order->quantity_discount, 2) }} ريال) لأنه أكبر من خصم الكوبون.</span>
                                        @elseif($order->coupon_discount > $order->quantity_discount)
                                            <span>تم تطبيق خصم الكوبون ({{ number_format($order->coupon_discount, 2) }} ريال) لأنه أكبر من خصم الكمية.</span>
                                        @elseif($order->coupon_discount == $order->quantity_discount && $order->coupon_discount > 0)
                                            <span>تم تطبيق خصم متساوٍ ({{ number_format($order->coupon_discount, 2) }} ريال) من كلا النوعين.</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- تتبع الطلب -->
        <div class="order-tracking mt-5 p-4">
            <h3 class="tracking-title text-center mb-4">تتبع الطلب</h3>

            <div class="tracking-stepper">
                <div class="tracking-step {{ $order->order_status != 'pending' ? 'completed' : '' }}">
                    <div class="step-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <h4>تم استلام الطلب</h4>
                        <p>تم استلام طلبك وهو قيد المراجعة</p>
                    </div>
                </div>

                <div class="tracking-step {{ in_array($order->order_status, ['processing', 'out_for_delivery', 'on_the_way', 'delivered', 'completed']) ? 'completed' : '' }}">
                    <div class="step-icon">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <h4>قيد المعالجة</h4>
                        <p>جاري تجهيز طلبك</p>
                    </div>
                </div>

                <div class="tracking-step {{ in_array($order->order_status, ['out_for_delivery', 'on_the_way', 'delivered', 'completed']) ? 'completed' : '' }}">
                    <div class="step-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <h4>جاري التوصيل</h4>
                        <p>تم تجهيز طلبك للتوصيل</p>
                    </div>
                </div>

                <div class="tracking-step {{ in_array($order->order_status, ['on_the_way', 'delivered', 'completed']) ? 'completed' : '' }}">
                    <div class="step-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <h4>في الطريق</h4>
                        <p>المندوب في طريقه إليك</p>
                    </div>
                </div>

                <div class="tracking-step {{ in_array($order->order_status, ['delivered', 'completed']) ? 'completed' : '' }}">
                    <div class="step-icon">
                        <i class="bi bi-house-check-fill"></i>
                    </div>
                    <div class="step-content">
                        <h4>تم التوصيل</h4>
                        <p>تم توصيل طلبك بنجاح</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إدارة الأصدقاء -->
    <div class="order-card mt-4">
        <div class="order-header">
            <h3 class="section-title">
                <i class="fas fa-user-friends"></i>
                إدارة الأصدقاء
            </h3>
            <p class="text-muted">أضف أصدقاء لتتبع طلبك وتحديث حالته</p>
        </div>
        
        <div class="order-details">
            <!-- إضافة صديق جديد -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-plus"></i> إضافة صديق جديد</h5>
                        </div>
                        <div class="card-body">
                            <form id="addFriendForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="friend_name">اسم الصديق *</label>
                                            <input type="text" class="form-control" id="friend_name" name="friend_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="friend_email">البريد الإلكتروني *</label>
                                            <input type="email" class="form-control" id="friend_email" name="friend_email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="friend_phone">رقم الهاتف</label>
                                            <input type="text" class="form-control" id="friend_phone" name="friend_phone">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="fas fa-user-plus"></i>
                                    إضافة الصديق
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قائمة الأصدقاء -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-users"></i> الأصدقاء المضافة</h5>
                        </div>
                        <div class="card-body">
                            <div id="friendsList">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">جاري التحميل...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
