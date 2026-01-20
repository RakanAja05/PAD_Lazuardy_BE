<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 5px 5px; }
        .detail-item { margin: 15px 0; padding: 10px; background: white; border-left: 4px solid #007bff; }
        .label { font-weight: bold; color: #007bff; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pemberitahuan Pembayaran Gaji</h1>
        </div>
        <div class="content">
            <p>Halo {{ $tutorName }},</p>

            <p>Kami dengan senang hati menginformasikan bahwa gaji Anda untuk periode ini telah kami proses dan bayarkan.</p>

            <h2 style="color: #007bff;">Detail Pembayaran Gaji</h2>

            <div class="detail-item">
                <span class="label">Nama:</span> {{ $tutorName }}
            </div>

            <div class="detail-item">
                <span class="label">Jumlah:</span> Rp {{ number_format($amount, 0, ',', '.') }}
            </div>

            <div class="detail-item">
                <span class="label">Metode Pembayaran:</span> {{ ucfirst($paymentMethod) }}
            </div>

            <div class="detail-item">
                <span class="label">Tanggal Pembayaran:</span> {{ $paidAt }}
            </div>

            @if($note)
            <div class="detail-item">
                <span class="label">Catatan:</span> {{ $note }}
            </div>
            @endif

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

            <p>Mohon simpan email ini sebagai bukti pembayaran. Invoice pembayaran telah kami lampirkan dalam email ini.</p>

            <p>Jika Anda memiliki pertanyaan atau menemukan ketidaksesuaian, silakan hubungi tim admin kami.</p>

            <p style="font-weight: bold; color: #28a745;">Terima kasih atas dedikasi Anda sebagai tutor di PAD Lazuardy!</p>

            <div style="margin-top: 20px; text-align: center;">
                <a href="{{ config('app.url') }}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Lihat Dashboard</a>
            </div>

            <div class="footer">
                <p style="margin: 0;">Salam,<br><strong>Tim Lazuardy</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
