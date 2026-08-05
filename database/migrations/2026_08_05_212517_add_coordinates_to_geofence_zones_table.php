<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('geofence_zones', function (Blueprint $table) {
            $table->json('coordinates')->nullable()->after('radius_meters')->comment('Array of polygon vertices [{"lat": y, "lng": x}, ...]');
            $table->unsignedInteger('radius_meters')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('geofence_zones', function (Blueprint $table) {
            $table->dropColumn('coordinates');
            $table->unsignedInteger('radius_meters')->nullable(false)->change();
        });
    }
};
