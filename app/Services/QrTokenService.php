<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\QrToken;
use App\Models\OutsourcingStaff;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class QrTokenService
{
    private const TOKEN_TTL_MINUTES = 5;

    public function generateToken(int $staffId, AttendanceStatus $purpose = AttendanceStatus::CHECK_IN): QrToken
    {
        QrToken::where('staff_id', $staffId)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->update(['is_used' => true]);

        $payload = json_encode([
            'staff_id' => $staffId,
            'purpose' => $purpose->value,
            'nonce' => Str::random(16),
            'issued_at' => now()->timestamp,
        ]);

        $encryptedToken = Crypt::encryptString($payload);

        return QrToken::create([
            'staff_id' => $staffId,
            'token' => $encryptedToken,
            'purpose' => $purpose,
            'is_used' => false,
            'expires_at' => now()->addMinutes(self::TOKEN_TTL_MINUTES),
        ]);
    }

    public function validateToken(string $token): ?QrToken
    {
        $qrToken = QrToken::where('token', $token)->valid()->first();

        if (!$qrToken) {
            return null;
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true);
            if ($payload['staff_id'] !== $qrToken->staff_id) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return $qrToken;
    }

    public function cleanupExpired(): int
    {
        return QrToken::expired()->delete();
    }
}
