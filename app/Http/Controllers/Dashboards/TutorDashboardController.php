<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\TutorDashboardService;
use Illuminate\Http\Request;

class TutorDashboardController extends Controller
{
    public function __construct(private readonly TutorDashboardService $tutorDashboardService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/tutor",
     *     tags={"Dashboards"},
     *     summary="Tutor dashboard",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dashboard data", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->tutorDashboardService->index($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/tutor/summary",
     *     tags={"Dashboards"},
     *     summary="Tutor dashboard summary",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Summary data", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function summary(Request $request)
    {
        $result = $this->tutorDashboardService->summary($request);

        return response()->json($result->payload, $result->code);
    }
}
