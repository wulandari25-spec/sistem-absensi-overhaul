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
        $staffs = OutsourcingStaff::registered()
            ->withFaceData()
            ->select(['id', 'staff_code', 'name', 'face_descriptor'])
            ->get();

        return response()->json([
            'success' => true,
            'descriptors' => $staffs->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'staff_code' => $staff->staff_code,
                    'name' => $staff->name,
                    'descriptor' => $staff->face_descriptor,
                ];
            }),
        ]);
    }

    public function matchFace(Request $request): JsonResponse
    {
        $request->validate([
            'face_descriptor' => 'required|array',
            'face_descriptor.*' => 'numeric',
        ]);

        $result = $this->faceMatchingService->findBestMatch($request->input('face_descriptor'));

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
                'confidence' => round(1 - $result['distance'], 4),
                'distance' => round($result['distance'], 4),
            ]);
        }

        return response()->json([
            'success' => true,
            'matched' => false,
            'message' => 'Tidak ditemukan kecocokan wajah. Silakan coba lagi atau gunakan QR Code.',
            'distance' => $result['staff'] ? round($result['distance'], 4) : null,
        ]);
    }

    public function registerFace(Request $request): JsonResponse
    {
        $request->validate([
            'staff_id' => 'required|integer|exists:outsourcing_staffs,id',
            'face_descriptor' => 'required|array|min:128|max:128',
            'face_descriptor.*' => 'numeric',
        ]);

        $staff = OutsourcingStaff::findOrFail($request->input('staff_id'));
        $staff->update([
            'face_descriptor' => $request->input('face_descriptor'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Data wajah untuk {$staff->name} berhasil didaftarkan.",
        ]);
    }
}