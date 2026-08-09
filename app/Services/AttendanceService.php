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
                    'message' => 'Pegawai sudah tercatat berada di dalam area (sudah check-in).',
                    'attendance' => null,
                ];
            }

            // Validasi Jadwal Shift & Rentang Jam Masuk
            $shiftValidation = $this->validateShiftWindow($staff, AttendanceStatus::CHECK_IN);
            if (!$shiftValidation['valid']) {
                return [
                    'success' => false,
                    'message' => $shiftValidation['message'],
                    'attendance' => null,
                ];
            }
            $shiftId = $shiftValidation['shift']?->id;

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
                    ? 'Akses masuk ruangan tercatat dengan catatan peringatan.'
                    : 'Akses masuk ruangan berhasil! Selamat bekerja dengan selamat.',
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
                    'message' => 'Pegawai belum tercatat masuk ruangan. Silakan scan masuk terlebih dahulu.',
                    'attendance' => null,
                ];
            }

            // Validasi Jadwal Shift & Akses Keluar
            $shiftValidation = $this->validateShiftWindow($staff, AttendanceStatus::CHECK_OUT);
            if (!$shiftValidation['valid']) {
                return [
                    'success' => false,
                    'message' => $shiftValidation['message'],
                    'attendance' => null,
                ];
            }
            $shiftId = $shiftValidation['shift']?->id;

            $zone = $this->geofenceService->validatePosition($lat, $lng);
            $isFlagged = false;
            $flagReason = null;

            // Check contract date validity
            if ($staff->contract_start_date && $staff->contract_end_date) {
                if (!now()->between($staff->contract_start_date->startOfDay(), $staff->contract_end_date->endOfDay())) {
                    $isFlagged = true;
                    $flagReason = 'Akses dilakukan di luar masa kontrak aktif (' . $staff->contract_start_date->format('d/m/Y') . ' s/d ' . $staff->contract_end_date->format('d/m/Y') . ')';
                }
            }

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
                'message' => 'Akses keluar ruangan berhasil! Hati-hati di jalan.',
                'attendance' => $attendance,
            ];
        });
    }

    /**
     * Menentukan shift dan memvalidasi apakah waktu saat ini diizinkan untuk presensi masuk / pulang.
     */
    public function validateShiftWindow(OutsourcingStaff $staff, AttendanceStatus $type): array
    {
        $todayDate = now()->format('Y-m-d');
        
        // 1. Ambil jadwal terdaftar jika ada
        $schedule = \App\Models\StaffSchedule::with('shift')
            ->where('staff_id', $staff->id)
            ->where('schedule_date', $todayDate)
            ->first();
            
        $shift = $schedule?->shift;

        // 2. Jika tidak ada jadwal khusus hari ini, cari shift yang mencakup waktu sekarang
        if (!$shift) {
            $nowTime = now()->format('H:i:s');
            $allShifts = \App\Models\Shift::all();

            foreach ($allShifts as $s) {
                $start = $s->start_time;
                $end = $s->end_time;

                if ($end <= $start) {
                    // Shift malam / sore lintas hari (misal 16:00 - 00:00 atau 20:00 - 04:00)
                    if ($nowTime >= $start || $nowTime < $end || $end === '00:00:00') {
                        if ($nowTime >= $start || ($end === '00:00:00' && $nowTime >= '16:00:00')) {
                            $shift = $s;
                            break;
                        }
                    }
                } else {
                    if ($nowTime >= $start && $nowTime <= $end) {
                        $shift = $s;
                        break;
                    }
                }
            }

            // Fallback ke shift terdekat jika di luar jam shift standar
            if (!$shift && $allShifts->count() > 0) {
                $shift = $allShifts->first();
            }
        }

        if ($type === AttendanceStatus::CHECK_IN) {
            // Cek apakah sudah pernah check-in hari ini
            $existingCheckIn = Attendance::where('staff_id', $staff->id)
                ->where('status', AttendanceStatus::CHECK_IN)
                ->whereDate('checked_at', $todayDate)
                ->latest('checked_at')
                ->first();

            if ($existingCheckIn && $staff->is_active_onsite) {
                return [
                    'valid' => false,
                    'message' => "Anda sudah mencatat akses masuk ruangan hari ini pada pukul " . $existingCheckIn->checked_at->format('H:i') . " WIB.",
                    'shift' => $shift,
                ];
            }
        } elseif ($type === AttendanceStatus::CHECK_OUT) {
            // Cek log check-in terakhir
            $lastCheckIn = Attendance::where('staff_id', $staff->id)
                ->where('status', AttendanceStatus::CHECK_IN)
                ->latest('checked_at')
                ->first();

            if ($lastCheckIn) {
                // Cegah scan ganda instan (minimal 30 detik setelah scan masuk)
                if (now()->diffInSeconds($lastCheckIn->checked_at) < 30) {
                    return [
                        'valid' => false,
                        'message' => "Anda baru saja melakukan scan masuk pada " . $lastCheckIn->checked_at->format('H:i:s') . " WIB. Silakan tunggu beberapa detik untuk scan keluar.",
                        'shift' => $shift,
                    ];
                }
            }
        }

        return [
            'valid' => true,
            'message' => null,
            'shift' => $shift,
        ];
    }

    private function getShiftTimeWindow(\App\Models\Shift $shift, AttendanceStatus $type, \Carbon\Carbon $now): array
    {
        $today = $now->copy()->startOfDay();
        
        $startParts = explode(':', $shift->start_time);
        $endParts = explode(':', $shift->end_time);

        $shiftStart = $today->copy()->setTime((int)$startParts[0], (int)$startParts[1], 0);
        $shiftEnd = $today->copy()->setTime((int)$endParts[0], (int)$endParts[1], 0);

        // Jika shift lintas hari (misal 16:00 - 00:00 atau 20:00 - 04:00)
        if ($shiftEnd->lte($shiftStart)) {
            $shiftEnd->addDay();
        }

        // Khusus Shift Malam yang mulai jam 00:00 (presensi masuk mulai jam 22:00 malam sebelumnya)
        if ((int)$startParts[0] === 0 && $now->hour >= 22) {
            $shiftStart->addDay();
            $shiftEnd->addDay();
        }

        if ($type === AttendanceStatus::CHECK_IN) {
            return [
                'start' => $shiftStart->copy()->subHours(2), // 2 jam sebelum mulai
                'end'   => $shiftStart->copy()->addHours(4), // batas 4 jam setelah mulai
            ];
        } else {
            return [
                'start' => $shiftEnd->copy()->subMinutes(30), // 30 menit sebelum pulang
                'end'   => $shiftEnd->copy()->addHours(4),    // batas 4 jam setelah pulang
            ];
        }
    }

    private function isTimeInShiftWindow(\App\Models\Shift $shift, AttendanceStatus $type, \Carbon\Carbon $now): bool
    {
        $window = $this->getShiftTimeWindow($shift, $type, $now);
        return $now->between($window['start'], $window['end']);
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
