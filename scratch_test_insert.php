<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OutsourcingStaff;

try {
    $staff = OutsourcingStaff::create([
        'staff_code' => 'OS-TEST-99',
        'name' => 'Pegawai Uji DB',
        'institution' => 'Vendor Test DB',
        'department' => 'IT',
        'position' => 'Developer',
        'phone' => '08123456789',
        'id_number' => '1234567890123456',
        'contract_start_date' => '2026-08-01',
        'contract_end_date' => '2026-08-25',
        'password' => bcrypt('password'),
    ]);

    echo "BERHASIL MENYIMPAN: ID = {$staff->id}\n";
    $staff->delete();
    echo "Pembersihan berhasil.\n";
} catch (\Exception $e) {
    echo "GAGAL MENYIMPAN: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
exit(0);
