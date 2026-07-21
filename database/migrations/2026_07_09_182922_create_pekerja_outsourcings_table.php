<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pekerja_outsourcings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('unit_instalasi_id')->constrained('unit_instalasis')->cascadeOnDelete();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('foto_referensi')->nullable(); // path foto wajah acuan
            $table->json('face_descriptor')->nullable();  // deskriptor 128-dimensi (array float)
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pekerja_outsourcings');
    }
};
