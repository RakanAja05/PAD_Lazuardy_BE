<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

        protected $fillable = 
    [
        'tutor_id',
        'student_id',
        'rate',
        'quality',
        'delivery',
        'attitude',
        'benefit',
        'review',
    ];
}
