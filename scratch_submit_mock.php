<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Requests\StoreStaffRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

$inputData1 = [
    'staff_code' => 'OS-9999',
    'name' => 'Pegawai Uji Coba',
    'institution' => 'Vendor Test',
    'contract_start_date' => '2026-08-01',
    'contract_end_date' => '2026-08-10', // 9 days difference, < 20 days
];

$request1 = StoreStaffRequest::create('/admin/staffs', 'POST', $inputData1);
// Bind the request to container so it can resolve properly
app()->instance('request', $request1);

$rules = $request1->rules();
$validator1 = Validator::make($inputData1, $rules);
if (method_exists($request1, 'withValidator')) {
    // We bind the request context using closure binding or pass it
    $request1->withValidator($validator1);
}

if ($validator1->fails()) {
    echo "PENGUJIAN VALIDASI 1 (Kontrak 9 hari) - GAGAL SANGAT BAGUS:\n";
    print_r($validator1->errors()->all());
} else {
    echo "PENGUJIAN VALIDASI 1: Lolos (Ini aneh jika lolos)\n";
}

$inputData2 = [
    'staff_code' => 'OS-9999',
    'name' => 'Pegawai Uji Coba',
    'institution' => 'Vendor Test',
    'contract_start_date' => '2026-08-01',
    'contract_end_date' => '2026-08-25', // 24 days, should pass
];

$request2 = StoreStaffRequest::create('/admin/staffs', 'POST', $inputData2);
app()->instance('request', $request2);
$validator2 = Validator::make($inputData2, $rules);
if (method_exists($request2, 'withValidator')) {
    $request2->withValidator($validator2);
}

if ($validator2->fails()) {
    echo "\nPENGUJIAN VALIDASI 2 (Kontrak 24 hari) - GAGAL:\n";
    print_r($validator2->errors()->all());
} else {
    echo "\nPENGUJIAN VALIDASI 2: Lolos\n";
}

// Let's delete the script
exit(0);
