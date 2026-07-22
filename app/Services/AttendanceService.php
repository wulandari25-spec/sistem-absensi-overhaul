<?php

namespace App\Services;

use App\Enums\AttendanceMethod;
use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\OutsourcingStaff;
use App\Models\GeofenceZone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    public function __construct(
        private GeofenceService $geofenceService,
        private QrTokenService $qrTokenService,
    ) {}

    public function processCheckIn(
        int $staffId,
        AttendanceMethod $method,
        float $lat,
        float $lng,
        ?string $proofPhoto = null,
        ?float $confidenceScore = null,
        ?string $deviceInfo = null,
    ): array {
        return DB::transaction(function () use ($staffId, $method, $lat, $lng, $proofPhoto, $confidenceScore, $deviceInfo) {
            $staff = OutsourcingStaff::findOrFail($staffId);

            if ($staff->is_active_onsite) {
                return [
                    'success' => false,
                    'message' => 'Pegawai sudah tercatat berada di dalam area. Silakan check-out terlebih dahulu.',
                    'attendance' => null,
                ];
            }

            $zone = $this->geofenceService->validatePosition($lat, $lng);
            $isFlagged = false;
            $flagReason = null;

            if (!$zone) {
                $isFlagged = true;
                $flagReason = 'Presensi dilakukan di luar area geofence yang diizinkan';
                Log::warning("Geofence violation: Staff {$staffId} attempted check-in outside zones", [
                    'lat' => $lat, 'lng' => $lng,
                ]);
            }

            // Check contract date validity
            if ($staff->contract_start_date && $staff->contract_end_date) {
                if (!now()->between($staff->contract_start_date->startOfDay(), $staff->contract_end_date->endOfDay())) {
                    $isFlagged = true;
                    $flagReason = 'Presensi dilakukan di luar masa kontrak aktif (' . $staff->contract_start_date->format('d/m/Y') . ' s/d ' . $staff->contract_end_date->format('d/m/Y') . ')';
                }
            }

            if ($method === AttendanceMethod::FACE_RECOGNITION && $confidenceScore !== null && $confidenceScore < 0.6) {
                $isFlagged = true;
                $flagReason = "Skor kecocokan wajah rendah: {$confidenceScore}";
            }

            $todayDate = now()->format('Y-m-d');
            $schedule = \App\Models\StaffSchedule::where('staff_id', $staffId)
                ->where('schedule_date', $todayDate)
                ->first();
            $shiftId = $schedule?->shift_id;

            $attendance = Attendance::create([
                'staff_id' => $staffId,
                'geofence_zone_id' => $zone?->id,
                'shift_id' => $shiftId,
                'method' => $method,
                'status' => AttendanceStatus::CHECK_IN,
                'latitude' => $lat,
                'longitude' => $lng,
                'proof_photo' => $proofPhoto,
                'confidence_score' => $confidenceScore,
                'is_flagged' => $isFlagged,
                'flag_reason' => $flagReason,
                'device_info' => $deviceInfo,
                'checked_at' => now(),
            ]);

            $staff->markOnsite();

            return [
                'success' => true,
                'message' => $isFlagged
                    ? 'Check-in tercatat dengan peringatan. Petugas keamanan akan mereview.'
                    : 'Check-in berhasil! Selamat bekerja dengan selamat.',
                'attendance' => $attendance,
            ];
        });
    }

    public function processCheckOut(
        int $staffId,
        AttendanceMethod $method,
        float $lat,
        float $lng,
        ?string $proofPhoto = null,
        ?float $confidenceScore = null,
        ?string $deviceInfo = null,
    ): array {
        return DB::transaction(function () use ($staffId, $method, $lat, $lng, $proofPhoto, $confidenceScore, $deviceInfo) {
            $staff = OutsourcingStaff::findOrFail($staffId);

            if (!$staff->is_active_onsite) {
                return [
                    'success' => false,
                    'message' => 'Pegawai belum tercatat check-in. Tidak dapat melakukan check-out.',
                    'attendance' => null,
                ];
            }

            $zone = $this->geofenceService->validatePosition($lat, $lng);
            $isFlagged = false;
            $flagReason = null;

            // Check contract date validity
            if ($staff->contract_start_date && $staff->contract_end_date) {
                if (!now()->between($staff->contract_start_date->startOfDay(), $staff->contract_end_date->endOfDay())) {
                    $isFlagged = true;
                    $flagReason = 'Presensi dilakukan di luar masa kontrak aktif (' . $staff->contract_start_date->format('d/m/Y') . ' s/d ' . $staff->contract_end_date->format('d/m/Y') . ')';
                }
            }

            $todayDate = now()->format('Y-m-d');
            $schedule = \App\Models\StaffSchedule::where('staff_id', $staffId)
                ->where('schedule_date', $todayDate)
                ->first();
            $shiftId = $schedule?->shift_id;

            $attendance = Attendance::create([
                'staff_id' => $staffId,
                'geofence_zone_id' => $zone?->id,
                'shift_id' => $shiftId,
                'method' => $method,
                'status' => AttendanceStatus::CHECK_OUT,
                'latitude' => $lat,
                'longitude' => $lng,
                'proof_photo' => $proofPhoto,
                'confidence_score' => $confidenceScore,
                'is_flagged' => $isFlagged,
                'flag_reason' => $flagReason,
                'device_info' => $deviceInfo,
                'checked_at' => now(),
            ]);

            $staff->markOffsite();

            return [
                'success' => true,
                'message' => 'Check-out berhasil! Hati-hati di jalan.',
                'attendance' => $attendance,
            ];
        });
    }

    public function getActiveStaffCount(): int
    {
        return OutsourcingStaff::activeOnsite()->count();
    }

    public function getRecentLogs(int $limit = 50): Collection
    {
        return Attendance::with(['staff', 'geofenceZone'])
            ->today()
            ->recent($limit)
            ->get();
    }
    public function identifyStaffByFace(array $faceDescriptor): ?OutsourcingStaff
{
    $semuaPekerja = OutsourcingStaff::whereNotNull('face_descriptor')->get();
    $pekerjaTerbaik = null;
    $jarakTerkecil = 999;

    foreach ($semuaPekerja as $pekerja) {
        // Menggunakan logika Euclidean yang sama dengan PresensiController
        $jarak = $this->hitungEuclideanDistance($pekerja->face_descriptor, $faceDescriptor);
        
        if ($jarak < $jarakTerkecil) {
            $jarakTerkecil = $jarak;
            $pekerjaTerbaik = $pekerja;
        }
    }

    // Threshold 0.6 sesuai dengan logika sebelumnya
    return ($jarakTerkecil <= 0.6) ? $pekerjaTerbaik : null;
}

