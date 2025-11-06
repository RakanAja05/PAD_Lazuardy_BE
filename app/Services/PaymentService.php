<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    public function getData(Payment $query)
    {
        $data = [
            'payment_id' => $query->id,
            'amount' => $query->amount,
            'payment_method' => $query->payment_method,
            'payment_status' => $query->status,
            'date_created' => $query->created_at->format('Y-m-d'),
            'time_created' => $query->created_at->format('H:i:s'),
        ];

        return $data;
    }
}
