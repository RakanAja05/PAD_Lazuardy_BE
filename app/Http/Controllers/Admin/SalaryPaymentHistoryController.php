<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SalaryPaymentHistoryMail;

class SalaryPaymentHistoryController extends Controller
{
	/**
	 * @OA\Get(
	 *     path="/api/admin/tutor-salary/{userId}/payment-history",
	 *     tags={"Admin - Tutor Salary"},
	 *     summary="Get tutor payment history (all)",
	 *     description="Return all payment history records for a specific tutor without pagination",
	 *     security={{"sanctum":{}}},
	 *     @OA\Parameter(
	 *         name="userId",
	 *         in="path",
	 *         required=true,
	 *         description="Tutor user ID",
	 *         @OA\Schema(type="integer")
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Payment history retrieved successfully",
	 *         @OA\JsonContent(
	 *             @OA\Property(property="status", type="string", example="success"),
	 *             @OA\Property(property="data", type="array", items={
	 *                 @OA\Property(property="id", type="integer"),
	 *                 @OA\Property(property="amount", type="number"),
	 *                 @OA\Property(property="payment_method", type="string"),
	 *                 @OA\Property(property="note", type="string"),
	 *                 @OA\Property(property="paid_at", type="string", format="date-time"),
	 *                 @OA\Property(property="invoice_url", type="string")
	 *             })
	 *         )
	 *     ),
	 *     @OA\Response(response=401, description="Unauthenticated"),
	 *     @OA\Response(response=403, description="Unauthorized"),
	 *     @OA\Response(response=404, description="User not found")
	 * )
	 */
	public function getPaymentHistory($userId)
	{
		$user = User::findOrFail($userId);

		$payments = SalaryPayment::where('user_id', $userId)
			->orderBy('paid_at', 'desc')
			->get();

		return response()->json([
			'status' => 'success',
			'message' => 'Payment history retrieved successfully',
			'data' => $payments,
		], 200);
	}
}
