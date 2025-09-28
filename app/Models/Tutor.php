<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    /** @use HasFactory<\Database\Factories\TutorFactory> */
    use HasFactory;

    protected $fillable = 
    [
        'user_id',
        'education',
        'salary',
        'price',
        'description',
        'learning_mehod',
        'qualification',
        'status',
        'rank',
        'sanction_amount',
    ];
}
