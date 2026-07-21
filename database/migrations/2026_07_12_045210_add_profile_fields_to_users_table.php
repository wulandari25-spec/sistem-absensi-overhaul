<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable();
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (! Schema::hasColumn('users', 'unit')) {
                $table->string('unit')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['photo', 'phone', 'unit'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};