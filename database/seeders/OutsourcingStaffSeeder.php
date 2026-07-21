<?php

namespace Database\Seeders;

use App\Models\OutsourcingStaff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OutsourcingStaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffData = [
            ['staff_code' => 'OS-0001', 'name' => 'Ahmad Fauzi', 'institution' => 'PT. Rekayasa Industri', 'department' => 'Mekanik Turbin', 'position' => 'Teknisi Senior', 'is_active_onsite' => true, 'id_number' => '3201010101010001', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0002', 'name' => 'Budi Santoso', 'institution' => 'PT. Rekayasa Industri', 'department' => 'Mekanik Turbin', 'position' => 'Teknisi', 'is_active_onsite' => true, 'id_number' => '3201010101010002', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0003', 'name' => 'Candra Wijaya', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'Elektrikal', 'position' => 'Electrician', 'is_active_onsite' => false, 'id_number' => '3201010101010003', 'password' => Hash::make('password'), 'last_seen_at' => now()->subHours(3)],
            ['staff_code' => 'OS-0004', 'name' => 'Dewi Ratnasari', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'Instrumen', 'position' => 'Instrument Tech', 'is_active_onsite' => true, 'id_number' => '3201010101010004', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0005', 'name' => 'Eko Prasetyo', 'institution' => 'CV. Karya Teknik', 'department' => 'Scaffolding', 'position' => 'Scaffolder', 'is_active_onsite' => true, 'id_number' => '3201010101010005', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0006', 'name' => 'Fitri Handayani', 'institution' => 'CV. Karya Teknik', 'department' => 'HSE', 'position' => 'Safety Officer', 'is_active_onsite' => true, 'id_number' => '3201010101010006', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0007', 'name' => 'Gunawan Hidayat', 'institution' => 'PT. Nusa Power Services', 'department' => 'Boiler', 'position' => 'Welder', 'is_active_onsite' => false, 'id_number' => '3201010101010007', 'password' => Hash::make('password'), 'last_seen_at' => now()->subHours(1)],
            ['staff_code' => 'OS-0008', 'name' => 'Hendra Kusuma', 'institution' => 'PT. Nusa Power Services', 'department' => 'Boiler', 'position' => 'Pipe Fitter', 'is_active_onsite' => true, 'id_number' => '3201010101010008', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0009', 'name' => 'Irfan Maulana', 'institution' => 'PT. Rekayasa Industri', 'department' => 'Rotating Equipment', 'position' => 'Machinist', 'is_active_onsite' => true, 'id_number' => '3201010101010009', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0010', 'name' => 'Joko Widodo', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'Civil', 'position' => 'Foreman', 'is_active_onsite' => false, 'id_number' => '3201010101010010', 'password' => Hash::make('password'), 'last_seen_at' => now()->subMinutes(30)],
        ];

        foreach ($staffData as $data) {
            OutsourcingStaff::create($data);
        }
    }
}
