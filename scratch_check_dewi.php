<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OutsourcingStaff;

$staffs = OutsourcingStaff::where('name', 'LIKE', '%Dewi%')
    ->orWhere('name', 'LIKE', '%Ningtiyas%')
    ->orWhere('name', 'LIKE', '%Ningtiyah%')
    ->orWhere('name', 'LIKE', '%Cahya%')
    ->get();

if ($staffs->isEmpty()) {
    echo "Karyawan pencarian nama tidak ditemukan. Mencari kemiripan...\n";
    $staffs = OutsourcingStaff::where('name', 'LIKE', '%Dew%')->get();
}

foreach ($staffs as $staff) {
    echo "ID: {$staff->id}, Nama: {$staff->name}, Kode: {$staff->staff_code}, Foto: " . ($staff->photo_profile ?? 'NULL') . ", Descriptor: " . ($staff->face_descriptor ? 'ADA (' . count($staff->face_descriptor) . ')' : 'NULL') . "\n";
}

exit(0);
