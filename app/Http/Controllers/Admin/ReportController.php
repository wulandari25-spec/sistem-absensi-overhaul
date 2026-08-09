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
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        
        $dayNames = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];
        $daysList = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($year, $month, $d);
            $daysList[] = [
                'day' => $d,
                'day_name' => $dayNames[$date->dayOfWeekIso] ?? '',
                'is_weekend' => $date->isWeekend(),
                'date' => $date,
            ];
        }

        // Query staff with filters
        $staffsQuery = OutsourcingStaff::registered()->orderBy('name', 'asc');
        
        if ($search = $request->input('search')) {
            $staffsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%");
            });
        }

        if ($institution = $request->input('institution')) {
            $staffsQuery->where('institution', $institution);
        }

        $allStaffs = (clone $staffsQuery)->get();
        $staffs = $staffsQuery->paginate(15)->withQueryString();

        // Get attendances for this month and year
        $attendances = Attendance::whereYear('checked_at', $year)
            ->whereMonth('checked_at', $month)
            ->get()
            ->groupBy(function ($item) {
                return $item->staff_id . '_' . (int)$item->checked_at->format('j');
            });

        // Global stats for this month
        $totalRecords = Attendance::whereYear('checked_at', $year)
            ->whereMonth('checked_at', $month)
            ->count();
            
        $checkInsCount = Attendance::whereYear('checked_at', $year)
            ->whereMonth('checked_at', $month)
            ->where('status', \App\Enums\AttendanceStatus::CHECK_IN)
            ->count();

        $stats = [
            'total' => $totalRecords,
            'check_ins' => $checkInsCount,
            'total_staff' => OutsourcingStaff::registered()->count(),
            'month_name' => Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y'),
        ];

        $institutions = OutsourcingStaff::distinct()->whereNotNull('institution')->pluck('institution');

        return view('admin.reports.index', compact(
            'staffs',
            'allStaffs',
            'attendances',
            'daysList',
            'daysInMonth',
            'year',
            'month',
            'stats',
            'institutions'
        ));
    }

    public function exportCsv(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $staffsQuery = OutsourcingStaff::registered()->orderBy('name', 'asc');
        if ($search = $request->input('search')) {
            $staffsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%");
            });
        }
        if ($institution = $request->input('institution')) {
            $staffsQuery->where('institution', $institution);
        }
        $staffs = $staffsQuery->get();

        $attendances = Attendance::whereYear('checked_at', $year)
            ->whereMonth('checked_at', $month)
            ->get()
            ->groupBy(function ($item) {
                return $item->staff_id . '_' . (int)$item->checked_at->format('j');
            });

        $monthName = Carbon::createFromDate($year, $month, 1)->format('F_Y');
        $filename = "rekap_kehadiran_matrix_{$monthName}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $dayNames = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];

        $callback = function() use($staffs, $attendances, $daysInMonth, $year, $month, $dayNames) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            // Header 1: Tanggal
            $header1 = ['No', 'Kode Pegawai', 'Nama Lengkap', 'Vendor (Instansi)'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $header1[] = $d;
            }
            $header1[] = 'Total Hadir';
            $header1[] = 'Total Izin';
            $header1[] = 'Total Sakit';
            $header1[] = 'Total Alpa';
            fputcsv($file, $header1, ';');

            // Header 2: Hari
            $header2 = ['', '', '', ''];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::createFromDate($year, $month, $d);
                $header2[] = $dayNames[$date->dayOfWeekIso] ?? '';
            }
            $header2[] = '';
            $header2[] = '';
            $header2[] = '';
            $header2[] = '';
            fputcsv($file, $header2, ';');

            foreach ($staffs as $idx => $staff) {
                $row = [
                    $idx + 1,
                    $staff->staff_code,
                    $staff->name,
                    $staff->institution ?? '-'
                ];

                $hadirCount = 0;
                $izinCount = 0;
                $sakitCount = 0;
                $alpaCount = 0;

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $cellDate = Carbon::createFromDate($year, $month, $d);
                    $isWithinContract = $staff->isWithinContract($cellDate);
                    $key = $staff->id . '_' . $d;
                    $dayLogs = $attendances->get($key, collect());

                    if (!$isWithinContract) {
                        $row[] = '-';
                    } elseif ($dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_IN) || $dayLogs->contains('status', \App\Enums\AttendanceStatus::CHECK_OUT)) {
                        $row[] = 'H';
                        $hadirCount++;
                    } elseif ($dayLogs->contains('status', \App\Enums\AttendanceStatus::PERMIT)) {
                        $row[] = 'I';
                        $izinCount++;
                    } elseif ($dayLogs->contains('status', \App\Enums\AttendanceStatus::SICK)) {
                        $row[] = 'S';
                        $sakitCount++;
                    } else {
                        $row[] = 'A';
                        $alpaCount++;
                    }
                }

                $row[] = $hadirCount;
                $row[] = $izinCount;
                $row[] = $sakitCount;
                $row[] = $alpaCount;

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Verifikasi/Setujui atau Tolak presensi anomali.
     */
    public function verify(Request $request, Attendance $attendance)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya administrator yang diizinkan melakukan verifikasi.');
        }

        $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        $action = $request->input('action');

        if ($action === 'approve') {
            $attendance->update([
                'is_flagged' => false,
                'verified_by' => auth()->id(),
                'notes' => ($attendance->notes ? $attendance->notes . "\n" : "") . "Disetujui oleh admin " . auth()->user()->name . " pada " . now()->format('d-m-Y H:i')
            ]);
            $msg = 'Presensi berhasil disetujui (Aman).';
        } else {
            $attendance->update([
                'is_flagged' => true,
                'verified_by' => auth()->id(),
                'notes' => ($attendance->notes ? $attendance->notes . "\n" : "") . "Ditolak oleh admin " . auth()->user()->name . " pada " . now()->format('d-m-Y H:i')
            ]);
            $msg = 'Presensi berhasil ditolak (Tetap ditandai sebagai anomali).';
        }

        return back()->with('success', $msg);
    }
}
