<?php

namespace App\Mail;

use App\Models\SalaryPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalaryPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SalaryPayment $salaryPayment
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Pembayaran Gaji - PAD Lazuardy',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.salary_payment',
            with: [
                'tutorName' => $this->salaryPayment->user->name,
                'amount' => $this->salaryPayment->amount,
                'paymentMethod' => $this->salaryPayment->payment_method,
                'paidAt' => $this->salaryPayment->paid_at->format('d F Y'),
                'note' => $this->salaryPayment->note,
                'invoiceUrl' => $this->salaryPayment->invoice_url,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->salaryPayment->invoice_url) {
            $filePath = storage_path('app/public/' . $this->salaryPayment->invoice_url);

            if (file_exists($filePath)) {
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $mimeType = match ($extension) {
                    'pdf' => 'application/pdf',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    default => 'application/octet-stream'
                };

                $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($filePath)
                    ->as('Invoice_' . $this->salaryPayment->user->name . '.' . $extension)
                    ->withMime($mimeType);
            }
        }

        return $attachments;
    }
}
