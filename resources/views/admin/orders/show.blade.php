@extends($adminLayout)

@section('title', 'تفاصيل الطلب #' . $order->order_number)
@section('page_title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="orders-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <span class="icon-circle bg-primary text-white me-2">
                                                    <i class="fas fa-info-circle"></i>
                                                </span>
                                                تفاصيل الطلب #{{ $order->order_number }}
                                            </h5>
                                            <p class="text-muted mb-0 fs-sm">عرض تفاصيل الطلب والمنتجات</p>
                                        </div>
                                        <div class="actions d-flex gap-2">
                                            <a href="{{ route('admin.orders.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-2"></i>
                                                عودة للطلبات
                                            </a>
                                            <button onclick="window.print()" class="btn btn-light-primary">
                                                <i class="fas fa-print me-2"></i>
                                                طباعة الطلب
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Stats -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-primary me-3">
                                                <i class="fas fa-shopping-cart fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">رقم الطلب</h6>
                                                <h3 class="text-white mb-0">#{{ $order->order_number }}</h3>
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
                                                <i class="fas fa-box-open fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">عدد المنتجات</h6>
                                                <h3 class="text-white mb-0">{{ $order->items->count() }}</h3>
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
                                                <h6 class="text-white mb-1">إجمالي الطلب</h6>
                                                <h3 class="text-white mb-0">{{ number_format($order->total_amount) }} ريال</h3>
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
                                                <h6 class="text-white mb-1">تاريخ الطلب</h6>
                                                <h3 class="text-white mb-0">{{ $order->created_at->format('Y/m/d') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="row g-4">
                            <!-- Order Info -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 d-flex align-items-center">
                                            <span class="icon-circle bg-primary text-white me-2">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                            معلومات الطلب
                                        </h5>
                                        <div class="info-list">
                                            <div class="info-item d-flex justify-content-between py-2">
                                                <span class="text-muted">حالة الطلب</span>
                                                <div>
                                                    <select name="order_status" class="form-select form-select-sm d-inline-block w-auto me-2"
                                                        onchange="this.form.submit()" form="update-status-form">
                                                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                                        <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                                        <option value="out_for_delivery" {{ $order->order_status === 'out_for_delivery' ? 'selected' : '' }}>جاري التوصيل</option>
                                                        <option value="on_the_way" {{ $order->order_status === 'on_the_way' ? 'selected' : '' }}>في الطريق</option>
                                                        <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>تم التوصيل</option>
                                                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                                        <option value="returned" {{ $order->order_status === 'returned' ? 'selected' : '' }}>مرتجع</option>
                                                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                                    </select>
                                                    <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} rounded-pill">
                                                        {{ $order->status_text }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="info-item d-flex justify-content-between py-2">
                                                <span class="text-muted">طريقة الدفع</span>
                                                <span>{{ $order->payment_method === 'cash' ? 'كاش' : 'بطاقة' }}</span>
                                            </div>
                                            <div class="info-item d-flex justify-content-between py-2">
                                                <span class="text-muted">حالة الدفع</span>
                                                <div>
                                                    <select name="payment_status" class="form-select form-select-sm d-inline-block w-auto me-2"
                                                        onchange="this.form.submit()" form="update-payment-status-form">
                                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>تم الدفع</option>
                                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>فشل الدفع</option>
                                                    </select>
                                                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}-subtle
                                                                 text-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }} rounded-pill">
                                                        {{ $order->payment_status === 'paid' ? 'تم الدفع' : ($order->payment_status === 'pending' ? 'قيد الانتظار' : 'فشل الدفع') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 d-flex align-items-center">
                                            <span class="icon-circle bg-primary text-white me-2">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            معلومات العميل
                                        </h5>
                                        <div class="customer-info">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="avatar-circle bg-primary text-white me-3">
                                                    {{ substr($order->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $order->user->name }}</h6>
                                                    <p class="text-muted mb-0">{{ $order->user->email }}</p>
                                                </div>
                                            </div>
                                            <div class="info-list">
                                                <div class="info-item d-flex align-items-center py-2">
                                                    <i class="fas fa-phone text-primary me-3"></i>
                                                    <span>{{ $order->phone }}</span>
                                                </div>
                                                <div class="info-item d-flex align-items-center py-2">
                                                    <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                                    <span>{{ $order->shipping_address }}</span>
                                                </div>
                                                @if($order->notes)
                                                <div class="info-item d-flex align-items-center py-2">
                                                    <i class="fas fa-sticky-note text-primary me-3"></i>
                                                    <span>{{ $order->notes }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Actions -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 d-flex align-items-center">
                                            <span class="icon-circle bg-primary text-white me-2">
                                                <i class="fas fa-cogs"></i>
                                            </span>
                                            إجراءات الطلب
                                        </h5>
                                        <div class="row g-3">
                                            <!-- Assignment Actions -->
                                            <div class="col-md-6">
                                                <div class="action-section">
                                                    <h6 class="mb-3">
                                                        <i class="fas fa-user-tie me-2"></i>
                                                        تعيين الطلب
                                                    </h6>
                                                    <div class="d-flex gap-2">
                                                        @if(!$order->isAssigned())
                                                            <button type="button" class="btn btn-success assign-order-btn" data-order-uuid="{{ $order->uuid }}">
                                                                <i class="fas fa-hand-paper me-2"></i>
                                                                استلام الطلب
                                                            </button>
                                                        @elseif($order->isAssignedTo(auth()->id()))
                                                            <button type="button" class="btn btn-warning unassign-order-btn" data-order-uuid="{{ $order->uuid }}">
                                                                <i class="fas fa-times me-2"></i>
                                                                إلغاء الاستلام
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-secondary" disabled>
                                                                <i class="fas fa-lock me-2"></i>
                                                                مخصص لأدمن آخر
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @if($order->isAssigned())
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-user me-1"></i>
                                                                مخصص لـ: <strong>{{ $order->assignedAdmin->name }}</strong>
                                                                <br>
                                                                <i class="fas fa-clock me-1"></i>
                                                                في: {{ $order->assigned_at->format('Y-m-d H:i') }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Friend Management -->
                                            <div class="col-md-6">
                                                <div class="action-section">
                                                    <h6 class="mb-3">
                                                        <i class="fas fa-user-friends me-2"></i>
                                                        إدارة الأصدقاء
                                                    </h6>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addFriendModal">
                                                            <i class="fas fa-user-plus me-2"></i>
                                                            إضافة صديق
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#friendsListModal">
                                                            <i class="fas fa-users me-2"></i>
                                                            عرض الأصدقاء
                                                        </button>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            إضافة أصدقاء لتتبع الطلب وتحديث حالته
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Transfer Management -->
                                            @if($order->isAssignedTo(auth()->id()))
                                            <div class="col-md-6">
                                                <div class="action-section">
                                                    <h6 class="mb-3">
                                                        <i class="fas fa-exchange-alt me-2"></i>
                                                        نقل الطلب
                                                    </h6>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#transferOrderModal">
                                                            <i class="fas fa-exchange-alt me-2"></i>
                                                            نقل الطلب
                                                        </button>
                                                        <a href="{{ route('admin.transfers.index') }}" class="btn btn-outline-warning">
                                                            <i class="fas fa-list me-2"></i>
                                                            طلبات النقل
                                                        </a>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            نقل الطلب إلى أدمن آخر مع موافقة الطرف الآخر
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Products List -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 d-flex align-items-center">
                                            <span class="icon-circle bg-primary text-white me-2">
                                                <i class="fas fa-shopping-bag"></i>
                                            </span>
                                            منتجات الطلب
                                        </h5>

                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 40px">#</th>
                                                        <th>المنتج</th>
                                                        <th>السعر</th>
                                                        <th>الكمية</th>
                                                        <th>المجموع</th>
                                                        <th>الخيارات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->items as $item)
                                                    <tr>
                                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0">
                                                                    @if($item->product->image)
                                                                    <img src="{{ asset($item->product->image) }}"
                                                                        class="product-image border"
                                                                        width="60" height="60"
                                                                        alt="{{ $item->product->name }}">
                                                                    @else
                                                                    <div class="product-image border d-flex align-items-center justify-content-center bg-light">
                                                                        <i class="fas fa-box text-muted fa-lg"></i>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="mb-1 fw-bold">{{ $item->product->name }}</h6>
                                                                    @if($item->product->category)
                                                                    <span class="badge bg-primary-subtle text-primary">
                                                                        {{ $item->product->category->name }}
                                                                    </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                                                <span class="fw-bold">{{ number_format($item->unit_price) }}</span>
                                                                <small class="text-muted ms-1">ريال</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark fw-bold">
                                                                {{ $item->quantity }} قطعة
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-calculator text-primary me-2"></i>
                                                                <span class="fw-bold">{{ number_format($item->subtotal) }}</span>
                                                                <small class="text-muted ms-1">ريال</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($item->color || $item->size)
                                                            <div class="options-card p-2">
                                                                @if($item->color)
                                                                <div class="mb-1">
                                                                    <i class="fas fa-palette text-primary me-1"></i>
                                                                    <span class="text-muted">اللون:</span>
                                                                    <span class="fw-bold">{{ $item->color }}</span>
                                                                </div>
                                                                @endif
                                                                @if($item->size)
                                                                <div>
                                                                    <i class="fas fa-ruler text-primary me-1"></i>
                                                                    <span class="text-muted">المقاس:</span>
                                                                    <span class="fw-bold">{{ $item->size }}</span>
                                                                </div>
                                                                @endif
                                                            </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Contact Information -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title mb-4 d-flex align-items-center">
                                        <span class="icon-circle bg-primary text-white me-2">
                                            <i class="fas fa-address-book"></i>
                                        </span>
                                        معلومات الاتصال الإضافية
                                    </h5>

                                    @if($additionalAddresses->isNotEmpty())
                                    <div class="mb-4">
                                        <h6 class="mb-3">العناوين الإضافية</h6>
                                        <div class="row g-3">
                                            @foreach($additionalAddresses as $address)
                                            <div class="col-md-6">
                                                <div class="address-card bg-light p-3 rounded">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                        <span class="fw-bold">{{ $address->type_text }}</span>
                                                    </div>
                                                    <p class="mb-0">{{ $address->full_address }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if($additionalPhones->isNotEmpty())
                                    <div>
                                        <h6 class="mb-3">أرقام الهواتف الإضافية</h6>
                                        <div class="row g-3">
                                            @foreach($additionalPhones as $phone)
                                            <div class="col-md-4">
                                                <div class="phone-card bg-light p-3 rounded">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-phone text-primary me-2"></i>
                                                        <div>
                                                            <div class="fw-bold">{{ $phone->phone }}</div>
                                                            <small class="text-muted">{{ $phone->type_text }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if($additionalAddresses->isEmpty() && $additionalPhones->isEmpty())
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle mb-2 fa-2x"></i>
                                        <p class="mb-0">لا توجد معلومات اتصال إضافية</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-md-6">
                            <div class="card bg-white border-0 shadow-sm order-summary-clean">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="card-title mb-0 d-flex align-items-center">
                                        <span class="icon-circle bg-primary text-white me-2">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </span>
                                        ملخص الطلب
                                    </h5>
                                </div>
                                <div class="card-body py-0">
                                    <div class="order-summary-list">
                                        <div class="summary-item d-flex justify-content-between py-3 border-bottom">
                                            <span class="text-muted">رقم الطلب:</span>
                                            <strong class="text-dark">#{{ $order->order_number }}</strong>
                                        </div>
                                        <div class="summary-item d-flex justify-content-between py-3 border-bottom">
                                            <span class="text-muted">تاريخ الطلب:</span>
                                            <span>{{ $order->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                        <div class="summary-item d-flex justify-content-between py-3 border-bottom">
                                            <span class="text-muted">السعر الأصلي:</span>
                                            <span>{{ number_format($order->original_amount, 2) }} ريال</span>
                                        </div>
                                        @if($order->quantity_discount > 0)
                                        <div class="summary-item d-flex justify-content-between py-3 border-bottom">
                                            <span class="text-muted">خصم الكمية:</span>
                                            <span class="text-success">- {{ number_format($order->quantity_discount, 2) }} ريال</span>
                                        </div>
                                        @endif
                                        @if($order->coupon_discount > 0)
                                        <div class="summary-item d-flex justify-content-between py-3 border-bottom">
                                            <span class="text-muted">خصم الكوبون:</span>
                                            <span class="text-success">- {{ number_format($order->coupon_discount, 2) }} ريال</span>
                                        </div>
                                        @if($order->coupon_code)
                                        <div class="summary-item d-flex justify-content-between py-3 border-bottom">
                                            <span class="text-muted">كود الخصم:</span>
                                            <span><span class="badge bg-primary">{{ $order->coupon_code }}</span></span>
                                        </div>
                                        @endif
                                        @endif
                                        <div class="summary-item d-flex justify-content-between py-3 bg-light rounded-3 mt-2 mb-2">
                                            <strong class="text-primary fs-5">إجمالي الطلب:</strong>
                                            <strong class="text-primary fs-5">{{ number_format($order->total_amount, 2) }} ريال</strong>
                                        </div>
                                    </div>

                                    @if($order->quantity_discount > 0 || $order->coupon_discount > 0)
                                    <div class="alert alert-info mt-3 mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
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
    </div>
</div>

<!-- Hidden Forms for Status Updates -->
<form id="update-status-form" action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-none">
    @csrf
    @method('PUT')
</form>

<form id="update-payment-status-form" action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" class="d-none">
    @csrf
    @method('PUT')
</form>

<!-- Add Friend Modal -->
<div class="modal fade" id="addFriendModal" tabindex="-1" aria-labelledby="addFriendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFriendModalLabel">
                    <i class="fas fa-user-plus me-2"></i>
                    إضافة صديق للطلب
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                                  <form id="addFriendForm">
                      <div class="mb-3">
                          <label for="friend_admin_id" class="form-label">اختر الأدمن *</label>
                          <select class="form-select" id="friend_admin_id" name="friend_admin_id" required>
                              <option value="">اختر الأدمن...</option>
                          </select>
                      </div>
                      <div class="mb-3">
                          <div class="alert alert-info">
                              <i class="fas fa-info-circle me-2"></i>
                              سيتم إرسال رابط الوصول للطلب إلى البريد الإلكتروني الخاص بالأدمن المختار
                          </div>
                      </div>
                  </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="submitAddFriend">
                    <i class="fas fa-plus me-2"></i>
                    إضافة الصديق
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Friends List Modal -->
<div class="modal fade" id="friendsListModal" tabindex="-1" aria-labelledby="friendsListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="friendsListModalLabel">
                    <i class="fas fa-users me-2"></i>
                    قائمة الأصدقاء
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="friendsListContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">جاري التحميل...</span>
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

<!-- Transfer Order Modal -->
<div class="modal fade" id="transferOrderModal" tabindex="-1" aria-labelledby="transferOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferOrderModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>
                    نقل الطلب
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transferOrderForm">
                    <div class="mb-3">
                        <label for="to_admin_id" class="form-label">اختر الأدمن *</label>
                        <select class="form-select" id="to_admin_id" name="to_admin_id" required>
                            <option value="">اختر الأدمن...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transfer_reason" class="form-label">سبب النقل (اختياري)</label>
                        <textarea class="form-control" id="transfer_reason" name="transfer_reason" rows="3" placeholder="اكتب سبب نقل الطلب..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-warning" id="submitTransfer">
                    <i class="fas fa-paper-plane me-2"></i>
                    إرسال طلب النقل
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="/assets/css/admin/orders.css">
@endsection

@section('scripts')
<style>
    /* Fix modal issues */
    .modal {
        z-index: 1050 !important;
    }
    .modal-backdrop {
        z-index: 1040 !important;
    }
    /* Prevent page freeze */
    body.modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
    /* Ensure modal is clickable */
    .modal-dialog {
        pointer-events: auto !important;
        transition: none !important;
    }
    .modal-content {
        pointer-events: auto !important;
    }
    
    /* Custom positioning for modals */
    .modal-dialog.positioned {
        position: fixed !important;
        margin: 0 !important;
        transform: none !important;
        z-index: 1060 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        border-radius: 8px !important;
    }
    
    /* Smooth animation for modal appearance */
    .modal.show .modal-dialog.positioned {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>
<script>
$(document).ready(function() {
    console.log('=== ORDER SHOW PAGE LOADED ===');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Fix modal functionality
    console.log('Modal buttons found:', $('[data-bs-toggle="modal"]').length);
    
    // Remove any existing event handlers
    $('[data-bs-toggle="modal"]').off();
    
        // Add custom modal handling to prevent page freeze
    $('[data-bs-toggle="modal"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const target = $(this).data('bs-target');
        const button = $(this);
        console.log('Opening modal:', target);

        // Manually create and show modal
        const modalElement = document.querySelector(target);
        if (modalElement) {
            // Remove any existing modal instances
            const existingModal = bootstrap.Modal.getInstance(modalElement);
            if (existingModal) {
                existingModal.dispose();
            }

            // Create new modal instance
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            // Show modal
            modal.show();
            
            // Position modal near the button
            setTimeout(function() {
                positionModalNearButton(modalElement, button);
            }, 50);
            
            // Fix backdrop issues
            setTimeout(function() {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $('body').addClass('modal-open');
            }, 100);
        }
    });
    
    // Function to position modal near the clicked button
    function positionModalNearButton(modalElement, button) {
        const buttonOffset = button.offset();
        const buttonHeight = button.outerHeight();
        const buttonWidth = button.outerWidth();
        const modalDialog = $(modalElement).find('.modal-dialog');
        const modalHeight = modalDialog.outerHeight();
        const modalWidth = modalDialog.outerWidth();
        const windowHeight = $(window).height();
        const windowWidth = $(window).width();
        const scrollTop = $(window).scrollTop();
        
        // Get button position relative to viewport
        const buttonTop = buttonOffset.top - scrollTop;
        const buttonLeft = buttonOffset.left;
        
        // Calculate modal position - try to place it right next to the button
        let top = buttonTop + buttonHeight + 5; // 5px below button
        let left = buttonLeft;
        
        // If button is in header (top < 100px), position modal below header
        if (buttonTop < 100) {
            top = 100; // Position below header
            left = Math.max(20, buttonLeft - 100); // Align with button but ensure minimum left
        }
        
        // Adjust if modal would go off screen
        if (top + modalHeight > windowHeight - 20) {
            // Position above button if not enough space below
            top = Math.max(20, buttonTop - modalHeight - 5);
        }
        
        if (left + modalWidth > windowWidth - 20) {
            // Align to right edge if not enough space
            left = windowWidth - modalWidth - 20;
        }
        
        // Ensure minimum positions
        if (top < 20) {
            top = 20;
        }
        if (left < 20) {
            left = 20;
        }
        
        // Apply position with smooth animation
        modalDialog.addClass('positioned').css({
            'top': top + 'px',
            'left': left + 'px',
            'transition': 'all 0.3s ease'
        });
        
        console.log('Modal positioned at:', { 
            top, 
            left, 
            buttonTop, 
            buttonLeft, 
            buttonHeight, 
            modalHeight, 
            modalWidth,
            windowHeight,
            windowWidth
        });
    }
    
    // Add friend functionality
    $('#submitAddFriend').on('click', function() {
        console.log('Add friend button clicked');
        const adminId = $('#friend_admin_id').val();
        
        if (!adminId) {
            showAlert('يرجى اختيار الأدمن', 'error');
            return;
        }
        
        const button = $(this);
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin me-2"></i> جاري الإضافة...');
        
        $.ajax({
            url: '{{ route("admin.orders.friends.add", $order->uuid) }}',
            type: 'POST',
            data: {
                admin_id: adminId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('تم إضافة الصديق بنجاح وتم إرسال رابط الوصول له', 'success');
                    closeModal('#addFriendModal');
                    $('#addFriendForm')[0].reset();
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
            },
            complete: function() {
                button.prop('disabled', false);
                button.html('<i class="fas fa-plus me-2"></i> إضافة الصديق');
            }
        });
    });
    
    $('#submitTransfer').on('click', function() {
        console.log('Transfer button clicked');
        // Transfer logic will be here
    });
    
    // Modal event handlers
    $('#addFriendModal').on('show.bs.modal', function() {
        console.log('Add friend modal opened');
        setTimeout(function() {
            loadAdminsForFriend();
        }, 100);
    });
    
    $('#friendsListModal').on('show.bs.modal', function() {
        console.log('Friends modal opened');
        setTimeout(function() {
            loadFriendsList();
        }, 100);
    });

    $('#transferOrderModal').on('show.bs.modal', function() {
        console.log('Transfer modal opened');
        setTimeout(function() {
            loadAdminsList();
        }, 100);
    });
    
    // Handle modal close buttons
    $('.modal .btn-close, .modal [data-bs-dismiss="modal"]').on('click', function() {
        const modalId = $(this).closest('.modal').attr('id');
        closeModal('#' + modalId);
    });
    
    // Handle modal backdrop click
    $('.modal').on('click', function(e) {
        if (e.target === this) {
            const modalId = $(this).attr('id');
            closeModal('#' + modalId);
        }
    });

    // Assignment buttons
    $('.assign-order-btn').on('click', function() {
        const orderUuid = $(this).data('order-uuid');
        const button = $(this);
        
        $.ajax({
            url: `{{ url('/admin/orders') }}/${orderUuid}/assign`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('تم استلام الطلب بنجاح', 'success');
                    location.reload();
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء استلام الطلب', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء استلام الطلب';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            }
        });
    });

    $('.unassign-order-btn').on('click', function() {
        const orderUuid = $(this).data('order-uuid');
        const button = $(this);
        
        $.ajax({
            url: `{{ url('/admin/orders') }}/${orderUuid}/unassign`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('تم إلغاء استلام الطلب بنجاح', 'success');
                    location.reload();
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء إلغاء استلام الطلب', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء إلغاء استلام الطلب';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            }
        });
    });

    // Add friend functionality
    $('#submitAddFriend').on('click', function() {
        console.log('Add friend button clicked');
        const formData = {
            friend_name: $('#friend_name').val(),
            friend_email: $('#friend_email').val(),
            friend_phone: $('#friend_phone').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        if (!formData.friend_name || !formData.friend_email) {
            showAlert('يرجى ملء جميع الحقول المطلوبة', 'error');
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.orders.friends.add", $order->uuid) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('تم إضافة الصديق بنجاح وتم إرسال رابط الوصول له', 'success');
                    closeModal('#addFriendModal');
                    $('#addFriendForm')[0].reset();
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
    
    // Submit transfer request
    $('#submitTransfer').on('click', function() {
        console.log('Submit transfer button clicked');
        const formData = {
            to_admin_id: $('#to_admin_id').val(),
            reason: $('#transfer_reason').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        if (!formData.to_admin_id) {
            showAlert('يرجى اختيار الأدمن', 'error');
            return;
        }
        
        const button = $(this);
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin me-2"></i> جاري الإرسال...');
        
        $.ajax({
            url: '{{ route("admin.orders.transfer.store", $order->uuid) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('تم إرسال طلب النقل بنجاح', 'success');
                    closeModal('#transferOrderModal');
                    $('#transferOrderForm')[0].reset();
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء إرسال طلب النقل', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء إرسال طلب النقل';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            },
            complete: function() {
                button.prop('disabled', false);
                button.html('<i class="fas fa-paper-plane me-2"></i> إرسال طلب النقل');
            }
        });
    });
    
    // Close modal buttons
    $('.modal .btn-secondary').on('click', function() {
        const modalId = $(this).closest('.modal').attr('id');
        closeModal('#' + modalId);
    });
    
    // Function to properly close modal
    function closeModal(modalId) {
        const modalElement = document.querySelector(modalId);
        if (modalElement) {
            const bootstrapModal = bootstrap.Modal.getInstance(modalElement);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        }
        // Clean up after modal is hidden
        setTimeout(function() {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $('body').css('overflow', 'auto');
            $('body').css('padding-right', '0');
            $('body').css('pointer-events', 'auto');
        }, 300);
    }
    
    // Add event listener for modal hidden event
    $('.modal').on('hidden.bs.modal', function() {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').css('overflow', 'auto');
        $('body').css('padding-right', '0');
        $('body').css('pointer-events', 'auto');
    });

    // Load admins list for transfer
    function loadAdminsList() {
        console.log('Loading admins list...');
        $.ajax({
            url: '{{ route("admin.transfers.admins") }}',
            type: 'GET',
            success: function(response) {
                console.log('Admins loaded:', response);
                if (response.success) {
                    const select = $('#to_admin_id');
                    select.empty();
                    select.append('<option value="">اختر الأدمن...</option>');
                    
                    response.admins.forEach(function(admin) {
                        select.append(`<option value="${admin.id}">${admin.name} (${admin.email})</option>`);
                    });
                    console.log('Admins list populated');
                } else {
                    showAlert('حدث خطأ أثناء تحميل قائمة الأدمن', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error loading admins:', xhr);
                showAlert('حدث خطأ أثناء تحميل قائمة الأدمن', 'error');
            }
        });
    }
    
    // Load admins list for adding friend
    function loadAdminsForFriend() {
        console.log('Loading admins for friend...');
        $.ajax({
            url: '{{ route("admin.transfers.admins") }}',
            type: 'GET',
            success: function(response) {
                console.log('Admins for friend loaded:', response);
                if (response.success) {
                    const select = $('#friend_admin_id');
                    select.empty();
                    select.append('<option value="">اختر الأدمن...</option>');
                    
                    response.admins.forEach(function(admin) {
                        select.append(`<option value="${admin.id}">${admin.name} (${admin.email})</option>`);
                    });
                    console.log('Admins for friend list populated');
                } else {
                    showAlert('حدث خطأ أثناء تحميل قائمة الأدمن', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error loading admins for friend:', xhr);
                showAlert('حدث خطأ أثناء تحميل قائمة الأدمن', 'error');
            }
        });
    }

    // Submit transfer request
    $('#submitTransfer').on('click', function() {
        console.log('Submit transfer button clicked');
        const formData = {
            to_admin_id: $('#to_admin_id').val(),
            reason: $('#transfer_reason').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        if (!formData.to_admin_id) {
            showAlert('يرجى اختيار الأدمن', 'error');
            return;
        }
        
        const button = $(this);
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin me-2"></i> جاري الإرسال...');
        
        $.ajax({
            url: '{{ route("admin.orders.transfer.store", $order->uuid) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('تم إرسال طلب النقل بنجاح', 'success');
                    closeModal('#transferOrderModal');
                    $('#transferOrderForm')[0].reset();
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء إرسال طلب النقل', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء إرسال طلب النقل';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            },
            complete: function() {
                button.prop('disabled', false);
                button.html('<i class="fas fa-paper-plane me-2"></i> إرسال طلب النقل');
            }
        });
    });

    function loadFriendsList() {
        console.log('Loading friends list...');
        $.ajax({
            url: '{{ route("admin.orders.friends.list", $order->uuid) }}',
            type: 'GET',
            success: function(response) {
                console.log('Friends loaded:', response);
                if (response.success) {
                    displayFriendsList(response.friends);
                } else {
                    showAlert(response.message || 'حدث خطأ أثناء تحميل قائمة الأصدقاء', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error loading friends:', xhr);
                showAlert('حدث خطأ أثناء تحميل قائمة الأصدقاء', 'error');
            }
        });
    }

    function displayFriendsList(friends) {
        if (friends.length === 0) {
            $('#friendsListContent').html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <p>لا يوجد أصدقاء مضافة لهذا الطلب</p>
                </div>
            `);
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-hover">';
        html += '<thead class="table-light"><tr>';
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
        $('#friendsListContent').html(html);
    }
    
    // Remove friend functionality
    $(document).on('click', '.remove-friend-btn', function() {
        const friendId = $(this).data('friend-id');
        
        if (confirm('هل أنت متأكد من إزالة هذا الصديق؟')) {
            $.ajax({
                url: '{{ route("admin.orders.friends.remove", [$order->uuid, ""]) }}'.replace('/friends/', '/friends/' + friendId + '/'),
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

    // Remove friend functionality is already implemented above

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
        
        $('.content-wrapper').prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection
    