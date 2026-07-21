<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outsourcing_staffs', function (Blueprint $table) {
            $table->id();
            $table->string('staff_code', 20)->unique()->comment('Kode unik pegawai, e.g. OS-0001');
            $table->string('name');
            $table->string('institution')->comment('Instansi/perusahaan asal');
            $table->string('department')->nullable()->comment('Unit kerja/departemen');
            $table->string('position')->nullable()->comment('Jabatan/posisi');
            $table->json('face_descriptor')->nullable()->comment('Float32Array 128-dimension face vector');
            $table->string('photo_profile')->nullable()->comment('Path foto profil');
            $table->string('phone', 20)->nullable();
            $table->string('id_number', 30)->nullable()->comment('NIK/No. KTP');
            $table->string('password')->nullable()->comment('Kata sandi login karyawan');
            $table->boolean('is_active_onsite')->default(false)->comment('Apakah saat ini berada di dalam area');
            $table->timestamp('last_seen_at')->nullable()->comment('Terakhir terdeteksi sistem');
            $table->boolean('is_registered')->default(true)->comment('Status registrasi aktif');
            $table->timestamps();

            $table->index('institution');
            $table->index('is_active_onsite');
            $table->index('is_registered');
            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outsourcing_staffs');
    }
};
