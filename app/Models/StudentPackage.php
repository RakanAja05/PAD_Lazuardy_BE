<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_user_id');
    }
    
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
