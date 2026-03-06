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

    /**
     * @OA\Get(
     *     path="/api/admin/student",
     *     tags={"Admin"},
     *     summary="List students",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Students", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function index()
    {
        $result = $this->studentManagementService->index();

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/student/{id}",
     *     tags={"Admin"},
     *     summary="Student detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detail", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function show(Payment $payment)
    {
        $result = $this->studentManagementService->show($payment);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/student/{id}/accept",
     *     tags={"Admin"},
     *     summary="Accept student payment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Accepted", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function accept(Payment $payment)
    {
        $result = $this->studentManagementService->accept($payment);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/student/{id}/reject",
     *     tags={"Admin"},
     *     summary="Reject student payment",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Rejected", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function reject(Payment $payment)
    {
        $result = $this->studentManagementService->reject($payment);

        return response()->json($result->payload, $result->code);
    }
}
