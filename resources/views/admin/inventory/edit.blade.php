@extends($adminLayout)

@section('title', 'تعديل المخزون')
@section('page_title', 'تعديل المخزون')

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
                                            <i class="fas fa-edit text-primary me-2"></i>
                                            تعديل المخزون
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للمخزون
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <h5 class="alert-heading mb-2">يوجد أخطاء في النموذج:</h5>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                معلومات المخزون
                                            </h5>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">المنتج</label>
                                                <input type="text" class="form-control" value="{{ $inventory->product->name ?? 'غير محدد' }}" disabled>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">المقاس</label>
                                                <input type="text" class="form-control" value="{{ $inventory->size->name ?? 'بدون مقاس' }}" disabled>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">اللون</label>
                                                <div class="d-flex align-items-center">
                                                    @if($inventory->color)
                                                    <span class="color-circle me-2" style="background-color: {{ $inventory->color->code ?? '#ccc' }}"></span>
                                                    @endif
                                                    <input type="text" class="form-control" value="{{ $inventory->color->name ?? 'بدون لون' }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-boxes text-primary me-2"></i>
                                                تفاصيل المخزون
                                            </h5>
                                            
                                            <div class="mb-3">
                                                <label for="stock" class="form-label required">الكمية المتاحة</label>
                                                <input type="number" id="stock" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $inventory->stock) }}" min="0" required>
                                                @error('stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="consumed_stock" class="form-label">الكمية المستهلكة</label>
                                                <input type="number" id="consumed_stock" class="form-control" value="{{ $inventory->consumed_stock }}" disabled>
                                                <small class="text-muted">هذه الكمية يتم تحديثها تلقائياً عند إتمام الطلبات</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="price" class="form-label">السعر (اختياري)</label>
                                                <div class="input-group">
                                                    <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $inventory->price) }}" min="0" step="0.01">
                                                    <span class="input-group-text">ر.س</span>
                                                </div>
                                                @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">اترك هذا الحقل فارغاً لاستخدام السعر الأساسي للمنتج</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', $inventory->is_available) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_available">متاح للبيع</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                حفظ التغييرات
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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