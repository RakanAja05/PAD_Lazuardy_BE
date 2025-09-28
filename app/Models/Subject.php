<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;
    public $timestamps = false; 

    protected $fillable = 
    [
        'name',
        'icon_image_url',
        'class_id',
        'major_id',
        'curriculum_id',
    ];
}
