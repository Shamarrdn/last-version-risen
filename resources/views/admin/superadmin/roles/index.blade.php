@extends('layouts.superadmin')

@section('title', 'إدارة الأدوار والصلاحيات')

@section('page_title', 'إدارة الأدوار والصلاحيات')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="activity-section bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="activity-title mb-0">قائمة الأدوار</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                    <i class="fas fa-plus"></i> إضافة دور جديد
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>اسم الدور</th>
                            <th>الصلاحيات</th>
                            <th>عدد المستخدمين</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $role->name === 'superadmin' ? 'danger' : ($role->name === 'admin' ? 'primary' : 'secondary') }}">
                                        {{ $role->name === 'superadmin' ? 'سوبر أدمن' : 
                                           ($role->name === 'admin' ? 'أدمن' : 'عميل') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($role->permissions as $permission)
                                            <span class="badge bg-info">{{ $permission->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ $role->users_count ?? 0 }}</td>
                                <td>{{ $role->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($role->name !== 'superadmin')
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="editRole({{ $role->id }})"
                                                    title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteRole({{ $role->id }})"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">لا يمكن تعديل</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">لا يوجد أدوار</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة دور جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('superadmin.roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الدور</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الصلاحيات</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="permissions[]" value="{{ $permission->name }}" 
                                               id="perm_{{ $permission->id }}">
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->name === 'manage products' ? 'إدارة المنتجات' :
                                               ($permission->name === 'manage orders' ? 'إدارة الطلبات' :
                                               ($permission->name === 'manage appointments' ? 'إدارة المواعيد' :
                                               ($permission->name === 'manage reports' ? 'إدارة التقارير' :
                                               ($permission->name === 'view admin sales reports' ? 'عرض تقارير مبيعات المشرفين' : $permission->name)))) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // حذف دور
    function deleteRole(roleId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف الدور نهائياً',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إنشاء نموذج حذف
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/superadmin/roles/${roleId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // تعديل دور
    function editRole(roleId) {
        window.location.href = `/superadmin/roles/${roleId}/edit`;
    }
</script>
@endsection
