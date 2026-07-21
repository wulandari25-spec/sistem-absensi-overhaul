<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@overhaul.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@overhaul.id'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'security1@overhaul.id'],
            [
                'name' => 'Satpam Unit 1',
                'password' => Hash::make('password'),
                'role' => 'security',
                'phone' => '081234567892',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'security2@overhaul.id'],
            [
                'name' => 'Satpam Unit 2',
                'password' => Hash::make('password'),
                'role' => 'security',
                'phone' => '081234567893',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'k3@overhaul.id'],
            [
                'name' => 'Petugas K3',
                'password' => Hash::make('password'),
                'role' => 'k3',
                'phone' => '081234567894',
                'is_active' => true,
            ]
        );
    }
}
