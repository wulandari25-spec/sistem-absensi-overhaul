<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')
                  ->constrained('outsourcing_staffs')
                  ->cascadeOnDelete();
            $table->string('token', 255)->unique()->comment('Encrypted unique token');
            $table->enum('purpose', ['check_in', 'check_out'])->default('check_in');
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at')->comment('Token expiry time');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['token', 'is_used']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_tokens');
    }
};
