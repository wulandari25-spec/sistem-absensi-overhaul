<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\OutsourcingStaff;
use App\Models\GeofenceZone;
use App\Models\User;
use App\Enums\AttendanceMethod;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $staffs = OutsourcingStaff::all();
        $zone = GeofenceZone::first();
        $security = User::where('role', 'security')->first();

        // Seed logs for the past 3 days: July 20, 21, 22 of 2026
        $dates = [
            Carbon::create(2026, 7, 20),
            Carbon::create(2026, 7, 21),
            Carbon::create(2026, 7, 22),
        ];

        foreach ($staffs as $staff) {
            foreach ($dates as $date) {
                // Only seed if date falls within their contract period
                if (!$staff->isWithinContract($date)) {
                    continue;
                }

                // Randomize method
                $method = rand(0, 1) ? AttendanceMethod::FACE_RECOGNITION : AttendanceMethod::QR_CODE;
                
                // 1. Check In
                $checkInTime = (clone $date)->setTime(rand(7, 8), rand(0, 59), rand(0, 59));
                
                Attendance::create([
                    'staff_id' => $staff->id,
                    'verified_by' => $method === AttendanceMethod::FACE_RECOGNITION ? null : ($security->id ?? null),
                    'geofence_zone_id' => $zone?->id,
                    'method' => $method,
                    'status' => AttendanceStatus::CHECK_IN,
                    'latitude' => $zone ? $zone->center_lat + (rand(-100, 100) / 1000000) : -6.88450000,
                    'longitude' => $zone ? $zone->center_lng + (rand(-100, 100) / 1000000) : 109.67530000,
                    'confidence_score' => $method === AttendanceMethod::FACE_RECOGNITION ? (rand(75, 99) / 100) : null,
                    'is_flagged' => false,
                    'checked_at' => $checkInTime,
                ]);

                // 2. Check Out (mostly complete day, 85% chance)
                if (rand(1, 100) <= 85) {
                    $checkOutTime = (clone $date)->setTime(rand(16, 17), rand(0, 59), rand(0, 59));

                    Attendance::create([
                        'staff_id' => $staff->id,
                        'verified_by' => $method === AttendanceMethod::FACE_RECOGNITION ? null : ($security->id ?? null),
                        'geofence_zone_id' => $zone?->id,
                        'method' => $method,
                        'status' => AttendanceStatus::CHECK_OUT,
                        'latitude' => $zone ? $zone->center_lat + (rand(-100, 100) / 1000000) : -6.88450000,
                        'longitude' => $zone ? $zone->center_lng + (rand(-100, 100) / 1000000) : 109.67530000,
                        'confidence_score' => $method === AttendanceMethod::FACE_RECOGNITION ? (rand(75, 99) / 100) : null,
                        'is_flagged' => false,
                        'checked_at' => $checkOutTime,
                    ]);
                }
            }
        }
    }
}
