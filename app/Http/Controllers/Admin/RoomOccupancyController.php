<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutsourcingStaff;
use App\Models\GeofenceZone;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use App\Services\AttendanceService;
use App\Services\FaceMatchingService;
use App\Services\QrTokenService;
use Illuminate\Http\Request;

class RoomOccupancyController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private QrTokenService $qrTokenService,
        private FaceMatchingService $faceMatchingService,
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $zoneId = $request->input('zone_id');

        // Geofence Zones / Ruangan
        $zones = GeofenceZone::where('is_active', true)->get();

        // Query karyawan yang terdaftar
        $staffQuery = OutsourcingStaff::registered();

        if ($search) {
            $staffQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        // Tampilkan karyawan yang sedang onsite atau yang memiliki riwayat presensi hari ini
        $todayDate = now()->format('Y-m-d');
        $staffs = $staffQuery->where(function($q) use ($todayDate) {
            $q->where('is_active_onsite', true)
              ->orWhereHas('attendances', function($attQuery) use ($todayDate) {
                  $attQuery->whereDate('checked_at', $todayDate);
              });
        })->orderBy('is_active_onsite', 'desc')->orderBy('last_seen_at', 'desc')->get();

        // Map data keberadaan & rekap masuk/keluar karyawan
        $occupancyData = $staffs->map(function ($staff) use ($todayDate, $zones) {
            $lastCheckIn = Attendance::with('geofenceZone')
                ->where('staff_id', $staff->id)
                ->where('status', AttendanceStatus::CHECK_IN)
                ->whereDate('checked_at', $todayDate)
                ->latest('checked_at')
                ->first();

            $entryTime = $lastCheckIn?->checked_at ?? ($staff->is_active_onsite ? $staff->last_seen_at : null);
            
            // Jika karyawan masih berada di dalam ruangan, jam keluar dipastikan belum ada (null)
            $exitTime = null;
            if (!$staff->is_active_onsite) {
                $lastCheckOut = Attendance::where('staff_id', $staff->id)
                    ->where('status', AttendanceStatus::CHECK_OUT)
                    ->whereDate('checked_at', $todayDate)
                    ->latest('checked_at')
                    ->first();
                if ($lastCheckOut && (!$entryTime || $lastCheckOut->checked_at->gte($entryTime))) {
                    $exitTime = $lastCheckOut->checked_at;
                }
            }

            $duration = '-';
            if ($entryTime) {
                if ($staff->is_active_onsite) {
                    $duration = $entryTime->diffForHumans(null, true);
                } elseif ($exitTime) {
                    $duration = $entryTime->diffForHumans($exitTime, true);
                }
            }

            return [
                'staff' => $staff,
                'entry_time' => $entryTime,
                'exit_time' => $exitTime,
                'duration' => $duration,
                'is_onsite' => $staff->is_active_onsite,
                'zone' => $lastCheckIn?->geofenceZone ?? $zones->first(),
            ];
        });

        // Urutkan aktivitas presensi/scan terbaru (masuk atau keluar) ke posisi paling atas
        $occupancyData = $occupancyData->sortByDesc(function ($item) {
            return $item['exit_time'] ?? $item['entry_time'] ?? $item['staff']->last_seen_at;
        })->values();

        // Hitung kepadatan karyawan onsite PER ZONA/RUANGAN secara valid
        $zoneStats = $zones->map(function ($zone) use ($todayDate, $occupancyData) {
            // Hitung hanya karyawan yang saat ini aktif onsite (is_active_onsite = true) di zona ini
            $count = $occupancyData->filter(function ($item) use ($zone) {
                return $item['is_onsite'] && $item['zone']?->id == $zone->id;
            })->count();

            return [
                'zone' => $zone,
                'onsite_count' => $count,
            ];
        });

        $totalOnsite = OutsourcingStaff::activeOnsite()->count();
        $totalRegistered = OutsourcingStaff::registered()->count();
        $totalOffsite = max(0, $totalRegistered - $totalOnsite);

        return view('admin.occupancy.index', compact(
            'occupancyData',
            'zones',
            'zoneStats',
            'totalOnsite',
            'totalOffsite',
            'totalRegistered',
            'search',
            'zoneId'
        ));
    }

    /**
     * Tampilkan halaman Scanner Khusus Akses Ruangan (Masuk / Keluar).
     */
    public function showScan()
    {
        return view('admin.occupancy.scan');
    }

    /**
     * Proses Akses Ruangan (Scan Muka / QR) - Mencatat log masuk & keluar ke rekap presensi.
     */
    public function processScan(Request $request)
    {
        $methodInput = $request->input('method'); // 'FACE_RECOGNITION' or 'QR_CODE'
        $staffId = null;

        if ($methodInput === 'FACE_RECOGNITION') {
            $photoBase64 = $request->input('proof_photo');
            if (!$photoBase64) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto wajah tidak terdeteksi dari kamera.',
                ], 422);
            }

            $matchResult = $this->faceMatchingService->findBestMatch($photoBase64);
            if (!$matchResult['matched']) {
                return response()->json([
                    'success' => false,
                    'message' => $matchResult['message'] ?? 'Wajah tidak dikenali atau belum terdaftar dalam sistem.',
                ], 422);
            }

            $staffId = $matchResult['staff']->id;
        } elseif ($methodInput === 'QR_CODE') {
            $qrInput = trim($request->input('qr_token'));
            $staff = OutsourcingStaff::where('staff_code', $qrInput)->first();

            if ($staff) {
                $staffId = $staff->id;
            } else {
                $qrToken = $this->qrTokenService->validateToken($qrInput);
                if (!$qrToken) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code atau Kode Pegawai tidak valid.',
                    ], 422);
                }
                $staffId = $qrToken->staff_id;
            }
        }

        $staff = OutsourcingStaff::findOrFail($staffId);
        $methodEnum = $methodInput === 'FACE_RECOGNITION' ? \App\Enums\AttendanceMethod::FACE_RECOGNITION : \App\Enums\AttendanceMethod::QR_CODE;

        // Toggle status keberadaan di ruangan & simpan log ke rekap absensi
        if ($staff->is_active_onsite) {
            $staff->update([
                'is_active_onsite' => false,
                'last_seen_at' => now(),
            ]);

            // Catat log CHECK_OUT ke rekap absensi
            Attendance::create([
                'staff_id' => $staff->id,
                'status' => AttendanceStatus::CHECK_OUT,
                'method' => $methodEnum,
                'checked_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'action' => 'EXIT',
                'message' => "Akses Keluar Ruangan Berhasil!\nTerima kasih, {$staff->name}.",
                'staff' => [
                    'name' => $staff->name,
                    'staff_code' => $staff->staff_code,
                    'institution' => $staff->institution,
                    'status_label' => 'Keluar Ruangan',
                    'photo_profile' => $staff->photo_profile ? asset('storage/' . $staff->photo_profile) : null,
                ]
            ]);
        } else {
            $staff->update([
                'is_active_onsite' => true,
                'last_seen_at' => now(),
            ]);

            // Catat log CHECK_IN ke rekap absensi
            Attendance::create([
                'staff_id' => $staff->id,
                'status' => AttendanceStatus::CHECK_IN,
                'method' => $methodEnum,
                'checked_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'action' => 'ENTRY',
                'message' => "Akses Masuk Ruangan Berhasil!\nSelamat datang, {$staff->name}.",
                'staff' => [
                    'name' => $staff->name,
                    'staff_code' => $staff->staff_code,
                    'institution' => $staff->institution,
                    'status_label' => 'Di Dalam Ruangan',
                    'photo_profile' => $staff->photo_profile ? asset('storage/' . $staff->photo_profile) : null,
                ]
            ]);
        }
    }
}
