<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view_users', 'create_users', 'edit_users', 'delete_users',
            // Meal permissions
            'view_meals', 'create_meals', 'edit_meals', 'delete_meals',
            // Order permissions
            'view_orders', 'create_orders', 'edit_orders', 'delete_orders',
            // Canteen permissions
            'view_canteens', 'create_canteens', 'edit_canteens', 'delete_canteens',
            // Vendor permissions
            'view_vendor_dashboard', 'manage_vendor_meals', 'manage_vendor_orders',
            // Student permissions
            'place_orders', 'view_order_history', 'give_feedback',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $vendorRole = Role::create(['name' => 'vendor']);
        $vendorRole->givePermissionTo([
            'view_vendor_dashboard',
            'manage_vendor_meals',
            'manage_vendor_orders',
            'view_meals',
            'create_meals',
            'edit_meals',
            'delete_meals',
            'view_orders',
            'edit_orders',
        ]);

        $studentRole = Role::create(['name' => 'student']);
        $studentRole->givePermissionTo([
            'place_orders',
            'view_order_history',
            'give_feedback',
            'view_meals',
            'view_orders',
        ]);

        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'place_orders',
            'view_order_history',
            'give_feedback',
            'view_meals',
            'view_orders',
        ]);

        // Create default admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@smartcanteen.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // Create vendor user
        $vendor = User::create([
            'name' => 'Vendor User',
            'email' => 'vendor@smartcanteen.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
        ]);
        $vendor->assignRole('vendor');

        // Create student user
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@smartcanteen.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'matric_number' => 'ABC12345',
            'faculty' => 'Computer Science',
        ]);
        $student->assignRole('student');

        // Create staff user
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@smartcanteen.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);
        $staff->assignRole('staff');
    }
}
