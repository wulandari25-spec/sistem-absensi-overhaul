<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\OutsourcingStaff;

$staff = OutsourcingStaff::where('name', 'LIKE', '%Dewi Cahya Ningtiyas%')->first();

if (!$staff) {
    echo "Dewi Cahya Ningtiyas tidak ditemukan.\n";
    exit(1);
}

echo "Nama: {$staff->name}\n";
echo "Foto: {$staff->photo_profile}\n";
echo "Descriptor Status: " . ($staff->face_descriptor ? 'ADA (' . count($staff->face_descriptor) . ')' : 'NULL') . "\n";

exit(0);
