<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // guard_name must match config/auth.php ('web' by default)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);

        // A default admin account so you can log in immediately.
        // Change the password / delete this before deploying anywhere real.
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@pharmacera.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (! $adminUser->hasRole('admin')) {
            $adminUser->assignRole($admin);
        }
    }
}
