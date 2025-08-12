<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagementController extends Controller
{
    public function index()
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $roles = Role::with('permissions')->withCount('users')->get();
        $permissions = Permission::all();
        
        return view('admin.superadmin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function edit($id)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        
        return view('admin.superadmin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $role = Role::findOrFail($id);
        
        // لا يمكن تعديل دور السوبر أدمن
        if ($role->name === 'superadmin') {
            return redirect()->route('superadmin.roles.index')
                ->with('error', 'لا يمكن تعديل دور السوبر أدمن');
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy($id)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $role = Role::findOrFail($id);
        
        // لا يمكن حذف دور السوبر أدمن
        if ($role->name === 'superadmin') {
            return redirect()->route('superadmin.roles.index')
                ->with('error', 'لا يمكن حذف دور السوبر أدمن');
        }

        $role->delete();

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }
}
