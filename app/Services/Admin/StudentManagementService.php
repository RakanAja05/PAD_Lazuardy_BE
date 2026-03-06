<?php

namespace App\Services\Admin;

use App\DTOs\ResponseDTO;
use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StudentManagementService
{
    public function index(): ResponseDTO
    {
        $results = Payment::query()
            ->with('order.user.student', 'order.package')
            ->where('status', PaymentStatusEnum::UPLOADED)
            ->orderBy('created_at', 'asc')
            ->paginate(9);

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil daftar pembayaran',
            'data' => $results,
        ], 200);
    }

    public function show(Payment $payment): ResponseDTO
    {
        $payment->load('order.user.student', 'order.package');
        $file = Storage::url($payment->proof_image_url);

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil detail pembayaran',
            'data' => [
                'detail' => $payment,
                'file' => $file,
            ],
        ], 200);
    }

    public function accept(Payment $payment): ResponseDTO
    {
        try {
            $payment->update([
                'status' => PaymentStatusEnum::VALIDATED,
            ]);

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Verifikasi pembayaran diterima',
                'data' => [],
            ], 200);
        } catch (Throwable $e) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gagal menerima verifikasi: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function reject(Payment $payment): ResponseDTO
    {
        try {
            $payment->update([
                'status' => PaymentStatusEnum::REJECTED,
            ]);

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Verifikasi pembayaran ditolak',
                'data' => [],
            ], 200);
        } catch (Throwable $e) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gagal menolak verifikasi: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
