<?php

namespace App\Http\Controllers;

use App\Mail\SalaryPaymentMail;
use App\Models\SalaryPayment;
use App\Models\Tutor;
use App\Models\User;
use App\Services\SalaryPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorSalaryController extends Controller
{
    /**
     * Menampilkan daftar gaji semua tutor
     *
     * @OA\Get(
     *     path="/api/admin/tutor-salary",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Get list of tutor salaries",
     *     description="Menampilkan daftar gaji semua tutor yang sudah aktif.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by tutor name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_salary",
     *         in="query",
     *         description="Filter minimum salary",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="tutor@example.com"),
     *                     @OA\Property(property="salary", type="integer", example=500000),
     *                     @OA\Property(property="bank", type="string", example="BCA"),
     *                     @OA\Property(property="rekening", type="string", example="1234567890")
     *                 )
     *             ),
     *             @OA\Property(property="total_salary", type="integer", example=2500000)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Only admin can access")
     * )
     */
    public function index(Request $request)
    {
        $query = Tutor::with('user:id,name,email')
            ->where('status', 'active');

        // Filter by search name
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by minimum salary
        if ($request->has('min_salary')) {
            $query->where('salary', '>=', $request->min_salary);
        }

        $tutors = $query->get();

        $data = $tutors->map(function($tutor) {
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

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'total_salary' => $totalSalary
        ]);
    }

    /**
     * Menampilkan detail gaji tutor
     *
     * @OA\Get(
     *     path="/api/admin/tutor-salary/{userId}",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Get tutor salary detail",
     *     description="Menampilkan detail gaji tutor termasuk breakdown dari setiap sesi mengajar.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID of the tutor",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="salary", type="integer"),
     *                 @OA\Property(property="bank", type="string"),
     *                 @OA\Property(property="rekening", type="string"),
     *                 @OA\Property(property="sanction_amount", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tutor not found")
     * )
     */
    public function show($userId)
    {
        $tutor = Tutor::with('user:id,name,email')
            ->where('user_id', $userId)
            ->first();

        if (!$tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'salary' => $tutor->salary,
                'bank' => $tutor->bank,
                'rekening' => $tutor->rekening,
                'sanction_amount' => $tutor->sanction_amount,
            ]
        ]);
    }

    /**
     * Konfirmasi pembayaran gaji tutor dan reset ke 0
     *
     * @OA\Post(
     *     path="/api/admin/tutor-salary/{userId}/confirm",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Confirm salary payment",
     *     description="Mengkonfirmasi bahwa gaji tutor sudah dibayarkan dan mereset salary menjadi 0.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID of the tutor",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="note", type="string", example="Pembayaran periode November 2025", description="Optional note for payment confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Salary confirmed and reset",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Gaji tutor berhasil dikonfirmasi dan direset"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="paid_salary", type="integer", example=500000, description="Amount that was paid"),
     *                 @OA\Property(property="current_salary", type="integer", example=0, description="Current salary after reset")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tutor not found"),
     *     @OA\Response(response=400, description="Salary is already 0")
     * )
     */
    public function confirmPayment(Request $request, $userId)
    {
        $request->validate([
            'note' => 'nullable|string|max:500'
        ]);

        $tutor = Tutor::with('user:id,name,email')
            ->where('user_id', $userId)
            ->first();

        if (!$tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan'
            ], 404);
        }

        if ($tutor->salary == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gaji tutor sudah 0, tidak ada yang perlu dikonfirmasi'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $paidSalary = $tutor->salary;

            // Reset salary to 0
            $tutor->salary = 0;
            $tutor->save();

            // Optional: You can log this payment confirmation to a separate table
            // PaymentLog::create([...])

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Gaji tutor berhasil dikonfirmasi dan direset',
                'data' => [
                    'user_id' => $tutor->user_id,
                    'name' => $tutor->user->name,
                    'paid_salary' => $paidSalary,
                    'current_salary' => $tutor->salary,
                    'note' => $request->note
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Konfirmasi pembayaran gaji untuk multiple tutors sekaligus
     *
     * @OA\Post(
     *     path="/api/admin/tutor-salary/confirm-batch",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Confirm salary payment for multiple tutors",
     *     description="Mengkonfirmasi pembayaran gaji untuk beberapa tutor sekaligus dan mereset salary mereka menjadi 0.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tutor_ids"},
     *             @OA\Property(
     *                 property="tutor_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={2, 3, 5}
     *             ),
     *             @OA\Property(property="note", type="string", example="Pembayaran batch periode November 2025")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Batch payment confirmed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_tutors", type="integer", example=3),
     *                 @OA\Property(property="total_paid", type="integer", example=1500000),
     *                 @OA\Property(property="tutors", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="user_id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="paid_salary", type="integer")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function confirmBatchPayment(Request $request)
    {
        // Handle tutor_ids as either array or JSON string
        $tutorIds = $request->input('tutor_ids');

        // If it's a string, decode it
        if (is_string($tutorIds)) {
            $decoded = json_decode($tutorIds, true);
            if (is_array($decoded)) {
                $tutorIds = $decoded;
            }
        }

        // Ensure it's an array
        if (!is_array($tutorIds)) {
            $tutorIds = [];
        }

        // Validate the data
        $validated = $request->validate([
            'note' => 'nullable|string|max:500'
        ]);

        // Manual validation for tutor_ids
        if (empty($tutorIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'tutor_ids harus disediakan dan tidak boleh kosong'
            ], 422);
        }

        // Verify all tutor IDs exist
        $existingTutors = Tutor::whereIn('user_id', $tutorIds)->pluck('user_id')->toArray();
        $missingTutors = array_diff($tutorIds, $existingTutors);

        if (!empty($missingTutors)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tutor dengan ID: ' . implode(', ', $missingTutors) . ' tidak ditemukan'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $tutors = Tutor::with('user:id,name,email')
                ->whereIn('user_id', $tutorIds)
                ->where('salary', '>', 0)
                ->get();

            if ($tutors->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada tutor dengan gaji > 0 untuk dikonfirmasi'
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
                    'paid_salary' => $paidSalary
                ];

                $tutor->salary = 0;
                $tutor->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran gaji batch berhasil dikonfirmasi',
                'data' => [
                    'total_tutors' => count($tutorDetails),
                    'total_paid' => $totalPaid,
                    'tutors' => $tutorDetails,
                    'note' => $request->note
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat konfirmasi batch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Konfirmasi pembayaran gaji dengan upload invoice
     *
     * @OA\Post(
     *     path="/api/admin/tutor-salary/{userId}/confirm-with-invoice",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Confirm salary payment with invoice",
     *     description="Konfirmasi pembayaran gaji tutor dengan upload invoice bukti transfer dan mengirim email ke tutor.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID of the tutor",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"invoice_file", "payment_method"},
     *                 @OA\Property(property="invoice_file", type="string", format="binary", description="Invoice file (pdf, jpg, jpeg, png - max 5MB)"),
     *                 @OA\Property(property="payment_method", type="string", enum={"transfer", "check", "e-wallet"}, example="transfer"),
     *                 @OA\Property(property="note", type="string", example="Pembayaran periode November 2025")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Salary confirmed and email sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Pembayaran gaji berhasil dikonfirmasi dan email dikirim ke tutor"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="paid_salary", type="integer"),
     *                 @OA\Property(property="invoice_url", type="string"),
     *                 @OA\Property(property="payment_method", type="string"),
     *                 @OA\Property(property="email_sent", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tutor not found"),
     *     @OA\Response(response=400, description="Salary is already 0"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function confirmPaymentWithInvoice(Request $request, $userId)
    {
        $request->validate([
            'invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'payment_method' => 'required|string|in:transfer,check,e-wallet',
            'note' => 'nullable|string|max:500'
        ]);

        $tutor = Tutor::with('user:id,name,email')
            ->where('user_id', $userId)
            ->first();

        if (!$tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan'
            ], 404);
        }

        if ($tutor->salary == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gaji tutor sudah 0, tidak ada yang perlu dikonfirmasi'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $invoiceFile = $request->file('invoice_file');

            // Upload invoice ke storage/app/public/invoices/salary
            $invoicePath = $invoiceFile->store('invoices/salary', 'public');

            // Use service untuk payment confirmation
            $service = new SalaryPaymentService();
            $salaryPayment = $service->confirmPaymentWithInvoice($tutor, [
                'invoice_url' => $invoicePath,
                'payment_method' => $request->payment_method,
                'note' => $request->note
            ]);

            // Send email via service
            $service->sendPaymentEmail($salaryPayment);

            DB::commit();

            $paidSalary = $salaryPayment->amount;

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran gaji berhasil dikonfirmasi dan email telah diproses.',
                'data' => [
                    'user_id' => $tutor->user_id,
                    'name' => $tutor->user->name,
                    'email' => $tutor->user->email,
                    'paid_salary' => $paidSalary,
                    'invoice_url' => url('storage/' . $invoicePath),
                    'payment_method' => $request->payment_method,
                    'email_sent' => $salaryPayment->email_sent
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Menampilkan daftar tutor yang memiliki gaji terhutang (salary > 0)
     *
     * @OA\Get(
     *     path="/api/admin/tutor-salary/pending-payment",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Get tutors with pending salary",
     *     description="Menampilkan daftar tutor yang memiliki gaji terhutang (salary > 0) yang perlu dibayarkan.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by tutor name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort by: salary_asc, salary_desc (default: salary_desc)",
     *         @OA\Schema(type="string", enum={"salary_asc", "salary_desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="tutor@example.com"),
     *                     @OA\Property(property="salary", type="integer", example=500000),
     *                     @OA\Property(property="bank", type="string", example="BCA"),
     *                     @OA\Property(property="rekening", type="string", example="1234567890")
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=5),
     *             @OA\Property(property="total_pending", type="integer", example=2500000)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Only admin can access")
     * )
     */
    public function getPendingPayment(Request $request)
    {
        $query = Tutor::with('user:id,name,email')
            ->where('salary', '>', 0);

        // Filter by search name
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Sort by salary
        $sort = $request->input('sort', 'salary_desc');
        if ($sort === 'salary_asc') {
            $query->orderBy('salary', 'asc');
        } else {
            $query->orderBy('salary', 'desc');
        }

        // Limit 9 per request
        $tutors = $query->limit(9)->get();

        $data = $tutors->map(function($tutor) {
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

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'count' => count($data),
            'total_pending' => $totalPending
        ]);
    }

    /**
     * Menampilkan daftar tutor dengan status verification (pending verification)
     *
     * @OA\Get(
     *     path="/api/admin/tutor/verification-pending",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Get tutors pending verification",
     *     description="Menampilkan daftar tutor yang sedang menunggu verifikasi (status = verify).",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by tutor name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="tutor@example.com"),
     *                     @OA\Property(property="telephone_number", type="string", example="081234567890"),
     *                     @OA\Property(property="experience", type="string"),
     *                     @OA\Property(property="education", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="qualification", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="status", type="string", example="verify")
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Only admin can access")
     * )
     */
    public function getVerificationPending(Request $request)
    {
        $query = Tutor::with('user:id,name,email,telephone_number')
            ->where('status', 'verify');

        // Filter by search name
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $tutors = $query->get();

        $data = $tutors->map(function($tutor) {
            return [
                'user_id' => $tutor->user_id,
                'name' => $tutor->user->name,
                'email' => $tutor->user->email,
                'telephone_number' => $tutor->user->telephone_number ?? '-',
                'experience' => $tutor->experience,
                'education' => $tutor->education,
                'qualification' => $tutor->qualification,
                'status' => $tutor->status->value
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'count' => count($data)
        ]);
    }


    /**
     * Menampilkan history pembayaran gaji tutor
     *
     * @OA\Get(
     *     path="/api/admin/tutor-salary/{userId}/history",
     *     tags={"Admin - Tutor Salary"},
     *     summary="Get tutor salary payment history",
     *     description="Menampilkan riwayat pembayaran gaji untuk tutor tertentu.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID of the tutor",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="amount", type="integer"),
     *                     @OA\Property(property="payment_method", type="string"),
     *                     @OA\Property(property="paid_at", type="string", format="date-time"),
     *                     @OA\Property(property="invoice_url", type="string"),
     *                     @OA\Property(property="note", type="string"),
     *                     @OA\Property(property="email_sent", type="boolean")
     *                 )
     *             ),
     *             @OA\Property(property="total_paid", type="integer")
     *         )
     *     )
     * )
     */
    public function getSalaryHistory($userId)
    {
        $salaryPayments = SalaryPayment::where('user_id', $userId)
            ->orderBy('paid_at', 'desc')
            ->get();

        if ($salaryPayments->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'total_paid' => 0,
                'message' => 'Belum ada riwayat pembayaran'
            ]);
        }

        $data = $salaryPayments->map(function($payment) {
            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'paid_at' => $payment->paid_at->format('Y-m-d H:i:s'),
                'invoice_url' => $payment->invoice_url ? url('storage/' . $payment->invoice_url) : null,
                'note' => $payment->note,
                'email_sent' => $payment->email_sent
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'total_paid' => $salaryPayments->sum('amount')
        ]);
    }
}
