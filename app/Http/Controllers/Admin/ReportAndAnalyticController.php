<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportAndAnalyticService;

class ReportAndAnalyticController extends Controller
{
    public function __construct(private readonly ReportAndAnalyticService $reportAndAnalyticService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/admin/analytic",
     *     tags={"Admin"},
     *     summary="Admin analytics",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Analytics", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index()
    {
        $result = $this->reportAndAnalyticService->index();

        return response()->json($result->payload, $result->code);
    }
}
