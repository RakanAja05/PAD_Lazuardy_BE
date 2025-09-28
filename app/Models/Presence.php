<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    /** @use HasFactory<\Database\Factories\PresenceFactory> */
    use HasFactory;
    protected $fillable = 
    [
        'taken_schedule_id',
        'tutor_id',
        'student_id',
        'evaluation',
        'report',
        'pbm_image_url',
    ];
}
