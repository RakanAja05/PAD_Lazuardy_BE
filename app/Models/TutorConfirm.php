<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorConfirm extends Model
{
    /** @use HasFactory<\Database\Factories\TutorConfirmFactory> */
    use HasFactory;

    protected $fillable = 
    [
        'student_user_id',
        'tutor_user_id',
        'reason',
        'status',
    ];
}
