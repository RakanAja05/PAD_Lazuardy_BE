<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryPayment extends Model
{
    /** @use HasFactory<\Database\Factories\SalaryPaymentFactory> */
    use HasFactory;

    protected $table = 'salary_payments';

    protected $fillable = [
        'user_id',
        'amount',
        'invoice_url',
        'payment_method',
        'note',
        'paid_at',
        'email_sent',
        'email_sent_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'email_sent' => 'boolean',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class, 'user_id', 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
