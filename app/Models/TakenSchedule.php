<?php

namespace App\Models;

use App\Enums\TakenScheduleStatusEnum;
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

    protected function casts(): array
    {
        return [
            'status' => TakenScheduleStatusEnum::class,
            'date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
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
