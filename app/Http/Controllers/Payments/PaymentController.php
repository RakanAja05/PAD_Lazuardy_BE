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

    public function showPaymentPackage(Package $id)
    {
        $result = $this->paymentControllerService->showPaymentPackage($id);

        return response()->json($result->payload, $result->code);
    }

    public function storeOrderPackage(Request $request)
    {
        $result = $this->paymentControllerService->storeOrderPackage($request);

        return response()->json($result->payload, $result->code);
    }

    public function uploadPaymentFile(Request $request)
    {
        $result = $this->paymentControllerService->uploadPaymentFile($request);

        return response()->json($result->payload, $result->code);
    }

    public function showHistory(Request $request)
    {
        $result = $this->paymentControllerService->showHistory($request);

        return response()->json($result->payload, $result->code);
    }

    public function showDetail(Request $request)
    {
        $result = $this->paymentControllerService->showDetail($request);

        return response()->json($result->payload, $result->code);
    }
}
