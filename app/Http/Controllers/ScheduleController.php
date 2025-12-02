<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the student schedule resource.
     * 
     * @OA\Get(
     *     path="/api/student/schedule",
     *     tags={"Schedule"},
     *     summary="Get student schedule",
     *     description="Menampilkan jadwal belajar student dengan detail tutor, hari, dan waktu.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by date (optional)",
     *         @OA\Schema(type="string", format="date", example="2025-12-01")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="schedule_data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="tutor_name", type="string"),
     *                     @OA\Property(property="day", type="string"),
     *                     @OA\Property(property="time", type="string"),
     *                     @OA\Property(property="status", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
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

    /**
     * @OA\Get(
     *     path="/api/tutor/schedule",
     *     tags={"Schedule"},
     *     summary="Get tutor schedule",
     *     description="Menampilkan jadwal mengajar tutor dengan detail student yang diajar.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by date (optional)",
     *         @OA\Schema(type="string", format="date", example="2025-12-01")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="schedule_data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="student_name", type="string"),
     *                     @OA\Property(property="day", type="string"),
     *                     @OA\Property(property="time", type="string"),
     *                     @OA\Property(property="status", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
