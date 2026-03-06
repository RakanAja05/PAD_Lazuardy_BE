<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\TutorSalaryService;
use Illuminate\Http\Request;

class TutorSalaryController extends Controller
{
    public function __construct(private readonly TutorSalaryService $tutorSalaryService)
    {
    }

    public function index(Request $request)
    {
        $result = $this->tutorSalaryService->index($request);

        return response()->json($result->payload, $result->code);
    }

    public function show($userId)
    {
        $result = $this->tutorSalaryService->show($userId);

        return response()->json($result->payload, $result->code);
    }

    public function confirmPayment(Request $request, $userId)
    {
        $result = $this->tutorSalaryService->confirmPayment($request, $userId);

        return response()->json($result->payload, $result->code);
    }

    public function confirmBatchPayment(Request $request)
    {
        $result = $this->tutorSalaryService->confirmBatchPayment($request);

        return response()->json($result->payload, $result->code);
    }

    public function confirmPaymentWithInvoice(Request $request, $userId)
    {
        $result = $this->tutorSalaryService->confirmPaymentWithInvoice($request, $userId);

        return response()->json($result->payload, $result->code);
    }

    public function getPendingPayment(Request $request)
    {
        $result = $this->tutorSalaryService->getPendingPayment($request);

        return response()->json($result->payload, $result->code);
    }

    public function getVerificationPending(Request $request)
    {
        $result = $this->tutorSalaryService->getVerificationPending($request);

        return response()->json($result->payload, $result->code);
    }

    public function getSalaryHistory($userId)
    {
        $result = $this->tutorSalaryService->getSalaryHistory($userId);

        return response()->json($result->payload, $result->code);
    }
}
