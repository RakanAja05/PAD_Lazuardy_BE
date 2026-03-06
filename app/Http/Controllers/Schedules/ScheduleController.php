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

    public function indexStudent(Request $request)
    {
        $result = $this->scheduleService->indexStudent($request);

        return response()->json($result->payload, $result->code);
    }

    public function indexTutor(Request $request)
    {
        $result = $this->scheduleService->indexTutor($request);

        return response()->json($result->payload, $result->code);
    }
}
