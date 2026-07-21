<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PekerjaOutsourcing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PekerjaController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nik'                => 'required|string|unique:pekerja_outsourcings,nik',
            'nama'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'password'           => 'required|string|min:8',
            'vendor_id'          => 'required|exists:vendors,id',
            'unit_instalasi_id'  => 'required|exists:unit_instalasis,id',
            'foto_referensi'     => 'required|string',
            'face_descriptor'    => 'required|string',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['nama'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'karyawan',
            ]);

            $fotoPath = $data['foto_referensi'];

            PekerjaOutsourcing::create([
                'user_id'           => $user->id,
                'nik'               => $data['nik'],
                'nama'              => $data['nama'],
                'vendor_id'         => $data['vendor_id'],
                'unit_instalasi_id' => $data['unit_instalasi_id'],
                'foto_referensi'    => $fotoPath,
                'face_descriptor'   => json_decode($data['face_descriptor']),
                'status'            => 'aktif',
            ]);
        });

        return redirect()->route('pekerja.index')->with('success', 'Pekerja & akun login berhasil dibuat.');
    }
}