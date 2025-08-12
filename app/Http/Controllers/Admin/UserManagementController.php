<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    public function index()
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $users = User::with('roles')->paginate(15);
        $roles = Role::all();
        
        return view('admin.superadmin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
        ]);

        // تعيين دور المستخدم
        $user->assignRole($request->role);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function edit($id)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $user = User::findOrFail($id);
        $roles = Role::all();
        
        return view('admin.superadmin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ]);

        // تحديث دور المستخدم
        $user->syncRoles([$request->role]);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy($id)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $user = User::findOrFail($id);
        
        // لا يمكن حذف السوبر أدمن
        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'لا يمكن حذف السوبر أدمن');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function changeRole(Request $request, $id)
    {
        // تحقق من أن المستخدم هو سوبر أدمن
        if (!auth()->user()->hasRole('superadmin')) {
            return redirect()->route('dashboard');
        }

        $user = User::findOrFail($id);
        $newRole = $request->role;

        // لا يمكن تغيير دور السوبر أدمن
        if ($user->hasRole('superadmin') && $newRole !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تغيير دور السوبر أدمن'
            ]);
        }

        $user->syncRoles([$newRole]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير دور المستخدم بنجاح'
        ]);
    }
}
