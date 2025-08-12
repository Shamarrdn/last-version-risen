<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // إنشاء الأدوار
        $superadmin = Role::create(['name' => 'superadmin']);
        $admin = Role::create(['name' => 'admin']);
        $customer = Role::create(['name' => 'customer']);


        // إدارة المنتجات والفئات
        Permission::create(['name' => 'manage products']);

        // إدارة الطلبات
        Permission::create(['name' => 'manage orders']);

        // إدارة المواعيد
        Permission::create(['name' => 'manage appointments']);

        // إدارة التقارير
        Permission::create(['name' => 'manage reports']);

        // صلاحية للسوبر أدمن - مشاهدة تقارير مبيعات الأعضاء
        Permission::create(['name' => 'view admin sales reports']);
        
        // إعطاء جميع الصلاحيات للسوبر أدمن
        $superadmin->givePermissionTo(Permission::all());
        
        // إعطاء الصلاحيات للمدير (ما عدا مشاهدة تقارير مبيعات الأعضاء)
        $admin->givePermissionTo([
            'manage products',
            'manage orders', 
            'manage appointments',
            'manage reports'
        ]);

        // إنشاء مستخدم مدير
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '01234567890',
            'address' => 'Cairo, Egypt'
        ]);

        // إعطاء دور المدير للمستخدم
        $adminUser->assignRole('admin');

        // إنشاء مستخدم عادي
        $customerUser = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '01234567891',
            'address' => 'Alexandria, Egypt'
        ]);

        // إعطاء دور العميل للمستخدم
        $customerUser->assignRole('customer');
        
        // إنشاء مستخدم سوبر أدمن
        $superadminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'phone' => '01234567889',
            'address' => 'Cairo, Egypt'
        ]);

        // إعطاء دور السوبر أدمن للمستخدم
        $superadminUser->assignRole('superadmin');
    }
}
