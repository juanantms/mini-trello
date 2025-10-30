<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role if not exists
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Create 'view-logs' permission if not exists
        $permission = Permission::firstOrCreate(['name' => 'view-logs']);

        // Assign permission to admin role
        if (!$role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@minitrello.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('test'),
            ]
        );

        // Assign admin role
        $admin->assignRole($role);
    }
}
