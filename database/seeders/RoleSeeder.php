<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = ['admin', 'vendor', 'staff', 'student'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Create permissions (optional - for more granular control)
        $permissions = [
            'manage users',
            'manage canteens',
            'manage orders',
            'view orders',
            'place orders',
            'manage menu',
            'view menu',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles (optional)
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo($permissions);

        $vendorRole = Role::findByName('vendor');
        $vendorRole->givePermissionTo(['manage menu', 'manage orders', 'view orders']);

        $staffRole = Role::findByName('staff');
        $staffRole->givePermissionTo(['manage users', 'manage canteens', 'view orders']);

        $studentRole = Role::findByName('student');
        $studentRole->givePermissionTo(['place orders', 'view menu', 'view orders']);
    }
}
