<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access to all features'
        ]);

        $uploaderRole = Role::create([
            'name' => 'uploader',
            'description' => 'Can upload and manage their own assets'
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $admin->roles()->attach($adminRole);

        // Create uploader user
        $uploader = User::create([
            'name' => 'Uploader User',
            'email' => 'uploader@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $uploader->roles()->attach($uploaderRole);
    }
} 