<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear Spatie's permission cache.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions used by the application.
        $permissions = [
            // Dashboard
            'view dashboard',

            // Categories
            'view categories',
            'create categories',
            'update categories',
            'delete categories',

            // Products
            'view products',
            'create products',
            'update products',
            'delete products',

            // Daily menus
            'view menus',
            'create menus',
            'update menus',
            'delete menus',

            // Pickup settings and slots
            'view pickup settings',
            'manage pickup settings',
            'view pickup slots',
            'manage pickup slots',

            // Orders
            'view orders',
            'manage orders',
            'update order status',

            // Users
            'view users',
            'create users',
            'update users',
            'delete users',
            'assign user roles',

            // Role and permission management
            'view roles',
            'manage roles',
        ];

        // 2. Create permissions if they do not already exist.
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // 3. Create the initial roles.
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web',
        ]);

        $customer = Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web',
        ]);

        // 4. Give admin every permission.
        $admin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // 5. Give staff only the permissions they need.
        $staff->syncPermissions([
            'view dashboard',
            'view categories',
            'view products',
            'view menus',
            'view pickup slots',
            'view orders',
            'manage orders',
            'update order status',
        ]);

        // 6. Customers do not need admin permissions.
        $customer->syncPermissions([]);

        // Clear cache again after changing permissions.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