// Tambahkan helper ini
private function hitungEuclideanDistance(array $a, array $b): float
{
    $jumlah = 0;
    for ($i = 0; $i < count($a); $i++) {
        $jumlah += ($a[$i] - $b[$i]) ** 2;
    }
    return sqrt($jumlah);
}

    public function getTodayStats(): array
    {
        $today = today();

        return [
            'active_onsite' => $this->getActiveStaffCount(),
            'total_check_ins' => Attendance::checkIns()->whereDate('checked_at', $today)->count(),
            'total_check_outs' => Attendance::checkOuts()->whereDate('checked_at', $today)->count(),
            'flagged_count' => Attendance::flagged()->whereDate('checked_at', $today)->count(),
            'face_recognition_count' => Attendance::where('method', AttendanceMethod::FACE_RECOGNITION)->whereDate('checked_at', $today)->count(),
            'qr_code_count' => Attendance::where('method', AttendanceMethod::QR_CODE)->whereDate('checked_at', $today)->count(),
            'unique_staff_today' => Attendance::whereDate('checked_at', $today)->distinct('staff_id')->count('staff_id'),
        ];
    }

    public function getFlaggedRecords(int $limit = 20): Collection
    {
        return Attendance::with(['staff'])
            ->flagged()
            ->today()
            ->recent($limit)
            ->get();
    }

    public function getHourlyPopulation(): array
    {
        // Ambil absensi 24 jam terakhir agar grafik selalu terisi terlepas dari zona waktu server
        $attendances = Attendance::where('checked_at', '>=', now()->subHours(24))->get();

        $checkIns = array_fill(0, 24, 0);
        $checkOuts = array_fill(0, 24, 0);

        foreach ($attendances as $att) {
            if ($att->checked_at) {
                $h = (int) $att->checked_at->format('H');
                if ($att->status === AttendanceStatus::CHECK_IN) {
                    $checkIns[$h]++;
                } elseif ($att->status === AttendanceStatus::CHECK_OUT) {
                    $checkOuts[$h]++;
                }
            }
        }

        $hours = [];
        for ($h = 0; $h < 24; $h++) {
            $hours[] = [
                'hour' => sprintf('%02d:00', $h),
                'check_ins' => $checkIns[$h],
                'check_outs' => $checkOuts[$h],
            ];
        }

        return $hours;
    }
}
