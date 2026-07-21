<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('qr_tokens')) {
            Schema::create('qr_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pekerja_outsourcing_id')->constrained('pekerja_outsourcings')->cascadeOnDelete();
                $table->string('token')->unique(); // token terenkripsi
                $table->timestamp('expired_at');
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_tokens');
    }
};
