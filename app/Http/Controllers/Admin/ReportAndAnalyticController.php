<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportAndAnalyticService;

class ReportAndAnalyticController extends Controller
{
    public function __construct(private readonly ReportAndAnalyticService $reportAndAnalyticService)
    {
    }

    public function index()
    {
        $result = $this->reportAndAnalyticService->index();

        return response()->json($result->payload, $result->code);
    }
}
