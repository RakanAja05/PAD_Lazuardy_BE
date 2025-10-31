<?php

namespace App\Models;

use App\Enums\ScheduleStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakenSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\TakenScheduleFactory> */
    use HasFactory;
    public $timestamps = false; 

    protected $fillable = 
    [
        'user_id',
        'schedule_tutor_id',
        'subject_id',
        'date',
        'status',
    ];

    protected $casts = [
        'status' => ScheduleStatusEnum::class,
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scheduleTutor()
    {
        return $this->belongsTo(ScheduleTutor::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
