<?php

namespace App\Listeners;

use App\Events\PaymentConfirmedEvent;
use App\Models\User;
use App\Notifications\PaymentConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentConfirmedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentConfirmedEvent $event): void
    {
        // Dapatkan student dari payment -> order -> student
        $payment = $event->payment;
        $order = $payment->order;
        $student = $order->student;

        // Kirim notifikasi ke student (Flutter & Vue)
        if ($student) {
            $student->notify(new PaymentConfirmedNotification($payment));
        }
    }
}
