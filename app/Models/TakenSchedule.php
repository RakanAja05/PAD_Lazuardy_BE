<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakenSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\TakenScheduleFactory> */
    use HasFactory;
    public $timestamps = false; 

    protected $fillable = 
    [
        'student_id',
        'schedule_tutor_id',
        'subject_id',
        'date',
        'status',
    ];
}
