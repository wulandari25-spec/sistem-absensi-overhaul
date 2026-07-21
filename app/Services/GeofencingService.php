<?php

namespace App\Services;

class GeofencingService
{
    /**
     * Menghitung jarak antara dua koordinat menggunakan rumus Haversine.
     * Return dalam satuan meter.
     */
    public static function hitungJarakMeter(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $radiusBumi = 6371000; // meter

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2 +
             cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radiusBumi * $c;
    }

    /**
     * Mengecek apakah koordinat pengguna berada di dalam radius geofencing unit.
     */
    public static function dalamRadius(float $latUser, float $lonUser, float $latUnit, float $lonUnit, int $radiusMeter): array
    {
        $jarak = self::hitungJarakMeter($latUser, $lonUser, $latUnit, $lonUnit);

        return [
            'valid' => $jarak <= $radiusMeter,
            'jarak_meter' => round($jarak, 2),
        ];
    }
}