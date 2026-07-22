<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\OutsourcingStaff;
use App\Models\Shift;
use App\Models\StaffSchedule;
use Carbon\Carbon;

class ShiftScheduleController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $staffs = OutsourcingStaff::registered()->orderBy('name', 'asc')->get();
        $shifts = Shift::all();

        // Get schedules grouped by staff_id and day
        $schedules = StaffSchedule::whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->with('shift')
            ->get()
            ->groupBy(function ($item) {
                return $item->staff_id . '_' . $item->schedule_date->format('j');
            });

        return view('admin.schedules.index', compact(
            'staffs',
            'shifts',
            'schedules',
            'year',
            'month',
            'daysInMonth'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2025,2030',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');

        $staffs = OutsourcingStaff::registered()->get();
        $shifts = Shift::all();
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        if ($staffs->isEmpty() || $shifts->isEmpty()) {
            return back()->with('error', 'Karyawan atau Shift tidak ditemukan di database!');
        }

        // Clear existing schedules for this month to avoid duplicates
        StaffSchedule::whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->delete();

        // Round-robin distribution
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);

            foreach ($staffs as $index => $staff) {
                // Generate a rotating pattern of shift assignments
                $shiftIndex = ($index + $day) % $shifts->count();
                $shift = $shifts[$shiftIndex];

                StaffSchedule::create([
                    'staff_id' => $staff->id,
                    'shift_id' => $shift->id,
                    'schedule_date' => $date->format('Y-m-d'),
                ]);
            }
        }

        return redirect()->route('admin.schedules.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Jadwal shift bulanan otomatis berhasil digenerate!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2025,2030',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');

        StaffSchedule::whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->delete();

        return redirect()->route('admin.schedules.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Seluruh jadwal shift untuk bulan terpilih berhasil dikosongkan.');
    }
}
