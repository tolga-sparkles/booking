<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create manager user  
        User::create([
            'name' => 'Manager',
            'email' => 'manager@admin.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);

        // Create expert user
        User::create([
            'name' => 'Expert',
            'email' => 'expert@admin.com',
            'password' => Hash::make('expert123'),
            'role' => 'expert',
            'email_verified_at' => now(),
        ]);
    }
}
