<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GeofenceZone;
use App\Models\OutsourcingStaff;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan presensi.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['staff', 'geofenceZone']);

        // Default filters
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        if ($selectedMonth && $selectedYear) {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        }

        // Apply Date Range
        $query->whereBetween('checked_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);

        // Filter: Search Name / Code
        if ($search = $request->input('search')) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%");
            });
        }

        // Filter: Vendor (Institution)
        if ($institution = $request->input('institution')) {
            $query->whereHas('staff', function ($q) use ($institution) {
                $q->where('institution', $institution);
            });
        }

        // Filter: Geofence Zone
        if ($zoneId = $request->input('geofence_zone_id')) {
            $query->where('geofence_zone_id', $zoneId);
        }

        // Filter: Status (Check-in / Check-out)
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter: Method (Face / QR)
        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }

        // Filter: Flagged (Suspicious)
        if ($request->has('is_flagged') && $request->input('is_flagged') !== '') {
            $query->where('is_flagged', $request->boolean('is_flagged'));
        }

        // Clone query for stats calculation before pagination
        $statsQuery = clone $query;
        $allRecords = $statsQuery->get();

        $stats = [
            'total' => $allRecords->count(),
            'check_ins' => $allRecords->where('status', \App\Enums\AttendanceStatus::CHECK_IN)->count(),
            'check_outs' => $allRecords->where('status', \App\Enums\AttendanceStatus::CHECK_OUT)->count(),
            'flagged' => $allRecords->where('is_flagged', true)->count(),
            'face' => $allRecords->where('method', \App\Enums\AttendanceMethod::FACE_RECOGNITION)->count(),
            'qr' => $allRecords->where('method', \App\Enums\AttendanceMethod::QR_CODE)->count(),
        ];

        $reportType = $request->input('report_type', 'log');
        $dailySummary = null;
        $attendances = null;

        if ($reportType === 'daily') {
            // Get all records matching filters for grouping
            $records = $query->orderBy('checked_at', 'asc')->get();

            $grouped = $records->groupBy(function ($item) {
                return $item->staff_id . '_' . $item->checked_at->format('Y-m-d');
            });

            $dailySummaryCollection = $grouped->map(function ($items) {
                $first = $items->first();
                $checkIn = $items->where('status', \App\Enums\AttendanceStatus::CHECK_IN)->first();
                $checkOut = $items->where('status', \App\Enums\AttendanceStatus::CHECK_OUT)->last();

                $duration = '-';
                $overtime = '-';
                if ($checkIn && $checkOut) {
                    $diff = $checkIn->checked_at->diff($checkOut->checked_at);
                    $duration = $diff->format('%h Jam %i Menit');
                    
                    $totalHours = $diff->h + ($diff->days * 24);
                    $totalMinutes = $diff->i;
                    
                    if ($totalHours >= 8) {
                        $overtimeHours = $totalHours - 8;
                        if ($overtimeHours > 0 || $totalMinutes > 0) {
                            $overtime = $overtimeHours . ' Jam ' . $totalMinutes . ' Menit';
                        }
                    }
                }

                return [
                    'date' => $first->checked_at->format('Y-m-d'),
                    'raw_date' => $first->checked_at,
                    'staff' => $first->staff,
                    'check_in' => $checkIn ? $checkIn->checked_at->format('H:i:s') : '-',
                    'check_out' => $checkOut ? $checkOut->checked_at->format('H:i:s') : '-',
                    'duration' => $duration,
                    'overtime' => $overtime,
                    'is_flagged' => $items->contains('is_flagged', true),
                ];
            })->sortByDesc('date');

            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 15;
            $currentItems = $dailySummaryCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $dailySummary = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $dailySummaryCollection->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
            );
            $dailySummary->withQueryString();
        } else {
            // Paginate records
            $attendances = $query->latest('checked_at')->paginate(15)->withQueryString();
        }

        // Get filter options
        $institutions = OutsourcingStaff::distinct()->whereNotNull('institution')->pluck('institution');
        $zones = GeofenceZone::active()->get();

        return view('admin.reports.index', compact(
            'attendances',
            'dailySummary',
            'reportType',
            'institutions',
            'zones',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    public function exportCsv(Request $request)
    {
        $query = Attendance::with(['staff', 'geofenceZone']);

        // Default filters
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        if ($selectedMonth && $selectedYear) {
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        }

        // Apply Date Range
        $query->whereBetween('checked_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);

        // Filter: Search Name / Code
        if ($search = $request->input('search')) {
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%");
            });
        }

        // Filter: Vendor (Institution)
        if ($institution = $request->input('institution')) {
            $query->whereHas('staff', function ($q) use ($institution) {
                $q->where('institution', $institution);
            });
        }

        // Filter: Geofence Zone
        if ($zoneId = $request->input('geofence_zone_id')) {
            $query->where('geofence_zone_id', $zoneId);
        }

        // Filter: Status (Check-in / Check-out)
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter: Method (Face / QR)
        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }

        // Filter: Flagged (Suspicious)
        if ($request->has('is_flagged') && $request->input('is_flagged') !== '') {
            $query->where('is_flagged', $request->boolean('is_flagged'));
        }

        $records = $query->latest('checked_at')->get();
        $reportType = $request->input('report_type', 'log');

        if ($reportType === 'daily') {
            $filename = "rekap_kehadiran_harian_" . Carbon::parse($startDate)->format('Ymd') . "_to_" . Carbon::parse($endDate)->format('Ymd') . ".csv";

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['Tanggal', 'Kode Staf', 'Nama Karyawan', 'Vendor (Instansi)', 'Jam Masuk', 'Jam Pulang', 'Durasi Kerja', 'Lembur', 'Status Anomali'];

            $grouped = $records->sortBy('checked_at')->groupBy(function ($item) {
                return $item->staff_id . '_' . $item->checked_at->format('Y-m-d');
            });

            $callback = function() use($grouped, $columns) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 for Excel
                fputcsv($file, $columns, ';');

                foreach ($grouped as $items) {
                    $first = $items->first();
                    $checkIn = $items->where('status', \App\Enums\AttendanceStatus::CHECK_IN)->first();
                    $checkOut = $items->where('status', \App\Enums\AttendanceStatus::CHECK_OUT)->last();

                    $duration = '-';
                    $overtime = '-';
                    if ($checkIn && $checkOut) {
                        $diff = $checkIn->checked_at->diff($checkOut->checked_at);
                        $duration = $diff->format('%h Jam %i Menit');
                        
                        $totalHours = $diff->h + ($diff->days * 24);
                        $totalMinutes = $diff->i;
                        
                        if ($totalHours >= 8) {
                            $overtimeHours = $totalHours - 8;
                            if ($overtimeHours > 0 || $totalMinutes > 0) {
                                $overtime = $overtimeHours . ' Jam ' . $totalMinutes . ' Menit';
                            }
                        }
                    }

                    $hasFlag = $items->contains('is_flagged', true);

                    fputcsv($file, [
                        $first->checked_at->format('d/m/Y'),
                        $first->staff->staff_code ?? '-',
                        $first->staff->name ?? '-',
                        $first->staff->institution ?? '-',
                        $checkIn ? $checkIn->checked_at->format('H:i:s') : '-',
                        $checkOut ? $checkOut->checked_at->format('H:i:s') : '-',
                        $duration,
                        $overtime,
                        $hasFlag ? 'Mencurigakan' : 'Aman'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $filename = "laporan_kehadiran_" . Carbon::parse($startDate)->format('Ymd') . "_to_" . Carbon::parse($endDate)->format('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal', 'Waktu', 'Kode Staf', 'Nama Karyawan', 'Vendor (Instansi)', 'Zona Kerja', 'Metode Verifikasi', 'Status', 'Latitude', 'Longitude', 'Status Anomali'];

        $callback = function() use($records, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 for Excel
            fputcsv($file, $columns, ';');

            foreach ($records as $row) {
                fputcsv($file, [
                    $row->checked_at->format('d/m/Y'),
                    $row->checked_at->format('H:i:s'),
                    $row->staff->staff_code ?? '-',
                    $row->staff->name ?? '-',
                    $row->staff->institution ?? '-',
                    $row->geofenceZone->zone_name ?? 'Luar Geofence',
                    $row->method ? $row->method->label() : '-',
                    $row->status ? $row->status->label() : '-',
                    $row->latitude ?? '-',
                    $row->longitude ?? '-',
                    $row->is_flagged ? 'Mencurigakan' : 'Aman'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
