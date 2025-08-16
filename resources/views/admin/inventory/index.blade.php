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

                        <!-- Inventory Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
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
                                                <td>{{ $item->stock }}</td>
                                                <td>{{ $item->consumed_stock }}</td>
                                                <td>
                                                    <span class="badge {{ $item->available_stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $item->available_stock }}
                                                    </span>
                                                </td>
                                                <td>{{ $item->price ? number_format($item->price, 2) . ' ر.س' : '-' }}</td>
                                                <td>
                                                    @if($item->is_available)
                                                        <span class="badge bg-success">متاح</span>
                                                    @else
                                                        <span class="badge bg-danger">غير متاح</span>
                                                    @endif
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
</style>
@endsection