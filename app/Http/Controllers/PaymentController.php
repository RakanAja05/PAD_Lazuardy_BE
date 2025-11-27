<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use PhpParser\Node\Stmt\Catch_;
use Throwable;

class PaymentController extends Controller
{
    public function showPaymentPackage(Request $request)
    {
        $request->validate([
            'package_id' => ['required', 'integer', 'exists:packages,id'],
        ]);

        $package = Package::findOrFail($request->package_id);

        $data = [
            'name' => $package->name,
            'session' => $package->session,
            'price' => $package->price,
            'discount' => $package->discount,
            'description' => $package->description,
            'benefit' => $package->benefit,
            'image_url' => $package->image_url,
            'subject_amount' => $package->subject_amount,
        ];

        return response()->json($data, 200);
    }

    public function storeOrderPackage(Request $request)
    {
        $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'total_amount' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', new Enum(PaymentMethodEnum::class)],
        ]);
        $user = $request->user();
        DB::beginTransaction();
        try{
            $order = Order::create([
                'user_id' => $user->id,
                'package_id' => $request->package_id,
                'total_amount' => $request->total_amount,
                'status' => OrderStatusEnum::PENDING->value,
            ]);
    
            Payment::create([
                'order_id' => $order->id,
                'amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'status' => PaymentStatusEnum::PENDING->value,
            ]);
            
            DB::commit();
            return response()->json([
                'status' => "success",
                'message' => 'Berhasil membuat order',
            ], 200);
        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => "error",
                'message' => "Gagal membuat order: " . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadPaymentFile(Request $request)
    {
        $request->validate([
            'file_upload' => ['required', 'file', 'mimes:png,jpg, pdf, svg, webp'],
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        if($request->hasFile('file_upload')){
            $file = $request->file('file_upload');
                                                                            
            // Simpan ke storage
            $path = $file->store("uploads", 'public');

            $updatePayment = Payment::where('id', $request->order_id)
                    ->where('status', '!=', PaymentStatusEnum::VALIDATED->value)
                    ->updateOrFail([
                        'status' => PaymentStatusEnum::UPLOADED->value,
                        'proof_image_url' => $path,
                        'updated_at' => now(),
                    ]);

            if($updatePayment === 0){
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Gagal mengupload data file'
                ]);
            }

            return response()->json([
                'status' => "success",
                "message" => 'Bukti pembayaran berhasil terkirim',
            ], 200);
        }
        return response()->json([
            'status' => "failed",
            "message" => 'Tidak ditemukan file yang diunggah',
        ], 400);
    }

    public function showHistory(Request $request)
    {
        try {
            $user = $request->user();

            $payments = Payment::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('order.package')
            ->orderBy('created_at', 'desc')
            ->get();
            
            $historyData = [];

            foreach ($payments as $payment) {
                $order = $payment->order;
                $package = $order->package ?? null;

                if ($package) {
                    $historyData[] = [
                        'payment_id' => $payment->id,
                        'package_id' => $package->id,
                        'package_name' => $package->name,
                        'session' => $package->session,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'payment_status' => $payment->status,
                        'date_created' => $payment->created_at->format('Y-m-d'),
                        'time_created' => $payment->created_at->format('H:i:s'),
                    ];
                }
            }

            // 3. Mengembalikan array riwayat transaksi
            return response()->json([
                'status' => 'success',
                'data' => $historyData
            ], 200);

        } catch (Throwable $e) {
            // Gunakan respons 500 saat terjadi error internal
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses riwayat transaksi.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    public function showDetail(Request $request) {
        $request->validate([
            'payment_id' => ['required', 'integer', 'exists:payments,id']
        ]);

        $payment = Payment::with(['order.package'])
                            ->whereHas('order', function($query) use ($request){
                                $query->where('user_id', $request->user()->id);
                            })
                            ->where($request->payment_id)
                            ->firstOrFail();
        
        $package = $payment->order->package;
        $data = [
            'package_id' => $package->id,
            'package_name' => $package->name,
            'session' => $package->session,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'payment_status' => $payment->status,
            'date_created' => $payment->created_at->format('Y-m-d'),
            'time_created' => $payment->created_at->format('H:i:s'),
        ];

        return response()->json($data, 200);
    }

    
}
