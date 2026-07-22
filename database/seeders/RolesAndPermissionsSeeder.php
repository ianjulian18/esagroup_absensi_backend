<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Default Roles
        $roles = [
            'Super Admin',
            'Admin Entitas',
            'Principal',
            'HR',
            'Staff',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Migrate existing users from 'role' string to Spatie role
        // For example, anyone with role = 'admin' gets 'Super Admin'
        // anyone with role = 'karyawan' gets 'Staff'
        
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'admin') {
                $user->assignRole('Super Admin');
            } elseif ($user->role === 'karyawan') {
                $user->assignRole('Staff');
            }
        }
        
        echo "Roles have been seeded and existing users migrated.\n";
    }
}
