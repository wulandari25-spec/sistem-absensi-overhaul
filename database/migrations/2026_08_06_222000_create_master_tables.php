<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
 
        Schema::create('master_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
 
        Schema::create('master_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
 
        // Seed from existing staff data
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
 
    public function down(): void
    {
        Schema::dropIfExists('master_positions');
        Schema::dropIfExists('master_departments');
        Schema::dropIfExists('master_institutions');
    }
};
