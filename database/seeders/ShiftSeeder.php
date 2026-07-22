<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Shift::create([
            'name' => 'Shift Pagi',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'color' => 'emerald',
            'description' => 'Shift kerja pagi (08.00 - 16.00)'
        ]);

        \App\Models\Shift::create([
            'name' => 'Shift Sore',
            'start_time' => '16:00:00',
            'end_time' => '00:00:00',
            'color' => 'amber',
            'description' => 'Shift kerja sore/malam (16.00 - 24.00)'
        ]);

        \App\Models\Shift::create([
            'name' => 'Shift Malam',
            'start_time' => '00:00:00',
            'end_time' => '08:00:00',
            'color' => 'blue',
            'description' => 'Shift kerja dini hari/malam (24.00 - 08.00)'
        ]);
    }
}
