<?php

namespace App\Services\Admin;

use App\DTOs\ResponseDTO;
use App\Models\SalaryPayment;
use App\Models\Tutor;
use App\Services\SalaryPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorSalaryService
{
    public function index(Request $request): ResponseDTO
    {
        $query = Tutor::with('user:id,name,email')
            ->where('status', 'active');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('min_salary')) {
            $query->where('salary', '>=', $request->min_salary);
        }

        $tutors = $query->get();

        $data = $tutors->map(function ($tutor) {
            return [
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'salary' => $tutor->salary,
                'bank' => $tutor->bank,
                'rekening' => $tutor->rekening,
            ];
        });

        $totalSalary = $tutors->sum('salary');

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil data gaji tutor',
            'data' => [
                'tutors' => $data,
                'total_salary' => $totalSalary,
            ],
        ], 200);
    }

    public function show($userId): ResponseDTO
    {
        $tutor = Tutor::with('user:id,name,email')
            ->where('user_id', $userId)
            ->first();

        if (!$tutor) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan',
                'errors' => [
                    'user_id' => $userId,
                ],
            ], 404);
        }

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil detail gaji tutor',
            'data' => [
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'salary' => $tutor->salary,
                'bank' => $tutor->bank,
                'rekening' => $tutor->rekening,
                'sanction_amount' => $tutor->sanction_amount,
            ],
        ], 200);
    }

    public function confirmPayment(Request $request, $userId): ResponseDTO
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $tutor = Tutor::with('user:id,name,email')
            ->where('user_id', $userId)
            ->first();

        if (!$tutor) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan',
                'errors' => [
                    'user_id' => $userId,
                ],
            ], 404);
        }

        if ($tutor->salary == 0) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gaji tutor sudah 0, tidak ada yang perlu dikonfirmasi',
                'errors' => [
                    'salary' => $tutor->salary,
                ],
            ], 400);
        }

        DB::beginTransaction();
        try {
            $paidSalary = $tutor->salary;
            $tutor->salary = 0;
            $tutor->save();

            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Gaji tutor berhasil dikonfirmasi dan direset',
                'data' => [
                    'user_id' => $tutor->user_id,
                    'name' => $tutor->user->name,
                    'paid_salary' => $paidSalary,
                    'current_salary' => $tutor->salary,
                    'note' => $request->note,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat konfirmasi pembayaran: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function confirmBatchPayment(Request $request): ResponseDTO
    {
        $tutorIds = $request->input('tutor_ids');

        if (is_string($tutorIds)) {
            $decoded = json_decode($tutorIds, true);
            if (is_array($decoded)) {
                $tutorIds = $decoded;
            }
        }

        if (!is_array($tutorIds)) {
            $tutorIds = [];
        }

        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        if (empty($tutorIds)) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'tutor_ids harus disediakan dan tidak boleh kosong',
                'errors' => [
                    'tutor_ids' => $tutorIds,
                ],
            ], 422);
        }

        $existingTutors = Tutor::whereIn('user_id', $tutorIds)->pluck('user_id')->toArray();
        $missingTutors = array_diff($tutorIds, $existingTutors);

        if (!empty($missingTutors)) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Tutor dengan ID: ' . implode(', ', $missingTutors) . ' tidak ditemukan',
                'errors' => [
                    'missing_tutors' => $missingTutors,
                ],
            ], 422);
        }

        DB::beginTransaction();
        try {
            $tutors = Tutor::with('user:id,name,email')
                ->whereIn('user_id', $tutorIds)
                ->where('salary', '>', 0)
                ->get();

            if ($tutors->isEmpty()) {
                return new ResponseDTO([
                    'status' => 'error',
                    'message' => 'Tidak ada tutor dengan gaji > 0 untuk dikonfirmasi',
                    'errors' => [
                        'tutor_ids' => $tutorIds,
                    ],
                ], 400);
            }

            $totalPaid = 0;
            $tutorDetails = [];

            foreach ($tutors as $tutor) {
                $paidSalary = $tutor->salary;
                $totalPaid += $paidSalary;

                $tutorDetails[] = [
                    'user_id' => $tutor->user_id,
                    'name' => $tutor->user->name,
                    'paid_salary' => $paidSalary,
                ];

                $tutor->salary = 0;
                $tutor->save();
            }

            DB::commit();

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Pembayaran gaji batch berhasil dikonfirmasi',
                'data' => [
                    'total_tutors' => count($tutorDetails),
                    'total_paid' => $totalPaid,
                    'tutors' => $tutorDetails,
                    'note' => $request->note,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat konfirmasi batch: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function confirmPaymentWithInvoice(Request $request, $userId): ResponseDTO
    {
        $request->validate([
            'invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'payment_method' => 'required|string|in:transfer,check,e-wallet',
            'note' => 'nullable|string|max:500',
        ]);

        $tutor = Tutor::with('user:id,name,email')
            ->where('user_id', $userId)
            ->first();

        if (!$tutor) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan',
                'errors' => [
                    'user_id' => $userId,
                ],
            ], 404);
        }

        if ($tutor->salary == 0) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Gaji tutor sudah 0, tidak ada yang perlu dikonfirmasi',
                'errors' => [
                    'salary' => $tutor->salary,
                ],
            ], 400);
        }

        DB::beginTransaction();
        try {
            $invoiceFile = $request->file('invoice_file');
            $invoicePath = $invoiceFile->store('invoices/salary', 'public');

            $service = new SalaryPaymentService();
            $salaryPayment = $service->confirmPaymentWithInvoice($tutor, [
                'invoice_url' => $invoicePath,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
            ]);

            $service->sendPaymentEmail($salaryPayment);

            DB::commit();

            $paidSalary = $salaryPayment->amount;

            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Pembayaran gaji berhasil dikonfirmasi dan email telah diproses.',
                'data' => [
                    'user_id' => $tutor->user_id,
                    'name' => $tutor->user->name,
                    'email' => $tutor->user->email,
                    'paid_salary' => $paidSalary,
                    'invoice_url' => url('storage/' . $invoicePath),
                    'payment_method' => $request->payment_method,
                    'email_sent' => $salaryPayment->email_sent,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat konfirmasi pembayaran: ' . $e->getMessage(),
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function getPendingPayment(Request $request): ResponseDTO
    {
        $query = Tutor::with('user:id,name,email')
            ->where('salary', '>', 0);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'salary_desc');
        if ($sort === 'salary_asc') {
            $query->orderBy('salary', 'asc');
        } else {
            $query->orderBy('salary', 'desc');
        }

        $tutors = $query->limit(9)->get();

        $data = $tutors->map(function ($tutor) {
            return [
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'salary' => $tutor->salary,
                'bank' => $tutor->bank,
                'rekening' => $tutor->rekening,
            ];
        });

        $totalPending = $tutors->sum('salary');

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil data pembayaran tertunda',
            'data' => [
                'tutors' => $data,
                'count' => count($data),
                'total_pending' => $totalPending,
            ],
        ], 200);
    }

    public function getVerificationPending(Request $request): ResponseDTO
    {
        $query = Tutor::with('user:id,name,email,telephone_number')
            ->where('status', 'verify');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $tutors = $query->get();

        $data = $tutors->map(function ($tutor) {
            return [
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'telephone_number' => $tutor->user->telephone_number ?? '-',
                'experience' => $tutor->experience,
                'education' => $tutor->education,
                'qualification' => $tutor->qualification,
                'status' => $tutor->status->value,
            ];
        });

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil data verifikasi pending',
            'data' => [
                'tutors' => $data,
                'count' => count($data),
            ],
        ], 200);
    }

    public function getSalaryHistory($userId): ResponseDTO
    {
        $salaryPayments = SalaryPayment::where('user_id', $userId)
            ->orderBy('paid_at', 'desc')
            ->get();

        if ($salaryPayments->isEmpty()) {
            return new ResponseDTO([
                'status' => 'success',
                'message' => 'Belum ada riwayat pembayaran',
                'data' => [
                    'payments' => [],
                    'total_paid' => 0,
                ],
            ], 200);
        }

        $data = $salaryPayments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'paid_at' => $payment->paid_at->format('Y-m-d H:i:s'),
                'invoice_url' => $payment->invoice_url ? url('storage/' . $payment->invoice_url) : null,
                'note' => $payment->note,
                'email_sent' => $payment->email_sent,
            ];
        });

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil riwayat pembayaran',
            'data' => [
                'payments' => $data,
                'total_paid' => $salaryPayments->sum('amount'),
            ],
        ], 200);
    }
}
