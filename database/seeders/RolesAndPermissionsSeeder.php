<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {

        // php artisan db:seed --class=RolesAndPermissionsSeeder

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Standard CRUD Modules
        $modules = [
            'users',
            'customers',
            'invoices',
            'products',
            'reports',
            'payments',
            'branches',
            'integrations',
            'settings',
            'permissions',
            'dashboard',
            'roles',
        ];

        foreach ($modules as $module) {
            Permission::firstOrCreate(['name' => "create $module"]);
            Permission::firstOrCreate(['name' => "view $module"]);
            Permission::firstOrCreate(['name' => "update $module"]);
            Permission::firstOrCreate(['name' => "delete $module"]);
        }

        // 2. Specific Granular Task Permissions
        $granularPermissions = [
            'view financials',   // Controls visibility of prices, totals, and balances
            'record payments',   // Allows recording M-Pesa/Cash payments
            'manage deliveries', // Allows seeing delivery locations and marking as delivered
            'pack orders',       // Allows marking orders as packed
        ];

        foreach ($granularPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create roles
        $admin = Role::firstOrCreate(['name' => 'super-admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $staff = Role::firstOrCreate(['name' => 'staff']);

        // New Workflow Roles
        $packer = Role::firstOrCreate(['name' => 'packer']);
        $delivery = Role::firstOrCreate(['name' => 'delivery']);

        // 4. Assign Permissions to Roles

        // Admin has absolutely everything
        $admin->syncPermissions(Permission::all());

        // Manager has all permissions except specified admin ones
        $managerPermissions = Permission::whereNotIn('name', [
            'create branches',
            'view branches',
            'update branches',
            'delete branches',
        ])->get();
        $manager->syncPermissions($managerPermissions);

        // Staff (front desk): customers, invoices/quotations and payments, but no deletes,
        // settings, users, roles, reports or expenses.
        $staff->syncPermissions([
            'view customers', 'create customers', 'update customers',
            'view invoices', 'create invoices', 'update invoices',
            'view products', 'view payments', 'create payments',
            'record payments', 'view financials', 'view dashboard',
        ]);

        // Customer: placeholder role with no admin access
        $customer->syncPermissions([]);

        // Packer Role: Can view invoices and update them to "packed", but no financials!
        $packer->syncPermissions([
            'view invoices',
            // 'update invoices',
            'pack orders'
        ]);

        // Delivery Role: Can view invoices and manage deliveries, but no financials!
        $delivery->syncPermissions([
            'view invoices',
            'manage deliveries'
        ]);

        // 5. Assign super-admin to user ID 1
        $user = \App\Models\User::find(1);
        if ($user) {
            $user->assignRole('super-admin');
        }
    }
}
