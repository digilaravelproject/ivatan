<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = ['admin', 'moderator', 'user'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Super Admin',
                'password' => bcrypt('12345678'), // change later

            ]
        );

        $admin->assignRole('admin');
    }
}
