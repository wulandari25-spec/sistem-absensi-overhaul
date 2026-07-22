<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')
                  ->constrained('outsourcing_staffs')
                  ->cascadeOnDelete();
            $table->foreignId('shift_id')
                  ->constrained('shifts')
                  ->cascadeOnDelete();
            $table->date('schedule_date');
            $table->timestamps();

            $table->unique(['staff_id', 'schedule_date']);
            $table->index('schedule_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_schedules');
    }
};
