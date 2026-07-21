<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('geofence_zones', function (Blueprint $table) {
            $table->id();
            $table->string('zone_name')->comment('Nama area, e.g. Unit 1 PLTU');
            $table->string('zone_code', 20)->unique()->comment('Kode zona, e.g. ZONE-U1');
            $table->decimal('center_lat', 10, 8)->comment('Latitude titik pusat zona');
            $table->decimal('center_lng', 11, 8)->comment('Longitude titik pusat zona');
            $table->unsignedInteger('radius_meters')->default(500)->comment('Radius geofence dalam meter');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geofence_zones');
    }
};
