<?php

namespace App\Http\Controllers;

use App\Models\LogKeamanan;
use App\Models\PekerjaOutsourcing;
use App\Services\GeofencingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PresensiController extends Controller
{
    /** Threshold jarak Euclidean deskriptor wajah (semakin kecil = semakin mirip) */
    const FACE_MATCH_THRESHOLD = 0.6;

    public function index()
    {
        $pekerja = auth()->user()->pekerjaOutsourcing;
        return view('presensi.index', compact('pekerja'));
    }

    /** Endpoint presensi via Face Recognition + Liveness + Geolocation */
    public function storeFace(Request $request)
    {
        Log::info('Data presensi diterima (Identifikasi Otomatis):', $request->only(['latitude', 'longitude', 'tipe_akses']));

        $data = $request->validate([
            'face_descriptor' => 'required|array',
            'liveness_valid'  => 'required|boolean',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
            'tipe_akses'      => 'required|in:masuk,keluar',
        ]);

        if (!$data['liveness_valid']) {
            return response()->json(['status' => 'gagal', 'pesan' => 'Liveness check gagal.'], 422);
        }

        // 1. CARI SIAPA PEMILIK WAJAH INI (Mencari di seluruh database pekerja)
        $semuaPekerja = PekerjaOutsourcing::whereNotNull('face_descriptor')->get();
        $pekerjaTerbaik = null;
        $jarakTerkecil = 999;

        foreach ($semuaPekerja as $pekerja) {
            $jarak = $this->hitungEuclideanDistance($pekerja->face_descriptor, $data['face_descriptor']);
            
            if ($jarak < $jarakTerkecil) {
                $jarakTerkecil = $jarak;
                $pekerjaTerbaik = $pekerja;
            }
        }

        // 2. CEK APAKAH DITEMUKAN DAN SESUAI THRESHOLD
        if (!$pekerjaTerbaik || $jarakTerkecil > self::FACE_MATCH_THRESHOLD) {
            return response()->json(['status' => 'gagal', 'pesan' => 'Wajah tidak terdaftar atau tidak cocok.'], 422);
        }

        // 3. JIKA DITEMUKAN, PROSES LOGIKA SEPERTI BIASA
        $pekerja = $pekerjaTerbaik;
        $unit = $pekerja->unitInstalasi;
        
        // (Lanjutkan dengan logika Geofencing Anda)
        $geofence = GeofencingService::dalamRadius($data['latitude'], $data['longitude'], $unit->latitude, $unit->longitude, $unit->radius_meter);

        if (!$geofence['valid']) {
            return response()->json(['status' => 'gagal', 'pesan' => "Anda berada di luar area kerja."], 422);
        }

        $log = LogKeamanan::create([
            'pekerja_outsourcing_id' => $pekerja->id,
            'tipe_akses'    => $data['tipe_akses'],
            'metode'        => 'face_recognition',
            'latitude'      => $data['latitude'],
            'longitude'     => $data['longitude'],
            'jarak_meter'   => $geofence['jarak_meter'],
            'status_validasi' => true,
            'waktu_akses'   => now(),
        ]);

        return response()->json([
            'status' => 'sukses', 
            'pesan' => 'Presensi berhasil dicatat sebagai ' . $pekerja->nama, // Anda bisa pastikan nama siapa yang muncul
            'log_id' => $log->id
        ]);
    }

    /** Endpoint presensi via QR Code (fallback) */
    public function storeQr(Request $request)
    {
        $data = $request->validate([
            'token'      => 'required|string',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'tipe_akses' => 'required|in:masuk,keluar',
        ]);

        $qrController = app(QrTokenController::class);
        $hasilValidasi = $qrController->validasi($data['token']);

        if (!$hasilValidasi) {
            return response()->json(['status' => 'gagal', 'pesan' => 'Token QR tidak valid atau sudah kedaluwarsa.'], 422);
        }

        $pekerja = PekerjaOutsourcing::findOrFail($hasilValidasi['pekerja_id']);
        $unit = $pekerja->unitInstalasi;

        $geofence = GeofencingService::dalamRadius(
            $data['latitude'], $data['longitude'],
            $unit->latitude, $unit->longitude, $unit->radius_meter
        );

        if (!$geofence['valid']) {
            return response()->json([
                'status' => 'gagal',
                'pesan'  => "Anda berada di luar radius area kerja ({$geofence['jarak_meter']} meter).",
            ], 422);
        }

        $log = LogKeamanan::create([
            'pekerja_outsourcing_id' => $pekerja->id,
            'tipe_akses'    => $data['tipe_akses'],
            'metode'        => 'qr_code',
            'latitude'      => $data['latitude'],
            'longitude'     => $data['longitude'],
            'jarak_meter'   => $geofence['jarak_meter'],
            'status_validasi' => true,
            'waktu_akses'   => now(),
        ]);

        return response()->json(['status' => 'sukses', 'pesan' => 'Presensi via QR Code berhasil dicatat.', 'log_id' => $log->id]);
    }

    private function hitungEuclideanDistance(array $a, array $b): float
    {
        $jumlah = 0;
        for ($i = 0; $i < count($a); $i++) {
            $jumlah += ($a[$i] - $b[$i]) ** 2;
        }
        return sqrt($jumlah);
    }
}