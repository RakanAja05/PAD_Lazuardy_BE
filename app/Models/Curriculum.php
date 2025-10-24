<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculums';
    public $timestamps = false; 

    protected $fillable = ['name'];

    public function students()
    {
        return $this->hasMany(Student::class, 'curriculum_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'curriculum_id');
    }

    
}
