<?php

namespace App\Services\Admin;

use App\DTOs\ResponseDTO;
use App\Enums\PaymentStatusEnum;
use App\Enums\TutorStatusEnum;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Student;
use App\Models\Tutor;
use Carbon\Carbon;

class ReportAndAnalyticService
{
    public function index(): ResponseDTO
    {
        $totalStudent = Student::all()->count();
        $totalStudentVerif = Payment::where('status', PaymentStatusEnum::UPLOADED)->count();
        $totalTutor = Tutor::all()->count();
        $totalTutorVerif = Tutor::where('status', TutorStatusEnum::VERIFY)->count();

        $startDate = Carbon::now()->startOfMonth();

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

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil laporan dan analitik',
            'data' => $data,
        ], 200);
    }
}
