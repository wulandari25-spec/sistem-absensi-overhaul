<?php

namespace App\Http\Controllers;

use App\Models\PekerjaOutsourcing;
use App\Models\QrToken;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrTokenController extends Controller
{
    /** Generate token QR baru untuk pekerja yang sedang login */
    public function generate()
    {
        $pekerja = auth()->user()->pekerjaOutsourcing; // pastikan relasi user->pekerja ada

        $payload = [
            'pekerja_id' => $pekerja->id,
            'nik'        => $pekerja->nik,
            'nonce'      => Str::random(16),
            'timestamp'  => now()->timestamp,
        ];

        $tokenTerenkripsi = Crypt::encryptString(json_encode($payload));

        QrToken::create([
            'pekerja_outsourcing_id' => $pekerja->id,
            'token'      => $tokenTerenkripsi,
            'expired_at' => now()->addMinutes(5),
        ]);

        $qrSvg = QrCode::size(250)->generate($tokenTerenkripsi);

        return response()->json([
            'qr_svg' => $qrSvg,
            'token'  => $tokenTerenkripsi,
            'berlaku_hingga' => now()->addMinutes(5)->toDateTimeString(),
        ]);
    }

    /** Validasi token QR (dipanggil dari PresensiController) */
    public function validasi(string $tokenInput): ?array
    {
        $qrToken = QrToken::where('token', $tokenInput)->first();

        if (!$qrToken) {
            return null; // token tidak dikenal / bukan hasil generate sistem
        }

        if ($qrToken->used_at !== null) {
            return null; // token sudah pernah dipakai (mencegah replay attack)
        }

        if (now()->greaterThan($qrToken->expired_at)) {
            return null; // token kedaluwarsa
        }

        try {
            $payload = json_decode(Crypt::decryptString($tokenInput), true);
        } catch (\Exception $e) {
            return null; // gagal dekripsi = token dipalsukan
        }

        $qrToken->update(['used_at' => now()]);

        return [
            'pekerja_id'  => $payload['pekerja_id'],
            'qr_token_id' => $qrToken->id,
        ];
    }
}