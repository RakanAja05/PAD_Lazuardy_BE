<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPackage extends Model
{
    /** @use HasFactory<\Database\Factories\StudentPackageFactory> */
    use HasFactory;

    protected $fillable = 
    [
        'package_id',
        'student_user_id',
        'subject_id',
        'tutor_user_id',
        'remaining_session',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function studentUser()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function tutorUser()
    {
        return $this->belongsTo(User::class, 'tutor_user_id');
    }
}
