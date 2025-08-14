<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixUserRolesSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الأدوار إذا لم تكن موجودة
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // إنشاء الصلاحيات إذا لم تكن موجودة
        $permissions = [
            'manage products',
            'manage orders',
            'manage appointments',
            'manage reports',
            'view admin sales reports'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // تعيين جميع الصلاحيات للسوبر أدمن
        $superadminRole->syncPermissions($permissions);

        // تعيين صلاحيات محدودة للأدمن
        $adminRole->syncPermissions([
            'manage products',
            'manage orders',
            'manage appointments',
            'manage reports'
        ]);

        // تعيين صلاحيات محدودة للعميل
        $customerRole->syncPermissions([]);

        // إصلاح أدوار المستخدمين بناءً على حقل role
        $users = User::all();
        
        foreach ($users as $user) {
            // إزالة جميع الأدوار الحالية
            $user->syncRoles([]);
            
            // تعيين الدور الصحيح بناءً على حقل role
            if ($user->role === 'superadmin') {
                $user->assignRole('superadmin');
                echo "تم تعيين دور superadmin للمستخدم: {$user->name}\n";
            } elseif ($user->role === 'admin') {
                $user->assignRole('admin');
                echo "تم تعيين دور admin للمستخدم: {$user->name}\n";
            } else {
                $user->assignRole('customer');
                echo "تم تعيين دور customer للمستخدم: {$user->name}\n";
            }
        }

        echo "\nتم إصلاح جميع أدوار المستخدمين بنجاح!\n";
    }
}







