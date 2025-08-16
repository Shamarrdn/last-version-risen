@extends($adminLayout)

@section('title', 'عرض المخزون')
@section('page_title', 'عرض المخزون')

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
                                            <i class="fas fa-box text-primary me-2"></i>
                                            تفاصيل المخزون
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.inventory.edit', $inventory->id) }}" class="btn btn-primary me-2">
                                                <i class="fas fa-edit me-1"></i>
                                                تعديل
                                            </a>
                                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للمخزون
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Product Information -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            معلومات المنتج
                                        </h5>
                                        
                                        @if($inventory->product)
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="{{ $inventory->product->image_url }}" alt="{{ $inventory->product->name }}" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
<div>
                                                <h5 class="mb-1">{{ $inventory->product->name }}</h5>
                                                <p class="text-muted mb-0">{{ $inventory->product->category->name ?? 'بدون تصنيف' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">الوصف:</h6>
                                            <p>{{ $inventory->product->description ?? 'لا يوجد وصف' }}</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6 class="fw-bold">السعر الأساسي:</h6>
                                            <p>{{ $inventory->product->base_price ? number_format($inventory->product->base_price, 2) . ' ر.س' : 'غير محدد' }}</p>
                                        </div>
                                        @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            المنتج غير موجود أو تم حذفه
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inventory Details -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-boxes text-primary me-2"></i>
                                            تفاصيل المخزون
                                        </h5>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">المقاس</h6>
                                                        <p class="card-text fw-bold">
                                                            @if($inventory->size)
                                                                <span class="badge bg-info">{{ $inventory->size->name }}</span>
                                                            @else
                                                                <span class="text-muted">بدون مقاس</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">اللون</h6>
                                                        <p class="card-text fw-bold">
                                                            @if($inventory->color)
                                                                <div class="d-flex align-items-center">
                                                                    <span class="color-circle me-2" style="background-color: {{ $inventory->color->code ?? '#ccc' }}"></span>
                                                                    <span>{{ $inventory->color->name }}</span>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">بدون لون</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">المخزون الكلي</h6>
                                                        <p class="card-text fw-bold fs-4">{{ $inventory->stock }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">المخزون المستهلك</h6>
                                                        <p class="card-text fw-bold fs-4">{{ $inventory->consumed_stock }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">المخزون المتاح</h6>
                                                        <p class="card-text fw-bold fs-4">
                                                            <span class="badge {{ $inventory->available_stock > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                                                {{ $inventory->available_stock }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">السعر</h6>
                                                        <p class="card-text fw-bold fs-4">{{ $inventory->price ? number_format($inventory->price, 2) . ' ر.س' : '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-subtitle mb-2 text-muted">الحالة</h6>
                                                        <p class="card-text">
                                                            @if($inventory->is_available)
                                                                <span class="badge bg-success">متاح</span>
                                                            @else
                                                                <span class="badge bg-danger">غير متاح</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete Button -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash me-2"></i>
                                            حذف المخزون
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        هل أنت متأكد من حذف هذا المخزون؟
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('admin.inventory.destroy', $inventory->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">حذف</button>
                                        </form>
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
</style>
@endsection