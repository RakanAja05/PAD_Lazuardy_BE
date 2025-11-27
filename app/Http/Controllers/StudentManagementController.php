<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Termwind\Components\Raw;
use Throwable;

class StudentManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = Payment::query()
            ->with('order.user.student', 'order.package')
            ->where('status', PaymentStatusEnum::UPLOADED)
            ->orderBy('created_at', 'asc')
            ->paginate(9);
        
        return response()->json($results, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load('order.user.student', 'order.package'); 

        $file = Storage::url($payment->proof_image_url);

        return response()->json([
            'detail' => $payment,
            'file' => $file
        ], 200);
    }

    public function accept(Payment $payment)
    {
        try{
            $payment->update([
                'status' => PaymentStatusEnum::VALIDATED,
            ]);
            return response()->json([
                'status' => 'success'
            ], 200);
        } catch(Throwable $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menerima verifikasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Payment $payment)
    {
        try{
            $payment->update([
                'status' => PaymentStatusEnum::REJECTED,
            ]);
            return response()->json([
                'status' => 'success'
            ], 200);
        } catch(Throwable $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menolak verifikasi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
