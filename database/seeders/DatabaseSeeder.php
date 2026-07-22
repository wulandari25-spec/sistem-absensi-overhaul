<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            GeofenceZoneSeeder::class,
            OutsourcingStaffSeeder::class,
            ShiftSeeder::class,
            AttendanceSeeder::class,
        ]);
    }
}