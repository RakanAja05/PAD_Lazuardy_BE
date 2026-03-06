<?php

namespace App\Services\Schedules;

use App\DTOs\ResponseDTO;
use Illuminate\Http\Request;

class ScheduleService
{
    public function indexStudent(Request $request): ResponseDTO
    {
        $user = $request->user()->load('takenSchedules.scheduleTutor.user');
        $takenSchedules = $user->takenSchedules;

        $tsData = [];
        foreach ($takenSchedules as $takenSchedule) {
            $schedule = $takenSchedule->scheduleTutor;
            $tsData[] = [
                'tutor_name' => $schedule->user->name,
                'day' => $schedule->day,
                'time' => $schedule->time,
                'status' => $schedule->status,
            ];
        }

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Data jadwal berhasil terkirim',
            'data' => [
                'schedules' => $tsData,
            ],
        ], 200);
    }

    public function indexTutor(Request $request): ResponseDTO
    {
        $user = $request->user()->load('schedules.takenSchedules.user');

        $data = $user->schedules->flatMap(function ($schedule) {
            return $schedule->takenSchedules->map(function ($ts) use ($schedule) {
                return [
                    'student_name' => $ts->user->name,
                    'day' => $schedule->day,
                    'time' => $schedule->time,
                    'status' => $ts->status,
                ];
            });
        });

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Data jadwal berhasil terkirim',
            'data' => [
                'schedules' => $data,
            ],
        ], 200);
    }
}
