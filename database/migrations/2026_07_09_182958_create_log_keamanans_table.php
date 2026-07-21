<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_keamanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pekerja_outsourcing_id')->constrained('pekerja_outsourcings')->cascadeOnDelete();
            $table->enum('tipe_akses', ['masuk', 'keluar']);
            $table->enum('metode', ['face_recognition', 'qr_code']);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('jarak_meter', 8, 2)->nullable(); // hasil hitung geofencing
            $table->boolean('status_validasi')->default(false);
            $table->timestamp('waktu_akses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_keamanans');
    }
};
