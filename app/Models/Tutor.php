<?php

namespace App\Models;

use App\Enums\BadgeEnum;
use App\Enums\CourseModeEnum;
use App\Enums\TutorStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tutor extends Model
{
    /** @use HasFactory<\Database\Factories\TutorFactory> */
    use HasFactory;

    protected $table = 'tutors';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = 
    [
        'user_id',
        'education',
        'salary',
        'price',
        'description',
        'experience',
        'organization',
        'learning_method',
        'qualification',
        'course_mode',
        'status',
        'badge',
        'sanction_amount',
    ];
    
    protected $casts = [
        'qualification' => 'array',
        'organization' => 'array',
        'education' => 'array',
        'badge' => BadgeEnum::class,
        'course_mode' => CourseModeEnum::class,
        'status' => TutorStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'tutor_subjects', 'user_id', 'subject_id');
    }
}
