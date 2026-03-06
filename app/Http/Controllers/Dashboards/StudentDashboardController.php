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

    public function index(Request $request)
    {
        $result = $this->studentDashboardService->index($request);

        return response()->json($result->payload, $result->code);
    }

    public function getRecommendedTutors(Request $request)
    {
        $result = $this->studentDashboardService->getRecommendedTutors($request);

        return response()->json($result->payload, $result->code);
    }

    public function summary(Request $request)
    {
        $result = $this->studentDashboardService->summary($request);

        return response()->json($result->payload, $result->code);
    }
}
