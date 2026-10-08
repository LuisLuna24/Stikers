<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $name = (string) config('admin.name');
        $email = (string) config('admin.email');
        $password = (string) config('admin.password');

        if (blank($password) && app()->isProduction()) {
            throw new RuntimeException('ADMIN_PASSWORD must be configured in production.');
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        $admin->name = $name;
        $admin->status = 'active';
        $admin->email_verified_at ??= now();

        if (! $admin->exists) {
            $admin->password = $password ?: 'password';
        }

        $admin->save();
        $admin->assignRole('admin');
    }
}
