<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterTableSeeder extends Seeder
{
    public function run(): void
    {
        // Sync institutions
        $existingInstitutions = DB::table('outsourcing_staffs')
            ->whereNotNull('institution')
            ->where('institution', '!=', '')
            ->distinct()
            ->pluck('institution');

        foreach ($existingInstitutions as $inst) {
            DB::table('master_institutions')->insertOrIgnore([
                'name' => $inst,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sync departments
        $existingDepartments = DB::table('outsourcing_staffs')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        foreach ($existingDepartments as $dept) {
            DB::table('master_departments')->insertOrIgnore([
                'name' => $dept,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sync positions
        $existingPositions = DB::table('outsourcing_staffs')
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->pluck('position');

        foreach ($existingPositions as $pos) {
            DB::table('master_positions')->insertOrIgnore([
                'name' => $pos,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
