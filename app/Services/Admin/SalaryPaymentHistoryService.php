<?php

namespace App\Services\Admin;

use App\DTOs\ResponseDTO;
use App\Models\SalaryPayment;
use App\Models\User;

class SalaryPaymentHistoryService
{
    public function getPaymentHistory($userId): ResponseDTO
    {
        $user = User::findOrFail($userId);

        $payments = SalaryPayment::where('user_id', $userId)
            ->orderBy('paid_at', 'desc')
            ->get();

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Payment history retrieved successfully',
            'data' => $payments,
        ], 200);
    }
}
