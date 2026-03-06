<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Admin\StudentManagementService;

class StudentManagementController extends Controller
{
    public function __construct(private readonly StudentManagementService $studentManagementService)
    {
    }

    public function index()
    {
        $result = $this->studentManagementService->index();

        return response()->json($result->payload, $result->code);
    }

    public function show(Payment $payment)
    {
        $result = $this->studentManagementService->show($payment);

        return response()->json($result->payload, $result->code);
    }

    public function accept(Payment $payment)
    {
        $result = $this->studentManagementService->accept($payment);

        return response()->json($result->payload, $result->code);
    }

    public function reject(Payment $payment)
    {
        $result = $this->studentManagementService->reject($payment);

        return response()->json($result->payload, $result->code);
    }
}
