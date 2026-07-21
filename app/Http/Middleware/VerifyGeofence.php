<?php

namespace App\Http\Middleware;

use App\Services\GeofenceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyGeofence
{
    public function __construct(
        private GeofenceService $geofenceService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat GPS diperlukan. Pastikan layanan lokasi aktif.',
            ], 422);
        }

        if (!$this->geofenceService->isInsideAnyZone((float) $lat, (float) $lng)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area kerja yang diizinkan. Presensi ditolak.',
                'geofence_violation' => true,
            ], 403);
        }

        return $next($request);
    }
}
