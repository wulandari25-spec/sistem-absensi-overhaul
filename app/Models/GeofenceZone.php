<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeofenceZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_name', 'zone_code', 'center_lat', 'center_lng',
        'radius_meters', 'description', 'is_active', 'coordinates',
    ];

    protected function casts(): array
    {
        return [
            'center_lat' => 'decimal:8',
            'center_lng' => 'decimal:8',
            'radius_meters' => 'integer',
            'is_active' => 'boolean',
            'coordinates' => 'array',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isWithinBounds(float $lat, float $lng): bool
    {
        $points = $this->coordinates;

        // If coordinates polygon is empty, fallback to circular radius check
        if (empty($points) || count($points) < 3) {
            if ($this->radius_meters) {
                return $this->calculateDistance($lat, $lng) <= $this->radius_meters;
            }
            return false;
        }

        $inside = false;
        $numPoints = count($points);
        $j = $numPoints - 1;

        for ($i = 0; $i < $numPoints; $i++) {
            $pi = $points[$i];
            $pj = $points[$j];

            $piLat = (float)($pi['lat'] ?? $pi['latitude'] ?? 0);
            $piLng = (float)($pi['lng'] ?? $pi['longitude'] ?? 0);
            $pjLat = (float)($pj['lat'] ?? $pj['latitude'] ?? 0);
            $pjLng = (float)($pj['lng'] ?? $pj['longitude'] ?? 0);

            if (
                (($piLat > $lat) !== ($pjLat > $lat)) &&
                ($lng < ($pjLng - $piLng) * ($lat - $piLat) / ($pjLat - $piLat + 0.00000001) + $piLng)
            ) {
                $inside = !$inside;
            }
            $j = $i;
        }

        return $inside;
    }

    public function calculateDistance(float $lat, float $lng): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad((float) $this->center_lat);
        $lngFrom = deg2rad((float) $this->center_lng);
        $latTo = deg2rad($lat);
        $lngTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2 +
             cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function getActiveStaffCountAttribute(): int
    {
        $activeStaffs = OutsourcingStaff::where('is_active_onsite', true)->get();
        
        $count = 0;
        foreach ($activeStaffs as $staff) {
            $latest = $staff->attendances()->latest('checked_at')->first();
            if ($latest && $latest->geofence_zone_id == $this->id && $latest->status->value === 'check_in') {
                $count++;
            }
        }
        return $count;
    }
}
