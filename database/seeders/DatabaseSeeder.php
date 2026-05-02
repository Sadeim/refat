<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedRolesAndPermissions();
        $this->seedAdminUser();
    }

    private function seedRolesAndPermissions(): void
    {
        $modules = [
            'users', 'roles',
            'employees', 'customers',
            'incoming_letters', 'outgoing_letters',
            'custodies',
            'salaries', 'transactions',
            'reports',
        ];

        $actions = ['view_any', 'view', 'create', 'update', 'delete', 'export'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$module}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $hr = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $archive = Role::firstOrCreate(['name' => 'archive', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        $superAdmin->syncPermissions(Permission::all());
        $admin->syncPermissions(Permission::all());

        $accountant->syncPermissions(Permission::whereIn('name', [
            'view_any_salaries', 'view_salaries', 'create_salaries', 'update_salaries', 'delete_salaries', 'export_salaries',
            'view_any_transactions', 'view_transactions', 'create_transactions', 'update_transactions', 'delete_transactions', 'export_transactions',
            'view_any_employees', 'view_employees',
            'view_any_customers', 'view_customers',
            'view_any_reports', 'export_reports',
        ])->get());

        $hr->syncPermissions(Permission::whereIn('name', [
            'view_any_employees', 'view_employees', 'create_employees', 'update_employees', 'delete_employees', 'export_employees',
            'view_any_custodies', 'view_custodies', 'create_custodies', 'update_custodies',
            'view_any_reports', 'export_reports',
        ])->get());

        $archive->syncPermissions(Permission::whereIn('name', [
            'view_any_incoming_letters', 'view_incoming_letters', 'create_incoming_letters', 'update_incoming_letters', 'delete_incoming_letters', 'export_incoming_letters',
            'view_any_outgoing_letters', 'view_outgoing_letters', 'create_outgoing_letters', 'update_outgoing_letters', 'delete_outgoing_letters', 'export_outgoing_letters',
        ])->get());

        $viewer->syncPermissions(Permission::where('name', 'like', 'view_%')->get());
    }

    private function seedAdminUser(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@trustguard.local'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }
    }
}
