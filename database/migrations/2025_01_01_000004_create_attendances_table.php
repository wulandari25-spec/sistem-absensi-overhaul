<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')
                  ->constrained('outsourcing_staffs')
                  ->cascadeOnDelete();
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('geofence_zone_id')
                  ->nullable()
                  ->constrained('geofence_zones')
                  ->nullOnDelete();
            $table->enum('method', ['face_recognition', 'qr_code', 'manual'])
                  ->nullable()
                  ->comment('Metode presensi yang digunakan');
            $table->enum('status', ['check_in', 'check_out', 'permit', 'sick'])
                  ->comment('Jenis aktivitas presensi');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('proof_photo')->nullable()->comment('Path foto bukti saat presensi');
            $table->float('confidence_score')->nullable()->comment('Skor kecocokan wajah 0-1');
            $table->text('notes')->nullable();
            $table->boolean('is_flagged')->default(false)->comment('Ditandai jika ada anomali/akses tidak sah');
            $table->string('flag_reason')->nullable()->comment('Alasan penandaan');
            $table->string('device_info')->nullable()->comment('Info perangkat user-agent');
            $table->timestamp('checked_at')->comment('Waktu aktual presensi');
            $table->timestamps();

            $table->index('staff_id');
            $table->index('checked_at');
            $table->index('status');
            $table->index('method');
            $table->index('is_flagged');
            $table->index(['staff_id', 'status', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
