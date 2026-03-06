<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SalaryPaymentHistoryService;

class SalaryPaymentHistoryController extends Controller
{
    public function __construct(private readonly SalaryPaymentHistoryService $salaryPaymentHistoryService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/admin/tutor-salary/{userId}/payment-history",
     *     tags={"Admin"},
     *     summary="Tutor salary payment history",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="History", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function getPaymentHistory($userId)
    {
        $result = $this->salaryPaymentHistoryService->getPaymentHistory($userId);

        return response()->json($result->payload, $result->code);
    }
}
