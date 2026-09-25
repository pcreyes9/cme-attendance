<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cme_attendance', function (Blueprint $table) {
            $table->id();

            $table->string('cme_year', 4);
            $table->string('cme_program_code', 10);

            $table->string('member_id_no', 4);

            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('mem_type', 2)->nullable();

            $table->time('time_in');
            $table->time('time_out')->nullable();

            $table->date('date');

            $table->string('remarks', 1000)->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'cme_year',
                    'cme_program_code',
                    'member_id_no',
                    'date'
                ],
                'uq_cme_attendance'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cme_attendance');
    }
};