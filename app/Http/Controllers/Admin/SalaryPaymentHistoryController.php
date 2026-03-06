<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SalaryPaymentHistoryService;

class SalaryPaymentHistoryController extends Controller
{
    public function __construct(private readonly SalaryPaymentHistoryService $salaryPaymentHistoryService)
    {
    }

    public function getPaymentHistory($userId)
    {
        $result = $this->salaryPaymentHistoryService->getPaymentHistory($userId);

        return response()->json($result->payload, $result->code);
    }
}
