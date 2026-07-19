<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // guard_name must match config/auth.php ('web' by default)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        // Locally, fall back to a known convenience login so you can seed
        // and log in immediately without any setup. Outside local/testing,
        // ADMIN_EMAIL and ADMIN_PASSWORD must be explicitly set in the
        // environment — this fails loudly instead of silently creating a
        // guessable admin@pharmacera.test / password account on real data.
        if (app()->environment(['local', 'testing'])) {
            $email ??= 'admin@pharmacera.test';
            $password ??= 'password';
        } elseif (! $email || ! $password) {
            throw new RuntimeException(
                'RoleSeeder: ADMIN_EMAIL and ADMIN_PASSWORD must be set in the environment '
                .'before seeding outside local/testing. Refusing to create a default admin account.'
            );
        }

        $adminUser = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        if (! $adminUser->hasRole('admin')) {
            $adminUser->assignRole($admin);
        }
    }
}