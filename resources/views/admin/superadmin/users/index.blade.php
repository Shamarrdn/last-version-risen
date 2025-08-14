@extends('layouts.superadmin')

@section('title', 'إدارة المستخدمين')

@section('page_title', 'إدارة المستخدمين')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="activity-section bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="activity-title mb-0">قائمة المستخدمين</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus"></i> إضافة مستخدم جديد
                </button>
            </div>
            
            <!-- Search Form -->
            <div class="p-3 border-bottom bg-light">
                <form id="searchForm" class="row g-3">
                    <div class="col-md-4">
                        <label for="searchQuery" class="form-label">
                            <i class="fas fa-search text-muted"></i> البحث
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchQuery" name="search" 
                                   placeholder="البحث بالاسم، البريد الإلكتروني، أو رقم الهاتف" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="roleFilter" class="form-label">
                            <i class="fas fa-user-tag text-muted"></i> الدور
                        </label>
                        <select class="form-select" id="roleFilter" name="role">
                            <option value="">جميع الأدوار</option>
                            <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>سوبر أدمن</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>أدمن</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>عميل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">
                            <i class="fas fa-toggle-on text-muted"></i> الحالة
                        </label>
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()" title="مسح البحث">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>الدور</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? 'غير محدد' }}</td>
                                <td>
                                    <select class="form-select form-select-sm role-selector" 
                                            data-user-id="{{ $user->id }}"
                                            {{ $user->hasRole('superadmin') ? 'disabled' : '' }}>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" 
                                                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name === 'superadmin' ? 'سوبر أدمن' : 
                                                   ($role->name === 'admin' ? 'أدمن' : 'عميل') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editUser({{ $user->id }})"
                                                title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(!$user->hasRole('superadmin'))
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteUser({{ $user->id }})"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">لا يوجد مستخدمين</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
                                @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    @endif
                    
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة مستخدم جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('superadmin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">الهاتف</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">الدور</label>
                        <select class="form-select" id="role" name="role" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ $role->name === 'superadmin' ? 'سوبر أدمن' : 
                                       ($role->name === 'admin' ? 'أدمن' : 'عميل') }}
                                </option>
                            @endforeach
                        </select>
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
    // تغيير دور المستخدم
    document.querySelectorAll('.role-selector').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const newRole = this.value;
            
            fetch(`/superadmin/users/${userId}/change-role`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ role: newRole })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // إظهار رسالة نجاح
                    Swal.fire({
                        title: 'نجح!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    // إظهار رسالة خطأ
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    });
                    // إعادة تعيين القيمة السابقة
                    this.value = this.getAttribute('data-original-value');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء تغيير الدور',
                    icon: 'error',
                    confirmButtonText: 'حسناً'
                });
            });
        });
    });
    
    // حذف مستخدم
    function deleteUser(userId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف المستخدم نهائياً',
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
                form.action = `/superadmin/users/${userId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    // تعديل مستخدم
    function editUser(userId) {
        window.location.href = `/superadmin/users/${userId}/edit`;
    }
    
    // وظائف البحث
    function performSearch() {
        const searchQuery = document.getElementById('searchQuery').value.toLowerCase();
        const roleFilter = document.getElementById('roleFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        
        const rows = document.querySelectorAll('tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            // تجاهل صف "لا يوجد مستخدمين"
            if (row.querySelector('td[colspan="6"]')) {
                return;
            }
            
            const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const phone = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const roleSelect = row.querySelector('td:nth-child(4) select');
            const role = roleSelect ? roleSelect.value : '';
            
            // التحقق من البحث
            const matchesSearch = !searchQuery || 
                name.includes(searchQuery) || 
                email.includes(searchQuery) || 
                phone.includes(searchQuery);
            
            // التحقق من فلتر الدور
            const matchesRole = !roleFilter || role === roleFilter;
            
            // التحقق من فلتر الحالة (يمكن إضافة منطق الحالة هنا)
            const matchesStatus = !statusFilter || true; // مؤقت للحالة
            
            if (matchesSearch && matchesRole && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // إظهار/إخفاء رسالة "لا توجد نتائج"
        updateNoResultsMessage(visibleCount);
        
        // تحديث عدد النتائج
        updateResultsCount(visibleCount);
    }
    
    function updateNoResultsMessage(visibleCount) {
        let noResultsRow = document.querySelector('tbody tr.no-results');
        
        if (visibleCount === 0) {
            if (!noResultsRow) {
                const tbody = document.querySelector('tbody');
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results';
                noResultsRow.innerHTML = '<td colspan="6" class="text-center text-muted">لا توجد نتائج للبحث</td>';
                tbody.appendChild(noResultsRow);
            }
        } else {
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    }
    
    function updateResultsCount(count) {
        let countElement = document.getElementById('resultsCount');
        if (!countElement) {
            countElement = document.createElement('div');
            countElement.id = 'resultsCount';
            countElement.className = 'alert alert-info mt-3';
            document.querySelector('.table-responsive').after(countElement);
        }
        countElement.innerHTML = `<i class="fas fa-info-circle"></i> تم العثور على <strong>${count}</strong> مستخدم`;
    }
    
    function clearSearch() {
        document.getElementById('searchQuery').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('statusFilter').value = '';
        performSearch();
    }
    
    // إضافة مستمعي الأحداث للبحث
    document.addEventListener('DOMContentLoaded', function() {
        const searchQuery = document.getElementById('searchQuery');
        const roleFilter = document.getElementById('roleFilter');
        const statusFilter = document.getElementById('statusFilter');
        const searchForm = document.getElementById('searchForm');
        
        if (searchQuery) {
            searchQuery.addEventListener('input', performSearch);
        }
        if (roleFilter) {
            roleFilter.addEventListener('change', performSearch);
        }
        if (statusFilter) {
            statusFilter.addEventListener('change', performSearch);
        }
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });
        }
        
        // تهيئة البحث عند تحميل الصفحة
        performSearch();
    });
</script>
@endsection
