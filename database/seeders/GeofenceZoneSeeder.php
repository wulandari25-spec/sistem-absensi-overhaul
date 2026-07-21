<?php

namespace Database\Seeders;

use App\Models\GeofenceZone;
use Illuminate\Database\Seeder;

class GeofenceZoneSeeder extends Seeder
{
    public function run(): void
    {
        GeofenceZone::firstOrCreate(
            ['zone_code' => 'ZONE-U1'],
            [
                'zone_name' => 'PLTU Unit 1 - Area Utama',
                'center_lat' => -6.88450000,
                'center_lng' => 109.67530000,
                'radius_meters' => 500,
                'description' => 'Area kerja utama Unit 1 Pembangkit Listrik',
                'is_active' => true,
            ]
        );

        GeofenceZone::firstOrCreate(
            ['zone_code' => 'ZONE-U2'],
            [
                'zone_name' => 'PLTU Unit 2 - Area Utama',
                'center_lat' => -6.88550000,
                'center_lng' => 109.67630000,
                'radius_meters' => 500,
                'description' => 'Area kerja utama Unit 2 Pembangkit Listrik',
                'is_active' => true,
            ]
        );

        GeofenceZone::firstOrCreate(
            ['zone_code' => 'ZONE-WG'],
            [
                'zone_name' => 'Workshop & Gudang',
                'center_lat' => -6.88350000,
                'center_lng' => 109.67430000,
                'radius_meters' => 300,
                'description' => 'Area workshop dan gudang peralatan overhaul',
                'is_active' => true,
            ]
        );
    }
}
