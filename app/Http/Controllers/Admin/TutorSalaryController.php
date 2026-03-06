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

    /**
     * @OA\Get(
     *     path="/api/admin/tutor-salary",
     *     tags={"Admin"},
     *     summary="List tutor salaries",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Salaries", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index(Request $request)
    {
        $result = $this->tutorSalaryService->index($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/tutor-salary/{userId}",
     *     tags={"Admin"},
     *     summary="Tutor salary detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detail", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function show($userId)
    {
        $result = $this->tutorSalaryService->show($userId);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/tutor-salary/{userId}/confirm",
     *     tags={"Admin"},
     *     summary="Confirm tutor salary payment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(type="object")
    *     ),
     *     @OA\Response(response=200, description="Confirmed", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function confirmPayment(Request $request, $userId)
    {
        $result = $this->tutorSalaryService->confirmPayment($request, $userId);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/tutor-salary/confirm-batch",
     *     tags={"Admin"},
     *     summary="Confirm batch salary payments",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(type="object")
    *     ),
     *     @OA\Response(response=200, description="Batch confirmed", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function confirmBatchPayment(Request $request)
    {
        $result = $this->tutorSalaryService->confirmBatchPayment($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/tutor-salary/{userId}/confirm-with-invoice",
     *     tags={"Admin"},
     *     summary="Confirm salary payment with invoice",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(type="object")
    *     ),
     *     @OA\Response(response=200, description="Confirmed", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function confirmPaymentWithInvoice(Request $request, $userId)
    {
        $result = $this->tutorSalaryService->confirmPaymentWithInvoice($request, $userId);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/tutor-salary/pending-payment",
     *     tags={"Admin"},
     *     summary="List pending salary payments",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Pending", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function getPendingPayment(Request $request)
    {
        $result = $this->tutorSalaryService->getPendingPayment($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/tutor/verification-pending",
     *     tags={"Admin"},
     *     summary="List tutor verification pending for salary",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Pending verification", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function getVerificationPending(Request $request)
    {
        $result = $this->tutorSalaryService->getVerificationPending($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/tutor-salary/{userId}/history",
     *     tags={"Admin"},
     *     summary="Tutor salary history",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="History", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function getSalaryHistory($userId)
    {
        $result = $this->tutorSalaryService->getSalaryHistory($userId);

        return response()->json($result->payload, $result->code);
    }
}
