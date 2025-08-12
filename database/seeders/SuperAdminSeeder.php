<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        try {
            // إنشاء دور السوبر أدمن إذا لم يكن موجوداً
            $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
            
            // إنشاء الصلاحيات إذا لم تكن موجودة
            $permissions = [
                'manage products',
                'manage orders',
                'manage appointments',
                'manage reports',
                'view admin sales reports'
            ];
            
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
            
            // إعطاء جميع الصلاحيات للسوبر أدمن
            $superadmin->syncPermissions(Permission::all());
            
            // إنشاء مستخدم سوبر أدمن إذا لم يكن موجوداً
            $superadminUser = User::firstOrCreate(
                ['email' => 'superadmin@admin.com'],
                [
                    'name' => 'Super Admin',
                    'password' => bcrypt('password'),
                    'role' => 'superadmin',
                    'phone' => '01234567889',
                    'address' => 'Cairo, Egypt'
                ]
            );
            
            // إعطاء دور السوبر أدمن للمستخدم
            $superadminUser->assignRole('superadmin');
            
            $this->command->info('تم إنشاء حساب السوبر أدمن بنجاح!');
            $this->command->info('البريد الإلكتروني: superadmin@admin.com');
            $this->command->info('كلمة المرور: password');
            
        } catch (\Exception $e) {
            $this->command->error('حدث خطأ أثناء إنشاء حساب السوبر أدمن: ' . $e->getMessage());
        }
    }
}





