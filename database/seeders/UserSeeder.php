<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@overhaul.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@overhaul.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Satpam Unit 1',
            'email' => 'security1@overhaul.id',
            'password' => Hash::make('password'),
            'role' => 'security',
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Satpam Unit 2',
            'email' => 'security2@overhaul.id',
            'password' => Hash::make('password'),
            'role' => 'security',
            'phone' => '081234567893',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Petugas K3',
            'email' => 'k3@overhaul.id',
            'password' => Hash::make('password'),
            'role' => 'k3',
            'phone' => '081234567894',
            'is_active' => true,
        ]);
    }
}
