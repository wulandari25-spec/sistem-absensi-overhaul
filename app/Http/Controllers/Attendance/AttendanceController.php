<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Enums\AttendanceMethod;
use App\Enums\AttendanceStatus;
use App\Models\OutsourcingStaff;
use App\Services\AttendanceService;
use App\Services\FaceMatchingService;
use App\Services\QrTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private QrTokenService $qrTokenService,
        private FaceMatchingService $faceMatchingService,
    ) {}

    public function showCheckIn()
    {
        return view('attendance.check-in');
    }

    public function processHybridAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'staff_id' => 'nullable|integer|exists:outsourcing_staffs,id',
            'method' => 'required|in:face_recognition,qr_code',
            'status' => 'required|in:check_in,check_out',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'face_descriptor' => 'required_if:method,face_recognition|array',
            'proof_photo' => 'nullable|string',
            'confidence_score' => 'nullable|numeric',
            'qr_token' => 'nullable|string',
            'scanned_code' => 'nullable|string',
        ]);

        $method = AttendanceMethod::from($request->input('method'));
        $status = AttendanceStatus::from($request->input('status'));
        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');
        $deviceInfo = $request->userAgent();

        $staffId = null;

        if ($method === AttendanceMethod::FACE_RECOGNITION) {
            $descriptor = $request->input('face_descriptor');
            $result = $this->faceMatchingService->findBestMatch($descriptor);

            if (!$result['matched']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi wajah gagal saat proses akhir. Silakan ulangi.',
                ], 422);
            }

            // Keamanan Ganda: Pastikan wajah yang di-scan cocok dengan karyawan yang sedang login
            $loggedInStaffId = session('logged_in_staff_id');
            if ($loggedInStaffId && $result['staff']->id != $loggedInStaffId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deteksi wajah gagal: Wajah tidak cocok dengan akun login (' . session('logged_in_staff_name') . ').',
                ], 422);
            }

            // staff_id ditentukan oleh SERVER hasil pencocokan wajah,
            // bukan dipercaya dari input client.
            $staffId = $result['staff']->id;
        } elseif ($method === AttendanceMethod::QR_CODE) {
            // Cek apakah karyawan sedang login sesi
            $loggedInStaffId = session('logged_in_staff_id');
            $loggedInStaffCode = session('logged_in_staff_code');

            if ($loggedInStaffId) {
                $scannedCode = $request->input('scanned_code');
                $expectedCode = $loggedInStaffCode ?: (\App\Models\OutsourcingStaff::find($loggedInStaffId)?->staff_code);
                
                if (trim($scannedCode) !== trim($expectedCode)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Absensi QR Gagal: QR Code yang di-scan tidak sesuai dengan akun Anda.',
                    ], 422);
                }
                $staffId = $loggedInStaffId;
            } else {
                // Fallback: Model QR Code dinamis berbasis token
                $qrToken = $this->qrTokenService->validateToken($request->input('qr_token'));
                if (!$qrToken) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token QR Code tidak valid atau sudah kedaluwarsa.',
                    ], 422);
                }
                $staffId = $qrToken->staff_id;
                $qrToken->markUsed();
            }
        }

        if ($status === AttendanceStatus::CHECK_IN) {
            $result = $this->attendanceService->processCheckIn(
                staffId: $staffId,
                method: $method,
                lat: $lat,
                lng: $lng,
                proofPhoto: $request->input('proof_photo'),
                confidenceScore: $request->input('confidence_score') ? (float) $request->input('confidence_score') : null,
                deviceInfo: $deviceInfo,
            );
        } else {
            $result = $this->attendanceService->processCheckOut(
                staffId: $staffId,
                method: $method,
                lat: $lat,
                lng: $lng,
                proofPhoto: $request->input('proof_photo'),
                confidenceScore: $request->input('confidence_score') ? (float) $request->input('confidence_score') : null,
                deviceInfo: $deviceInfo,
            );
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function showQrFallback()
    {
        return view('attendance.qr-scan');
    }

    /**
     * Tampilkan riwayat presensi mingguan untuk satu karyawan.
     */
    public function showHistory($staffId)
    {
        $staff = OutsourcingStaff::findOrFail($staffId);
        
        $startOfWeek = now()->startOfWeek(\Carbon\CarbonInterface::MONDAY);
        $endOfWeek = now()->endOfWeek(\Carbon\CarbonInterface::SUNDAY);
        
        $attendances = \App\Models\Attendance::with('geofenceZone')
            ->where('staff_id', $staffId)
            ->whereBetween('checked_at', [$startOfWeek, $endOfWeek])
            ->orderBy('checked_at', 'desc')
            ->get();
            
        return view('attendance.history', compact('staff', 'attendances', 'startOfWeek', 'endOfWeek'));
    }

    public function showPermitForm()
    {
        $staffId = session('logged_in_staff_id');
        $staff = OutsourcingStaff::findOrFail($staffId);
        return view('attendance.request-permit', compact('staff'));
    }

    public function storePermitRequest(Request $request)
    {
        $request->validate([
            'status' => 'required|in:permit,sick',
            'notes' => 'required|string|max:500',
            'proof_photo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ], [
            'status.required' => 'Pilih jenis pengajuan (Izin atau Sakit).',
            'notes.required' => 'Alasan/keterangan wajib diisi.',
            'proof_photo.image' => 'File bukti harus berupa gambar.',
            'proof_photo.max' => 'Ukuran file surat/bukti maksimal 2MB.',
        ]);

        $staffId = session('logged_in_staff_id');
        $staff = OutsourcingStaff::findOrFail($staffId);

        $proofPhotoPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPhotoPath = $request->file('proof_photo')->store('proof-photos', 'public');
        }

        // Simpan data absensi manual
        $staff->attendances()->create([
            'status' => $request->input('status'),
            'method' => 'manual',
            'notes' => $request->input('notes'),
            'proof_photo' => $proofPhotoPath,
            'checked_at' => now(),
        ]);

        // Karyawan menjadi offsite setelah mengajukan izin
        $staff->update([
            'is_active_onsite' => false,
            'last_seen_at' => now(),
        ]);

        return redirect()->route('attendance.history', $staff->id)
            ->with('success', 'Pengajuan ' . ($request->input('status') === 'permit' ? 'Izin' : 'Sakit') . ' berhasil dikirim.');
    }
}