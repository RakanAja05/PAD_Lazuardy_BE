<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the student schedule resource.
     */
    public function indexStudent(Request $request)
    {
        $user = $request->user()->load('takenSchedules.scheduleTutor.user'); // Merujuk ke siswa
        $takenSchedules = $user->takenSchedules;

        $tsData = [];
        foreach($takenSchedules as $takenSchedule)
        {
            $schedule = $takenSchedule->scheduleTutor;
            $tsData[] = [
                'tutor_name' => $schedule->user->name, // merujuk nama tutor
                'day' => $schedule->day,
                'time' => $schedule->time,
                'status' => $schedule->status,
            ];
        };

        return response()->json([
                'status' => 'success',
                'message' => 'Data jadwal berhasil terkirim',
                'schedule_data' => $tsData
            ], 200);
    }

    public function indexTutor(Request $request)
    {
        $user = $request->user()->load('schedules.takenSchedules.user'); // Merujuk ke tutor

        $data = $user->schedules->flatMap(function($schedule){
            return $schedule->takenSchedules->map(function($ts) use($schedule) {
                return [
                    'student_name' => $ts->user->name, // merujuk ke siswa
                    'day' => $schedule->day,
                    'time' => $schedule->time,
                    'status' => $ts->status,
                ];
            });
        });
        return response()->json([
                'status' => 'success',
                'message' => 'Data jadwal berhasil terkirim',
                'schedule_data' => $data,
            ], 200);
    }
}
