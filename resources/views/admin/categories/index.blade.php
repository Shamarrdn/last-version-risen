@extends($adminLayout)

@section('title', 'التصنيفات')
@section('page_title', 'إدارة التصنيفات')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="categories-container">
                        <!-- Stats Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-primary me-3">
                                                <i class="fas fa-layer-group fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي التصنيفات</h6>
                                                <h3 class="text-white mb-0">{{ $categories->total() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-success h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-success me-3">
                                                <i class="fas fa-box-open fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي المنتجات</h6>
                                                <h3 class="text-white mb-0">{{ $categories->sum('products_count') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-info me-3">
                                                <i class="fas fa-clock fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">آخر تحديث</h6>
                                                <h3 class="text-white mb-0">{{ $categories->first()?->updated_at->format('Y/m/d') ?? 'لا يوجد' }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <span class="icon-circle bg-primary text-white me-2">
                                                    <i class="fas fa-tags"></i>
                                                </span>
                                                إدارة التصنيفات
                                            </h5>
                                            <p class="text-muted mb-0 fs-sm">إدارة وتنظيم تصنيفات المنتجات</p>
                                        </div>
                                        <div class="actions">
                                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-wave">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة تصنيف جديد
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <form action="{{ route('admin.categories.index') }}" method="GET">
                                            <div class="row g-3">
                                                <div class="col-md-10">
                                                    <div class="search-wrapper">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-0">
                                                                <i class="fas fa-search text-muted"></i>
                                                            </span>
                                                            <input type="text" name="search" class="form-control border-0 shadow-none ps-0"
                                                                placeholder="البحث في التصنيفات..."
                                                                value="{{ request('search') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary btn-wave w-100">
                                                        <i class="fas fa-search me-2"></i>
                                                        بحث
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Grid -->
                        <div class="row g-4">
                            <table class="table table-centered table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">#</th>
                                        <th class="border-top-0">الصورة</th>
                                        <th class="border-top-0">اسم التصنيف</th>
                                        <th class="border-top-0">عدد المنتجات</th>
                                        <th class="border-top-0">تاريخ الإنشاء</th>
                                        <th class="border-top-0">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <span class="badge bg-success rounded-pill">{{ $category->products_count }}</span>
                                            </td>
                                            <td>{{ $category->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.categories.show', $category) }}"
                                                       class="btn btn-light-info btn-sm me-2"
                                                       title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                                       class="btn btn-light-primary btn-sm me-2"
                                                       title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.categories.destroy', $category) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-light-danger btn-sm"
                                                                title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد تصنيفات</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($categories->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $categories->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/category.css') }}">
@endsection
