<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tutor extends Model
{
    /** @use HasFactory<\Database\Factories\TutorFactory> */
    use HasFactory;

    protected $table = 'tutors';
    protected $primaryKey = 'user_id';

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
        'rank',
        'sanction_amount',
    ];
    
    protected $casts = [
        'qualification' => 'array',
        'organization' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
}
