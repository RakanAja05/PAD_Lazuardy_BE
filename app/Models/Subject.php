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
        'curriculum_id',
        'class_id',
        'icon_image_url',
    ];

    public function tutors(): BelongsToMany {
        return $this->belongsToMany(User::class, 'tutor_subjects', 'subject_id', 'user_id');
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
