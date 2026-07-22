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
            ['staff_code' => 'OS-0011', 'name' => 'Kurniawan Dwi', 'institution' => 'PT. Rekayasa Industri', 'department' => 'Mekanik Turbin', 'position' => 'Teknisi', 'is_active_onsite' => false, 'id_number' => '3201010101010011', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0012', 'name' => 'Lestari Indah', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'Instrumen', 'position' => 'Instrument Tech', 'is_active_onsite' => false, 'id_number' => '3201010101010012', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0013', 'name' => 'Muhammad Rizky', 'institution' => 'CV. Karya Teknik', 'department' => 'Scaffolding', 'position' => 'Scaffolder', 'is_active_onsite' => false, 'id_number' => '3201010101010013', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0014', 'name' => 'Novita Sari', 'institution' => 'PT. Nusa Power Services', 'department' => 'HSE', 'position' => 'Safety Officer', 'is_active_onsite' => false, 'id_number' => '3201010101010014', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0015', 'name' => 'Oki Setiawan', 'institution' => 'CV. Karya Teknik', 'department' => 'Civil', 'position' => 'Worker', 'is_active_onsite' => false, 'id_number' => '3201010101010015', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0016', 'name' => 'Putri Ayu', 'institution' => 'PT. Rekayasa Industri', 'department' => 'Administrasi', 'position' => 'Clerk', 'is_active_onsite' => false, 'id_number' => '3201010101010016', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0017', 'name' => 'Qori Ananda', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'Elektrikal', 'position' => 'Electrician', 'is_active_onsite' => false, 'id_number' => '3201010101010017', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0018', 'name' => 'Rian Hidayat', 'institution' => 'PT. Nusa Power Services', 'department' => 'Boiler', 'position' => 'Welder', 'is_active_onsite' => false, 'id_number' => '3201010101010018', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0019', 'name' => 'Siti Aminah', 'institution' => 'PT. Rekayasa Industri', 'department' => 'HSE', 'position' => 'Safety Inspector', 'is_active_onsite' => false, 'id_number' => '3201010101010019', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0020', 'name' => 'Taufik Hidayat', 'institution' => 'CV. Karya Teknik', 'department' => 'Mekanik Turbin', 'position' => 'Teknisi', 'is_active_onsite' => false, 'id_number' => '3201010101010020', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0021', 'name' => 'Utami Ningsih', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'Instrumen', 'position' => 'Instrument Tech', 'is_active_onsite' => false, 'id_number' => '3201010101010021', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0022', 'name' => 'Vicky Prasetyo', 'institution' => 'PT. Nusa Power Services', 'department' => 'Boiler', 'position' => 'Welder Fit', 'is_active_onsite' => false, 'id_number' => '3201010101010022', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0023', 'name' => 'Wahyudi Pratama', 'institution' => 'PT. Rekayasa Industri', 'department' => 'Rotating Equipment', 'position' => 'Machinist', 'is_active_onsite' => false, 'id_number' => '3201010101010023', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0024', 'name' => 'Xena Putri', 'institution' => 'CV. Karya Teknik', 'department' => 'Civil', 'position' => 'Worker', 'is_active_onsite' => false, 'id_number' => '3201010101010024', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0025', 'name' => 'Yayan Ruhian', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'Scaffolding', 'position' => 'Scaffolder', 'is_active_onsite' => false, 'id_number' => '3201010101010025', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0026', 'name' => 'Zulkifli Hasan', 'institution' => 'PT. Nusa Power Services', 'department' => 'Elektrikal', 'position' => 'Electrician Helper', 'is_active_onsite' => false, 'id_number' => '3201010101010026', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0027', 'name' => 'Adi Wijaya', 'institution' => 'PT. Rekayasa Industri', 'department' => 'Mekanik Turbin', 'position' => 'Helper', 'is_active_onsite' => false, 'id_number' => '3201010101010027', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0028', 'name' => 'Bella Cantika', 'institution' => 'PT. Daya Energi Mandiri', 'department' => 'HSE', 'position' => 'Safety Admin', 'is_active_onsite' => false, 'id_number' => '3201010101010028', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0029', 'name' => 'Cecep Rahman', 'institution' => 'CV. Karya Teknik', 'department' => 'Civil', 'position' => 'Foreman', 'is_active_onsite' => false, 'id_number' => '3201010101010029', 'password' => Hash::make('password'), 'last_seen_at' => now()],
            ['staff_code' => 'OS-0030', 'name' => 'Dani Darmawan', 'institution' => 'PT. Nusa Power Services', 'department' => 'Boiler', 'position' => 'Welder Helper', 'is_active_onsite' => false, 'id_number' => '3201010101010030', 'password' => Hash::make('password'), 'last_seen_at' => now()],
        ];

        foreach ($staffData as $data) {
            OutsourcingStaff::firstOrCreate(
                ['staff_code' => $data['staff_code']],
                $data
            );
        }
    }
}
