<?php

namespace App\Http\Controllers\Schedules;

use App\Http\Controllers\Controller;
use App\Services\Schedules\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(private readonly ScheduleService $scheduleService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/student/schedule",
     *     tags={"Schedules"},
     *     summary="Student schedules",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Schedules", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function indexStudent(Request $request)
    {
        $result = $this->scheduleService->indexStudent($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/tutor/schedule",
     *     tags={"Schedules"},
     *     summary="Tutor schedules",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Schedules", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function indexTutor(Request $request)
    {
        $result = $this->scheduleService->indexTutor($request);

        return response()->json($result->payload, $result->code);
    }
}
