<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Enums\AttendanceStatus;
use App\Models\OutsourcingStaff;
use App\Services\QrTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QrCodeController extends Controller
{
    public function __construct(
        private QrTokenService $qrTokenService,
    ) {}

    public function generateQr(Request $request): JsonResponse
    {
        $request->validate([
            'staff_id' => 'required|integer|exists:outsourcing_staffs,id',
            'purpose' => 'required|in:check_in,check_out',
        ]);

        $purpose = AttendanceStatus::from($request->input('purpose'));
        $qrToken = $this->qrTokenService->generateToken(
            (int) $request->input('staff_id'),
            $purpose,
        );

        return response()->json([
            'success' => true,
            'token' => $qrToken->token,
            'expires_at' => $qrToken->expires_at->format('H:i:s'),
            'expires_in_seconds' => now()->diffInSeconds($qrToken->expires_at),
        ]);
    }

    public function validateQr(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $qrToken = $this->qrTokenService->validateToken($request->input('token'));

        if (!$qrToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token QR tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        $staff = $qrToken->staff;

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'staff_code' => $staff->staff_code,
                'name' => $staff->name,
                'institution' => $staff->institution,
            ],
            'purpose' => $qrToken->purpose->value,
        ]);
    }
}
