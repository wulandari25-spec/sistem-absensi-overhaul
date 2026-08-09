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

    public function showAdminScan()
    {
        return view('admin.daily-attendance.scan');
    }

    public function processHybridAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'staff_id' => 'nullable|integer|exists:outsourcing_staffs,id',
            'method' => 'required|in:face_recognition,qr_code',
            'status' => 'nullable|in:check_in,check_out',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'proof_photo' => 'required_if:method,face_recognition|nullable|string',
            'confidence_score' => 'nullable|numeric',
            'qr_token' => 'nullable|string',
            'scanned_code' => 'nullable|string',
        ]);

        $method = AttendanceMethod::from($request->input('method'));
        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');
        $deviceInfo = $request->userAgent();

        $staffId = null;
        $proofPhotoPath = null;
        $confidenceScore = $request->input('confidence_score') ? (float) $request->input('confidence_score') : null;

        if ($method === AttendanceMethod::FACE_RECOGNITION) {
            $photoBase64 = $request->input('proof_photo');
            $result = $this->faceMatchingService->findBestMatch($photoBase64);

            if (!$result['matched']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Verifikasi wajah gagal saat proses akhir. Silakan ulangi.',
                ], 422);
            }

            $staffId = $result['staff']->id;
            $confidenceScore = $result['confidence'] ?? null;

            // Simpan foto absen ke local storage
            try {
                if (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $typeMatches)) {
                    $fileData = substr($photoBase64, strpos($photoBase64, ',') + 1);
                    $ext = strtolower($typeMatches[1]);
                    if ($ext === 'jpg') $ext = 'jpeg';
                    
                    if (in_array($ext, ['jpeg', 'png', 'webp'])) {
                        $decoded = base64_decode($fileData);
                        if ($decoded !== false) {
                            $proofPhotoPath = 'attendance-photos/' . uniqid() . '.' . $ext;
                            \Illuminate\Support\Facades\Storage::disk('local')->put($proofPhotoPath, $decoded);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal menyimpan foto absensi: " . $e->getMessage());
            }
        } elseif ($method === AttendanceMethod::QR_CODE) {
            $qrInput = $request->input('qr_token');
            
            // 1. Cek apakah ini static staff_code (kartu cetak)
            $staff = OutsourcingStaff::where('staff_code', trim($qrInput))->first();
            
            if ($staff) {
                $staffId = $staff->id;
            } else {
                // 2. Fallback ke dynamic token
                $qrToken = $this->qrTokenService->validateToken($qrInput);
                if (!$qrToken) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code atau Token tidak valid / tidak ditemukan.',
                    ], 422);
                }
                $staffId = $qrToken->staff_id;
                $qrToken->markUsed();
            }
        }

        $staff = OutsourcingStaff::findOrFail($staffId);
        
        // DETEKSI STATUS OTOMATIS: jika sudah check-in (onsite), maka check-out. Jika belum, check-in.
        $status = $staff->is_active_onsite ? AttendanceStatus::CHECK_OUT : AttendanceStatus::CHECK_IN;

        if ($status === AttendanceStatus::CHECK_IN) {
            $result = $this->attendanceService->processCheckIn(
                staffId: $staffId,
                method: $method,
                lat: $lat,
                lng: $lng,
                proofPhoto: $proofPhotoPath,
                confidenceScore: $confidenceScore,
                deviceInfo: $deviceInfo,
            );
        } else {
            $result = $this->attendanceService->processCheckOut(
                staffId: $staffId,
                method: $method,
                lat: $lat,
                lng: $lng,
                proofPhoto: $proofPhotoPath,
                confidenceScore: $confidenceScore,
                deviceInfo: $deviceInfo,
            );
        }

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'attendance' => $result['attendance'],
                'staff' => [
                    'name' => $staff->name,
                    'staff_code' => $staff->staff_code,
                    'institution' => $staff->institution,
                    'department' => $staff->department ?? '-',
                    'position' => $staff->position ?? '-',
                    'phone' => $staff->phone ?? '-',
                    'id_number' => $staff->id_number ?? '-',
                    'contract_period' => ($staff->contract_start_date && $staff->contract_end_date)
                        ? $staff->contract_start_date->format('d M Y') . ' s/d ' . $staff->contract_end_date->format('d M Y')
                        : '-',
                    'photo_profile' => $staff->photo_profile ? asset('storage/' . $staff->photo_profile) : null,
                    'status_label' => $status->label()
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
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
            $proofPhotoPath = $request->file('proof_photo')->store('proof-photos', 'local');
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