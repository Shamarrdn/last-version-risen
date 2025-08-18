@extends('layouts.superadmin')

@section('title', 'تعديل الدور')

@section('page_title', 'تعديل الدور')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="activity-section bg-white rounded-3 shadow-sm">
            <div class="activity-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="activity-title mb-0">تعديل الدور: {{ $role->name }}</h5>
                <a href="{{ route('superadmin.roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
            
            <div class="p-4">
                <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الدور</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $role->name) }}" required
                               {{ $role->name === 'superadmin' ? 'readonly' : '' }}>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($role->name === 'superadmin')
                            <div class="form-text text-muted">لا يمكن تعديل اسم دور السوبر أدمن</div>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">الصلاحيات</label>
                        <div class="row">
                            @foreach($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="permissions[]" value="{{ $permission->name }}" 
                                               id="perm_{{ $permission->id }}"
                                               {{ in_array($permission->name, $role->permissions->pluck('name')->toArray()) ? 'checked' : '' }}
                                               {{ $role->name === 'superadmin' ? 'disabled' : '' }}>
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
                        @if($role->name === 'superadmin')
                            <div class="form-text text-muted">لا يمكن تعديل صلاحيات دور السوبر أدمن</div>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('superadmin.roles.index') }}" class="btn btn-secondary">إلغاء</a>
                        @if($role->name !== 'superadmin')
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection











