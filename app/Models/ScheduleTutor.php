<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleTutor extends Model
{
    use HasFactory;
    public $timestamps = false; 

    protected $fillable = 
    [
        'tutor_id',
        'day',
        'time',
    ];
}
