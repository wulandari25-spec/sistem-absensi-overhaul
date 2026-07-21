<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_instalasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit'); // contoh: Unit 1, Unit 2
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('radius_meter')->default(100); // radius geofencing
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_instalasis');
    }
};
