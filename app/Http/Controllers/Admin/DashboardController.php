<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
    ) {}

    public function index()
    {
        $stats = $this->attendanceService->getTodayStats();
        $recentLogs = $this->attendanceService->getRecentLogs(30);
        $flaggedRecords = $this->attendanceService->getFlaggedRecords(10);

        return view('admin.dashboard', compact('stats', 'recentLogs', 'flaggedRecords'));
    }

    public function getRealtimeData(): JsonResponse
    {
        return response()->json([
            'stats' => $this->attendanceService->getTodayStats(),
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    public function getActivityLog(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 30);
        $logs = $this->attendanceService->getRecentLogs((int) $limit);

        return response()->json([
            'logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'staff_name' => $log->staff->name ?? 'Unknown',
                    'staff_code' => $log->staff->staff_code ?? '-',
                    'institution' => $log->staff->institution ?? '-',
                    'status' => $log->status->value,
                    'status_label' => $log->status->label(),
                    'method' => $log->method->value,
                    'method_label' => $log->method->label(),
                    'method_icon' => $log->method->icon(),
                    'is_flagged' => $log->is_flagged,
                    'flag_reason' => $log->flag_reason,
                    'checked_at' => $log->checked_at->format('H:i:s'),
                    'checked_at_full' => $log->checked_at->format('d/m/Y H:i:s'),
                    'zone_name' => $log->geofenceZone->zone_name ?? '-',
                ];
            }),
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    public function getHourlyPopulation(): JsonResponse
    {
        return response()->json([
            'data' => $this->attendanceService->getHourlyPopulation(),
        ]);
    }
}