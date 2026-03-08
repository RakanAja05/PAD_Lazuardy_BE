<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Services\Payments\PaymentControllerService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentControllerService $paymentControllerService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/package/order/{id}",
     *     tags={"Payments"},
     *     summary="Get package info for ordering",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Package detail", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function showPaymentPackage(Package $id)
    {
        $result = $this->paymentControllerService->showPaymentPackage($id);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/package/order",
     *     tags={"Payments"},
     *     summary="Create order",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"package_id","total_amount","payment_method"},
     *             @OA\Property(property="package_id", type="integer"),
     *             @OA\Property(property="total_amount", type="integer"),
    *             @OA\Property(property="payment_method", type="string", enum={"mandiri","bni","bri","bpr","bpd","qris"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Order created", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function storeOrderPackage(Request $request)
    {
        $result = $this->paymentControllerService->storeOrderPackage($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Post(
     *     path="/api/package/payment",
     *     tags={"Payments"},
     *     summary="Upload payment proof",
     *     security={{"bearerAuth":{}}},
    *     @OA\RequestBody(
     *         required=true,
     *         content={
    *             @OA\MediaType(
    *                 mediaType="multipart/form-data",
    *                 @OA\Schema(
    *                     required={"file_upload","order_id"},
    *                     @OA\Property(property="file_upload", type="string", format="binary"),
    *                     @OA\Property(property="order_id", type="integer")
    *                 )
    *             )
     *         }
     *     ),
     *     @OA\Response(response=200, description="Payment uploaded", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function uploadPaymentFile(Request $request)
    {
        $result = $this->paymentControllerService->uploadPaymentFile($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/payment/history",
     *     tags={"Payments"},
     *     summary="Payment history",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="History", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function showHistory(Request $request)
    {
        $result = $this->paymentControllerService->showHistory($request);

        return response()->json($result->payload, $result->code);
    }

    /**
     * @OA\Get(
     *     path="/api/payment/history/detail",
     *     tags={"Payments"},
     *     summary="Payment detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="payment_id", in="query", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detail", @OA\JsonContent(ref="#/components/schemas/StandardSuccess"))
     * )
     */
    public function showDetail(Request $request)
    {
        $result = $this->paymentControllerService->showDetail($request);

        return response()->json($result->payload, $result->code);
    }
}
