<?php

namespace App\Http\Controllers;

use App\Enums\ScheduleStatusEnum;
use App\Models\StudentPackage;
use App\Models\TakenSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    /**
     * Get dashboard data untuk student
     * 
     * @OA\Get(
     *     path="/api/dashboard/student",
     *     tags={"Dashboard"},
     *     summary="Get student dashboard data",
     *     description="Menampilkan data lengkap dashboard student: profile, paket belajar, jadwal, dan statistik.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="profile", type="object",
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="class", type="string", example="kelas 10"),
     *                 @OA\Property(property="school", type="string")
     *             ),
     *             @OA\Property(property="packages", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="package_stats", type="object",
     *                 @OA\Property(property="total_packages", type="integer"),
     *                 @OA\Property(property="total_remaining_sessions", type="integer")
     *             ),
     *             @OA\Property(property="schedules", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="schedule_stats", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="User bukan student")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Cek apakah user adalah student
        if (!$user->student) {
            return response()->json([
                'status' => 'error',
                'message' => 'User bukan student'
            ], 403);
        }

        $student = $user->student;

        // Data Profil Siswa
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
            'student_id' => $student->user_id,
            'class' => $student->class ? $student->class->name : null,
            'curriculum' => $student->curriculum ? $student->curriculum->name : null,
            'school' => $student->school,
            'parent_name' => $student->parent,
            'parent_telephone_number' => $student->parent_telephone_number,
        ];

        // Data Paket yang Dimiliki
        $packages = StudentPackage::where('student_user_id', $user->id)
            ->with(['package', 'subject', 'tutor'])
            ->get()
                ->map(function ($sp) {
                return [
                    'id' => $sp->id,
                    'package_name' => $sp->package?->name ?? null,
                    'package_session' => $sp->package?->session ?? 0,
                    'remaining_session' => $sp->remaining_session,
                    'used_session' => ($sp->package?->session ?? 0) - $sp->remaining_session,
                    'subject_name' => $sp->subject?->name ?? null,
                    'tutor_name' => $sp->tutor?->name ?? null,
                    'tutor_photo' => $sp->tutor?->profile_photo_url ?? null,
                ];
            });

        // Statistik Paket
        $packageStats = [
            'total_packages' => $packages->count(),
            'total_remaining_sessions' => $packages->sum('remaining_session'),
            'total_used_sessions' => $packages->sum('used_session'),
        ];

        // Jadwal yang Diambil
        $schedules = TakenSchedule::where('user_id', $user->id)
            ->with(['scheduleTutor.user', 'subject'])
            ->orderBy('date', 'desc')
            ->get()
                ->map(function ($ts) {
                return [
                    'id' => $ts->id,
                    'date' => $ts->date,
                    'status' => $ts->status,
                    'subject_name' => $ts->subject?->name ?? null,
                    'tutor_name' => $ts->scheduleTutor?->user?->name ?? null,
                    'tutor_photo' => $ts->scheduleTutor?->user?->profile_photo_url ?? null,
                    'schedule_day' => $ts->scheduleTutor?->day ?? null,
                    'schedule_time' => $ts->scheduleTutor?->time ?? null,
                ];
            });

        // Statistik Jadwal
        $scheduleStats = [
            'total_schedules' => $schedules->count(),
            'completed_schedules' => $schedules->where('status', ScheduleStatusEnum::COMPLETED->value)->count(),
            'pending_schedules' => $schedules->where('status', ScheduleStatusEnum::PENDING->value)->count(),
            'cancelled_schedules' => $schedules->where('status', ScheduleStatusEnum::CANCELLED->value)->count(),
        ];

        // Jadwal Mendatang (7 hari ke depan)
        $upcomingSchedules = TakenSchedule::where('user_id', $user->id)
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addDays(7)->toDateString())
            ->where('status', '!=', ScheduleStatusEnum::CANCELLED->value)
            ->with(['scheduleTutor.user', 'subject'])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($ts) {
                return [
                    'id' => $ts->id,
                    'date' => $ts->date,
                    'status' => $ts->status,
                    'subject_name' => $ts->subject?->name ?? null,
                    'tutor_name' => $ts->scheduleTutor?->user?->name ?? null,
                    'schedule_time' => $ts->scheduleTutor?->time ?? null,
                ];
            });

        // Daftar Tutor yang Pernah Mengajar (hanya unique tutors)
        $myTutors = StudentPackage::where('student_user_id', $user->id)
            ->with(['tutor'])
            ->get()
            ->unique('tutor_user_id')
            ->map(function ($sp) {
                return [
                    'tutor_id' => $sp->tutor_user_id,
                    'tutor_name' => $sp->tutor?->name ?? null,
                    'tutor_photo' => $sp->tutor?->profile_photo_url ?? null,
                    'tutor_education' => $sp->tutor?->tutor?->education ?? null,
                    'tutor_experience' => $sp->tutor?->tutor?->experience ?? null,
                ];
            })
            ->values();

        // Mata Pelajaran yang Dipelajari
        $subjects = StudentPackage::where('student_user_id', $user->id)
            ->with('subject')
            ->get()
            ->unique('subject_id')
            ->map(function ($sp) {
                return [
                    'subject_id' => $sp->subject_id,
                    'subject_name' => $sp->subject?->name ?? null,
                    'subject_icon' => $sp->subject?->icon_image_url ?? null,
                ];
            })
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'profile' => $profile,
                'packages' => $packages,
                'package_stats' => $packageStats,
                'schedules' => $schedules,
                'schedule_stats' => $scheduleStats,
                'upcoming_schedules' => $upcomingSchedules,
                'my_tutors' => $myTutors,
                'subjects' => $subjects,
            ]
        ], 200);
    }

    /**
     * Get recommended tutors dengan pagination (5 tutor per page)
     * Untuk kotak "Tutor Rekomendasi" di dashboard
     * 
     * @OA\Get(
     *     path="/api/dashboard/student/recommended-tutors",
     *     tags={"Dashboard"},
     *     summary="Get recommended tutors for student",
     *     description="Menampilkan daftar tutor rekomendasi berdasarkan kedekatan lokasi (5 tutors per page).",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="profile_photo_url", type="string"),
     *                     @OA\Property(property="education", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="subjects", type="array", @OA\Items(type="object"))
     *                 )
     *             ),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="User bukan student")
     * )
     */
    public function getRecommendedTutors(Request $request)
    {
        $user = $request->user();
        
        if (!$user->student) {
            return response()->json([
                'status' => 'error',
                'message' => 'User bukan student'
            ], 403);
        }

        // Ambil alamat student
        $studentAddress = $user->home_address;
        
        // Query tutor dengan status active
        $tutorsQuery = User::where('role', 'tutor')
            ->whereHas('tutor', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['tutor', 'subjects']);

        // Sorting berdasarkan kedekatan alamat (prioritas: regency > district > province)
        if ($studentAddress) {
            $tutorsQuery->orderByRaw("
                CASE
                    WHEN JSON_EXTRACT(home_address, '$.regency') = ? THEN 1
                    WHEN JSON_EXTRACT(home_address, '$.district') = ? THEN 2
                    WHEN JSON_EXTRACT(home_address, '$.province') = ? THEN 3
                    ELSE 4
                END
            ", [
                $studentAddress['regency'] ?? '',
                $studentAddress['district'] ?? '',
                $studentAddress['province'] ?? ''
            ]);
        }

        // Pagination: 5 tutors per page
        $tutors = $tutorsQuery->paginate(5);

        return response()->json([
            'status' => 'success',
            'data' => $tutors->map(function ($tutor) {
                return [
                    'tutor_id' => $tutor->id,
                    'tutor_name' => $tutor->name,
                    'tutor_photo' => $tutor->profile_photo_url,
                    'gender' => $tutor->gender,
                    'address' => $tutor->home_address,
                    'education' => $tutor->tutor?->education ?? null,
                    'experience' => $tutor->tutor?->experience ?? 0,
                    'price' => $tutor->tutor?->price ?? 0,
                    'description' => $tutor->tutor?->description ?? null,
                    'course_mode' => $tutor->tutor?->course_mode ?? null,
                    'badge' => $tutor->tutor?->badge ?? null,
                    'subjects' => $tutor->subjects->map(function ($subject) {
                        return [
                            'subject_id' => $subject->id,
                            'subject_name' => $subject->name,
                            'subject_icon' => $subject->icon_image_url ?? null,
                        ];
                    }),
                ];
            }),
            'pagination' => [
                'current_page' => $tutors->currentPage(),
                'last_page' => $tutors->lastPage(),
                'per_page' => $tutors->perPage(),
                'total' => $tutors->total(),
                'has_more' => $tutors->hasMorePages(),
            ]
        ], 200);
    }

    /**
     * Get summary untuk widget dashboard
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        
        if (!$user->student) {
            return response()->json([
                'status' => 'error',
                'message' => 'User bukan student'
            ], 403);
        }

        $summary = [
            'total_packages' => StudentPackage::where('student_user_id', $user->id)->count(),
            'total_remaining_sessions' => StudentPackage::where('student_user_id', $user->id)->sum('remaining_session'),
            'total_schedules_today' => TakenSchedule::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->count(),
            'total_upcoming_schedules' => TakenSchedule::where('user_id', $user->id)
                ->where('date', '>=', now()->toDateString())
                ->where('status', '!=', ScheduleStatusEnum::CANCELLED->value)
                ->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $summary
        ], 200);
    }
}
