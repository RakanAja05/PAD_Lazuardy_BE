<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function tutors(): BelongsToMany {
        return $this->belongsToMany(User::class, 'tutor_subjects', 'subject_id', 'user_id');
    }
}
