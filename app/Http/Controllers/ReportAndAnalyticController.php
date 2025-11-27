<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use App\Enums\TutorStatusEnum;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Student;
use App\Models\Tutor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportAndAnalyticController extends Controller
{
    public function index()
    {
        $totalStudent = Student::all()->count();
        $totalStudentVerif = Payment::where('status', PaymentStatusEnum::UPLOADED)->count();
        $totalTutor = Tutor::all()->count();
        $totalTutorVerif = Tutor::where('status', TutorStatusEnum::VERIFY)->count();

        $startDate = Carbon::now()->startOfMonth();

        // Technical debt: Transaksi yang terhitung masih sejak kapan pembayaran itu dibuat bukan tepat pas dibayarnya
        $totalTransaction = Payment::where('status', PaymentStatusEnum::VALIDATED)
                                ->where('created_at', '>=', $startDate)
                                ->count();

        $averageRating = Review::avg('rate');

        $data = [
            'total_student' => $totalStudent,
            'total_student_verif' => $totalStudentVerif,
            'total_tutor' => $totalTutor,
            'total_tutor_verif' => $totalTutorVerif,
            'total_transaction' => $totalTransaction,
            'average_rating' => $averageRating,
        ];

        return response()->json($data, 200);
    }
}
