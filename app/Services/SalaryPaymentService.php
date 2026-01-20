<?php

namespace App\Services;

use App\Models\SalaryPayment;
use App\Models\Tutor;
use App\Mail\SalaryPaymentMail;
use Illuminate\Support\Facades\Mail;

class SalaryPaymentService
{
    /**
     * Confirm payment dengan invoice path dari storage lokal
     * File sudah di-upload ke storage/app/public sebelum memanggil service ini
     */
    public function confirmPaymentWithInvoice(Tutor $tutor, array $data)
    {
        $paidSalary = $tutor->salary;

        // Simpan record pembayaran dengan invoice_url (path lokal)
        $salaryPayment = SalaryPayment::create([
            'user_id' => $tutor->user_id,
            'amount' => $paidSalary,
            'invoice_url' => $data['invoice_url'], // Path relatif dari storage/app/public
            'payment_method' => $data['payment_method'],
            'note' => $data['note'] ?? null,
            'paid_at' => now(),
            'email_sent' => false,
        ]);

        // Set salary ke 0
        $tutor->salary = 0;
        $tutor->save();

        return $salaryPayment;
    }

    /**
     * Send email notifikasi pembayaran gaji
     */
    public function sendPaymentEmail(SalaryPayment $salaryPayment)
    {
        try {
            // Load relasi user untuk Mailable
            $salaryPayment->load('user');

            \Log::info('DEBUG: Sending email to ' . $salaryPayment->user->email);

            Mail::to($salaryPayment->user->email)
                ->send(new SalaryPaymentMail($salaryPayment));

            // Update flag email_sent
            $salaryPayment->email_sent = true;
            $salaryPayment->save();

            \Log::info('DEBUG: Email sent successfully to ' . $salaryPayment->user->email);
            return true;

        } catch (\Exception $e) {
            \Log::error('Error sending email', [
                'user_id' => $salaryPayment->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
