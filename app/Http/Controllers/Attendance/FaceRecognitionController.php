<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\OutsourcingStaff;
use App\Services\FaceMatchingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FaceRecognitionController extends Controller
{
    public function __construct(private FaceMatchingService $faceMatchingService) {}

    public function getFaceDescriptors(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'descriptors' => [],
        ]);
    }

    public function matchFace(Request $request): JsonResponse
    {
        $request->validate([
            'proof_photo' => 'required|string',
        ]);

        $result = $this->faceMatchingService->findBestMatch($request->input('proof_photo'));

        if ($result['matched']) {
            $staff = $result['staff'];

            return response()->json([
                'success' => true,
                'matched' => true,
                'staff' => [
                    'id' => $staff->id,
                    'staff_code' => $staff->staff_code,
                    'name' => $staff->name,
                    'institution' => $staff->institution,
                ],
                'confidence' => round($result['confidence'], 4),
                'distance' => round($result['distance'], 4),
            ]);
        }

        return response()->json([
            'success' => true,
            'matched' => false,
            'message' => $result['message'] ?? 'Tidak ditemukan kecocokan wajah. Silakan coba lagi atau gunakan QR Code.',
            'distance' => round($result['distance'], 4),
        ]);
    }

    public function registerFace(Request $request): JsonResponse
    {
        $request->validate([
            'staff_id' => 'required|integer|exists:outsourcing_staffs,id',
            'proof_photo' => 'required|string',
        ]);

        $staff = OutsourcingStaff::findOrFail($request->input('staff_id'));
        
        $result = $this->faceMatchingService->registerFace($staff->staff_code, $request->input('proof_photo'));

        if ($result['success']) {
            $staff->update([
                'is_registered' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Data wajah untuk {$staff->name} berhasil didaftarkan.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? "Gagal mendaftarkan wajah.",
        ], 422);
    }
}