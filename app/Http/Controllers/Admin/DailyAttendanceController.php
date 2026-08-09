<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutsourcingStaff;
use App\Models\Attendance;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $dateStr = $request->input('date', Carbon::today()->format('Y-m-d'));
        $date = Carbon::parse($dateStr);

        // Fetch all active/registered staffs
        $staffsQuery = OutsourcingStaff::registered();

        if ($search = $request->input('search')) {
            $staffsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_code', 'like', "%{$search}%");
            });
        }

        if ($institution = $request->input('institution')) {
            $staffsQuery->where('institution', $institution);
        }

        $staffs = $staffsQuery->orderBy('name')->get();

        // Fetch all attendance records for this date
        $attendances = Attendance::with('geofenceZone')
            ->whereDate('checked_at', $date)
            ->get()
            ->groupBy('staff_id');

        // Map status for each staff on this day
        $dailyData = $staffs->map(function ($staff) use ($attendances) {
            $staffAttendances = $attendances->get($staff->id) ?? collect();
            
            $checkIn = $staffAttendances->where('status', \App\Enums\AttendanceStatus::CHECK_IN)->first();
            $checkOut = $staffAttendances->where('status', \App\Enums\AttendanceStatus::CHECK_OUT)->last();
            
            // Check if there is permit or sick attendance
            $permit = $staffAttendances->where('status', \App\Enums\AttendanceStatus::PERMIT)->first();
            $sick = $staffAttendances->where('status', \App\Enums\AttendanceStatus::SICK)->first();

            $status = 'Alpa'; // Default
            if ($permit) {
                $status = 'Izin';
            } elseif ($sick) {
                $status = 'Sakit';
            } elseif ($checkIn) {
                $status = 'Hadir';
            }

            return [
                'staff' => $staff,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'permit' => $permit,
                'sick' => $sick,
                'status' => $status,
            ];
        });

        // Statistics for today
        $totalStaff = $staffs->count();
        $totalPresent = $dailyData->where('status', 'Hadir')->count();
        $totalPermit = $dailyData->where('status', 'Izin')->count();
        $totalSick = $dailyData->where('status', 'Sakit')->count();
        $totalAbsent = $dailyData->where('status', 'Alpa')->count();

        $stats = [
            'total' => $totalStaff,
            'present' => $totalPresent,
            'permit' => $totalPermit,
            'sick' => $totalSick,
            'absent' => $totalAbsent,
        ];

        $institutions = OutsourcingStaff::distinct()->whereNotNull('institution')->pluck('institution');
        $shifts = Shift::all();
        $allStaffs = OutsourcingStaff::registered()->orderBy('name')->get(); // for manual attendance dropdown

        return view('admin.daily-attendance.index', compact(
            'dailyData',
            'stats',
            'dateStr',
            'institutions',
            'shifts',
            'allStaffs'
        ));
    }
}
