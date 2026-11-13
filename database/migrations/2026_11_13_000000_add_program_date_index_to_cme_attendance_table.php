<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cme_attendance', function (Blueprint $table) {
            $table->index(
                ['cme_year', 'cme_program_code', 'date'],
                'idx_cme_attendance_program_date'
            );
        });
    }

    public function down(): void
    {
        Schema::table('cme_attendance', function (Blueprint $table) {
            $table->dropIndex('idx_cme_attendance_program_date');
        });
    }
};
