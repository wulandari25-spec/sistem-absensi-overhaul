<?php

namespace App\Services;

use App\Models\GeofenceZone;

class GeofenceService
{
    public function validatePosition(float $lat, float $lng): ?GeofenceZone
    {
        $zones = GeofenceZone::active()->get();

        foreach ($zones as $zone) {
            if ($zone->isWithinBounds($lat, $lng)) {
                return $zone;
            }
        }

        return null;
    }

    public function isInsideAnyZone(float $lat, float $lng): bool
    {
        return $this->validatePosition($lat, $lng) !== null;
    }

    public function getZonesWithDistance(float $lat, float $lng): array
    {
        return GeofenceZone::active()->get()->map(function ($zone) use ($lat, $lng) {
            return [
                'zone' => $zone,
                'distance' => $zone->calculateDistance($lat, $lng),
                'is_inside' => $zone->isWithinBounds($lat, $lng),
            ];
        })->toArray();
    }
}
