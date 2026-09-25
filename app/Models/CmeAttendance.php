<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmeAttendance extends Model
{
    protected $table = 'cme_attendance';

    protected $fillable = [
        'cme_year',
        'cme_program_code',
        'member_id_no',
        'first_name',
        'last_name',
        'middle_name',
        'mem_type',
        'time_in',
        'time_out',
        'date',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}