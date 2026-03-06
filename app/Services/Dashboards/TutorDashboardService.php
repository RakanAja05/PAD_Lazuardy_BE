<?php

namespace App\Services\Dashboards;

use App\DTOs\ResponseDTO;
use App\Enums\ScheduleStatusEnum;
use App\Models\Review;
use App\Models\ScheduleTutor;
use App\Models\StudentPackage;
use App\Models\TakenSchedule;
use Illuminate\Http\Request;

class TutorDashboardService
{
    public function index(Request $request): ResponseDTO
    {
        $user = $request->user();

        if (!$user->tutor) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'User bukan tutor',
                'errors' => [
                    'role' => 'tutor_required',
                ],
            ], 403);
        }

        $tutor = $user->tutor;

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

        $subjects = $user->subjects->map(function ($subject) {
            return [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'subject_icon' => $subject->icon_image_url,
                'class_name' => $subject->class->name ?? null,
                'curriculum_name' => $subject->curriculum->name ?? null,
            ];
        });

        $students = StudentPackage::where('tutor_user_id', $user->id)
            ->with(['student.student', 'subject', 'package'])
            ->get()
            ->map(function ($sp) {
                return [
                    'student_package_id' => $sp->id,
                    'student_user_id' => $sp->student_user_id,
                    'student_name' => $sp->student->name ?? null,
                    'student_photo' => $sp->student->profile_photo_url ?? null,
                    'student_email' => $sp->student->email ?? null,
                    'student_phone' => $sp->student->telephone_number ?? null,
                    'student_class' => $sp->student->student->class->name ?? null,
                    'subject_name' => $sp->subject->name ?? null,
                    'package_name' => $sp->package->name ?? null,
                    'remaining_session' => $sp->remaining_session,
                    'total_session' => $sp->package->session ?? 0,
                    'progress_percentage' => $sp->package && $sp->package->session > 0
                        ? round((($sp->package->session - $sp->remaining_session) / $sp->package->session) * 100, 2)
                        : 0,
                ];
            });

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

        $schedules = ScheduleTutor::where('user_id', $user->id)
            ->get()
            ->map(function ($schedule) {
                return [
                    'schedule_id' => $schedule->id,
                    'day' => $schedule->day,
                    'time' => $schedule->time,
                ];
            });

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

        $scheduleStats = [
            'total_schedules' => $takenSchedules->count(),
            'completed_schedules' => $takenSchedules->where('status', ScheduleStatusEnum::COMPLETED->value)->count(),
            'pending_schedules' => $takenSchedules->where('status', ScheduleStatusEnum::PENDING->value)->count(),
            'cancelled_schedules' => $takenSchedules->where('status', ScheduleStatusEnum::CANCELLED->value)->count(),
        ];

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

        $completedSessions = TakenSchedule::whereHas('scheduleTutor', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', ScheduleStatusEnum::COMPLETED->value)
            ->count();

        $earnings = [
            'completed_sessions' => $completedSessions,
            'salary_per_session' => $tutor->salary,
            'estimated_total_earnings' => $completedSessions * ($tutor->salary ?? 0),
        ];

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

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil dashboard tutor',
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
            ],
        ], 200);
    }

    public function summary(Request $request): ResponseDTO
    {
        $user = $request->user();

        if (!$user->tutor) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'User bukan tutor',
                'errors' => [
                    'role' => 'tutor_required',
                ],
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

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil ringkasan tutor',
            'data' => $summary,
        ], 200);
    }
}
