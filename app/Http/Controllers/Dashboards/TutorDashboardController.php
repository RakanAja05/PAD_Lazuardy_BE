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

    public function index(Request $request)
    {
        $result = $this->tutorDashboardService->index($request);

        return response()->json($result->payload, $result->code);
    }

    public function summary(Request $request)
    {
        $result = $this->tutorDashboardService->summary($request);

        return response()->json($result->payload, $result->code);
    }
}
