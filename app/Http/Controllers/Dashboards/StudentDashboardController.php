<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\StudentDashboardService;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function __construct(private readonly StudentDashboardService $studentDashboardService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/student",
     *     tags={"Dashboards"},
     *     summary="Student dashboard",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dashboard data", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->studentDashboardService->index($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/student/recommended-tutors",
     *     tags={"Dashboards"},
     *     summary="Recommended tutors",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Recommended tutors", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function getRecommendedTutors(Request $request)
    {
        $result = $this->studentDashboardService->getRecommendedTutors($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/student/summary",
     *     tags={"Dashboards"},
     *     summary="Student dashboard summary",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Summary data", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function summary(Request $request)
    {
        $result = $this->studentDashboardService->summary($request);

        return response()->json($result->payload, $result->code);
    }
}
