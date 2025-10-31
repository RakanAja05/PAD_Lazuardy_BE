<?php

namespace App\Http\Controllers;

use App\Enums\ScheduleStatusEnum;
use App\Models\StudentPackage;
use App\Models\TakenSchedule;
use App\Models\ScheduleTutor;
use App\Models\Review;
use Illuminate\Http\Request;

class TutorDashboardController extends Controller
{
    /**
     * Get dashboard data untuk tutor
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Cek apakah user adalah tutor
        if (!$user->tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'User bukan tutor'
            ], 403);
        }

        $tutor = $user->tutor;

        // Data Profil Tutor
        $profile = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'telephone_number' => $user->telephone_number,
            'profile_photo_url' => $user->profile_photo_url,
            'date_of_birth' => $user->date_of_birth,
            'gender' => $user->gender,
            'religion' => $user->religion,
            'home_address' => $user->home_address,
            'education' => $tutor->education,
            'salary' => $tutor->salary,
            'price' => $tutor->price,
            'description' => $tutor->description,
            'experience' => $tutor->experience,
            'organization' => $tutor->organization,
            'learning_method' => $tutor->learning_method,
            'qualification' => $tutor->qualification,
            'course_mode' => $tutor->course_mode,
            'status' => $tutor->status,
            'badge' => $tutor->badge,
            'sanction_amount' => $tutor->sanction_amount,
        ];

        // Mata Pelajaran yang Diajarkan
        $subjects = $user->subjects->map(function ($subject) {
            return [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'subject_icon' => $subject->icon_image_url,
                'class_name' => $subject->class->name ?? null,
                'curriculum_name' => $subject->curriculum->name ?? null,
            ];
        });

        // Daftar Siswa yang Diajar
        $students = StudentPackage::where('tutor_user_id', $user->id)
            ->with(['studentUser.student', 'subject', 'package'])
            ->get()
            ->map(function ($sp) {
                return [
                    'student_package_id' => $sp->id,
                    'student_user_id' => $sp->student_user_id,
                    'student_name' => $sp->studentUser->name ?? null,
                    'student_photo' => $sp->studentUser->profile_photo_url ?? null,
                    'student_email' => $sp->studentUser->email ?? null,
                    'student_phone' => $sp->studentUser->telephone_number ?? null,
                    'student_class' => $sp->studentUser->student->class->name ?? null,
                    'subject_name' => $sp->subject->name ?? null,
                    'package_name' => $sp->package->name ?? null,
                    'remaining_session' => $sp->remaining_session,
                    'total_session' => $sp->package->session ?? 0,
                    'progress_percentage' => $sp->package && $sp->package->session > 0 
                        ? round((($sp->package->session - $sp->remaining_session) / $sp->package->session) * 100, 2)
                        : 0,
                ];
            });

        // Statistik Siswa
        $studentStats = [
            'total_students' => $students->unique('student_user_id')->count(),
            'total_active_packages' => StudentPackage::where('tutor_user_id', $user->id)
                ->where('remaining_session', '>', 0)
                ->count(),
            'total_sessions_given' => StudentPackage::where('tutor_user_id', $user->id)
                ->get()
                ->sum(function ($sp) {
                    return ($sp->package->session ?? 0) - $sp->remaining_session;
                }),
        ];

        // Jadwal Mengajar
        $schedules = ScheduleTutor::where('user_id', $user->id)
            ->get()
            ->map(function ($schedule) {
                return [
                    'schedule_id' => $schedule->id,
                    'day' => $schedule->day,
                    'time' => $schedule->time,
                ];
            });

        // Jadwal yang Sudah Diambil Siswa
        $takenSchedules = TakenSchedule::whereHas('scheduleTutor', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['user', 'subject'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($ts) {
                return [
                    'id' => $ts->id,
                    'date' => $ts->date,
                    'status' => $ts->status,
                    'student_name' => $ts->user->name ?? null,
                    'student_photo' => $ts->user->profile_photo_url ?? null,
                    'subject_name' => $ts->subject->name ?? null,
                    'schedule_time' => $ts->scheduleTutor->time ?? null,
                ];
            });

        // Statistik Jadwal
        $scheduleStats = [
            'total_schedules' => $takenSchedules->count(),
            'completed_schedules' => $takenSchedules->where('status', ScheduleStatusEnum::COMPLETED->value)->count(),
            'pending_schedules' => $takenSchedules->where('status', ScheduleStatusEnum::PENDING->value)->count(),
            'cancelled_schedules' => $takenSchedules->where('status', ScheduleStatusEnum::CANCELLED->value)->count(),
        ];

        // Jadwal Mengajar Mendatang (7 hari)
        $upcomingSchedules = TakenSchedule::whereHas('scheduleTutor', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addDays(7)->toDateString())
            ->where('status', '!=', 'cancelled')
            ->with(['user', 'subject', 'scheduleTutor'])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($ts) {
                return [
                    'id' => $ts->id,
                    'date' => $ts->date,
                    'status' => $ts->status,
                    'student_name' => $ts->user->name ?? null,
                    'subject_name' => $ts->subject->name ?? null,
                    'schedule_time' => $ts->scheduleTutor->time ?? null,
                ];
            });

        // Pendapatan
        $completedSessions = TakenSchedule::whereHas('scheduleTutor', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', ScheduleStatusEnum::COMPLETED->value)
            ->count();        $earnings = [
            'completed_sessions' => $completedSessions,
            'salary_per_session' => $tutor->salary,
            'estimated_total_earnings' => $completedSessions * ($tutor->salary ?? 0),
        ];

        // Review
        $reviews = Review::where('to_user_id', $user->id)
            ->with('fromUser')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rate' => $review->rate,
                    'quality' => $review->quality,
                    'delivery' => $review->delivery,
                    'attitude' => $review->attitude,
                    'benefit' => $review->benefit,
                    'review' => $review->review,
                    'from_user_name' => $review->fromUser->name ?? null,
                    'from_user_photo' => $review->fromUser->profile_photo_url ?? null,
                    'created_at' => $review->created_at,
                ];
            });

        $reviewStats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => $reviews->avg('rate') ?? 0,
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'profile' => $profile,
                'subjects' => $subjects,
                'students' => $students,
                'student_stats' => $studentStats,
                'my_schedules' => $schedules,
                'taken_schedules' => $takenSchedules,
                'schedule_stats' => $scheduleStats,
                'upcoming_schedules' => $upcomingSchedules,
                'earnings' => $earnings,
                'reviews' => $reviews,
                'review_stats' => $reviewStats,
            ]
        ], 200);
    }

    /**
     * Get summary untuk widget dashboard
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        
        if (!$user->tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'User bukan tutor'
            ], 403);
        }

        $completedSessionsThisMonth = TakenSchedule::whereHas('scheduleTutor', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', 'completed')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $summary = [
            'total_students' => StudentPackage::where('tutor_user_id', $user->id)->distinct('student_user_id')->count(),
            'total_subjects' => $user->subjects->count(),
            'total_schedules_today' => TakenSchedule::whereHas('scheduleTutor', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('date', now()->toDateString())
                ->count(),
            'total_upcoming_schedules' => TakenSchedule::whereHas('scheduleTutor', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('date', '>=', now()->toDateString())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'completed_sessions_this_month' => $completedSessionsThisMonth,
            'estimated_earnings_this_month' => $completedSessionsThisMonth * ($user->tutor->salary ?? 0),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $summary
        ], 200);
    }
}
