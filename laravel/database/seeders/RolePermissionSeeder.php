<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            'menus.view',
            'menus.create',
            'menus.update',
            'menus.delete',

            'pickup-settings.view',
            'pickup-settings.update',

            'pickup-slots.view',
            'pickup-slots.create',
            'pickup-slots.update',

            'orders.view',
            'orders.create',
            'orders.update',
            'orders.cancel',

            'payments.view',
            'payments.create',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $admin = Role::findOrCreate('admin');
        $staff = Role::findOrCreate('staff');
        $customer = Role::findOrCreate('customer');

        $admin->givePermissionTo(
            Permission::all()
        );

        $staff->givePermissionTo([
            'categories.view',
            'products.view',

            'menus.view',
            'menus.create',
            'menus.update',

            'pickup-settings.view',

            'pickup-slots.view',
            'pickup-slots.create',
            'pickup-slots.update',

            'orders.view',
            'orders.update',

            'payments.view',
        ]);

        $customer->givePermissionTo([
            'menus.view',
            'orders.create',
            'orders.view',
            'orders.cancel',
            'payments.create',
        ]);
    }
}