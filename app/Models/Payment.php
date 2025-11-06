<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = 
    [
        'order_id',
        'total_amount',
        'proof_image_url',
        'date',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethodEnum::class,
            'status' => PaymentStatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo 
    {
        return $this->belongsTo(Order::class);
    }
}
