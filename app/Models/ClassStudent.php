<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    /** @use HasFactory<\Database\Factories\ClassStudentFactory> */
    use HasFactory;
    public $timestamps = false; 

    protected $fillable = ['name'];
}
