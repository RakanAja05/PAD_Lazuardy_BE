<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use App\Models\ScheduleTutor;
use App\Models\TakenSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TutorProfileController extends Controller
{
    /**
     * Tampilkan profile tutor lengkap untuk student
     * 
     * Menampilkan:
     * - Informasi dasar tutor (nama, foto, rating, kontak)
     * - Kualifikasi & metode mengajar
     * - Mata pelajaran yang diajar
     * - Jadwal kosong tutor
     * - Review dari siswa lain
     * 
     * @OA\Get(
     *   path="/api/tutor-profile/{id}",
     *   tags={"Tutor Profile"},
     *   summary="Tampilkan profile tutor lengkap",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="User ID dari tutor",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Profile tutor berhasil dimuat"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Tutor tidak ditemukan"
     *   )
     * )
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        
        // Get tutor data dengan rating
        $tutor = User::select('users.*')
            ->selectRaw('COALESCE(AVG(reviews.rate), 0) as avg_rating')
            ->selectRaw('COUNT(reviews.id) as review_count')
            ->leftJoin('reviews', 'users.id', '=', 'reviews.to_user_id')
            ->with(['tutor', 'tutor.subjects', 'tutor.subjects.class'])
            ->where('users.id', $id)
            ->where('users.role', 'tutor')
            ->groupBy('users.id')
            ->first();

        if (!$tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan'
            ], 404);
        }

        // Calculate distance if user has location
        $distance = null;
        if ($user->latitude && $user->longitude && $tutor->latitude && $tutor->longitude) {
            $distance = $this->calculateDistance(
                $user->latitude, 
                $user->longitude, 
                $tutor->latitude, 
                $tutor->longitude
            );
        }

        // Parse home address
        $address = is_string($tutor->home_address) 
            ? json_decode($tutor->home_address, true) 
            : $tutor->home_address;

        // Format subjects
        $subjects = $tutor->tutor->subjects->map(function($subject) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'class' => [
                    'id' => $subject->class->id ?? null,
                    'name' => $subject->class->name ?? null,
                ],
            ];
        });

        // Get available schedules (jadwal kosong)
        $availableSchedules = $this->getAvailableSchedules($id);

        // Get reviews with pagination
        $reviews = $this->getReviews($id, $request->input('review_page', 1));

        // Format response sesuai gambar
        return response()->json([
            'status' => 'success',
            'data' => [
                // HEADER INFO
                'user_id' => $tutor->id,
                'name' => $tutor->name,
                'profile_photo_url' => $tutor->profile_photo_url,
                'telephone_number' => $tutor->telephone_number,
                'gender' => $tutor->gender,
                
                // RATING INFO
                'rating' => [
                    'average' => round($tutor->avg_rating, 1),
                    'count' => (int) $tutor->review_count,
                    'stars_text' => round($tutor->avg_rating, 1) . ' ⭐',
                ],

                // LOCATION INFO
                'location' => [
                    'subdistrict' => $address['subdistrict'] ?? null,
                    'district' => $address['district'] ?? null,
                    'regency' => $address['regency'] ?? null,
                    'province' => $address['province'] ?? null,
                    'full_text' => implode(', ', array_filter([
                        $address['district'] ?? null,
                        $address['regency'] ?? null,
                    ])),
                    'distance_km' => $distance ? round($distance, 1) : null,
                ],

                // MATA PELAJARAN
                'subjects' => $subjects,

                // KUALIFIKASI
                'qualification' => [
                    'education' => $tutor->tutor->education ?? [],
                    'experience' => $tutor->tutor->experience ?? null,
                    'specialization' => $tutor->tutor->qualification ?? [],
                ],

                // METODE MENGAJAR
                'teaching_method' => [
                    'description' => $tutor->tutor->learning_method ?? null,
                    'course_mode' => $tutor->tutor->course_mode ?? null,
                ],

                // TUTOR INFO
                'tutor_info' => [
                    'price' => $tutor->tutor->price ?? null,
                    'price_formatted' => $tutor->tutor->price 
                        ? 'Rp ' . number_format($tutor->tutor->price, 0, ',', '.') 
                        : null,
                    'description' => $tutor->tutor->description ?? null,
                    'badge' => $tutor->tutor->badge ?? null,
                    'status' => $tutor->tutor->status ?? null,
                ],

                // LIHAT JADWAL MENGAJAR (button action)
                'schedule_action' => [
                    'text' => 'Lihat Jadwal Mengajar',
                    'has_schedules' => !empty($availableSchedules['schedules']),
                ],

                // JADWAL KOSONG (available schedules)
                'available_schedules' => $availableSchedules,

                // ULASAN DARI SISWA
                'reviews' => $reviews,
            ]
        ]);
    }

    /**
     * Get available schedules untuk tutor
     * Filter: jadwal yang belum di-booking untuk tanggal >= hari ini
     */
    private function getAvailableSchedules($tutorId)
    {
        // Get all schedules untuk tutor
        $schedules = ScheduleTutor::where('user_id', $tutorId)
            ->orderBy('day')
            ->orderBy('time')
            ->get();

        // Group by day
        $groupedByDay = $schedules->groupBy('day')->map(function($daySchedules, $day) use ($tutorId) {
            return [
                'day' => $day,
                'day_name' => $this->getDayName($day),
                'time_slots' => $daySchedules->map(function($schedule) use ($tutorId) {
                    // Check if this schedule is taken for upcoming dates
                    $upcomingBookings = TakenSchedule::where('schedule_tutor_id', $schedule->id)
                        ->where('date', '>=', now()->toDateString())
                        ->count();

                    return [
                        'schedule_id' => $schedule->id,
                        'time' => $schedule->time,
                        'is_available' => true, // You can add more logic here
                        'upcoming_bookings' => $upcomingBookings,
                    ];
                })->values(),
            ];
        })->values();

        return [
            'total_days' => $groupedByDay->count(),
            'schedules' => $groupedByDay,
        ];
    }

    /**
     * Get reviews untuk tutor dengan pagination
     */
    private function getReviews($tutorId, $page = 1)
    {
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $reviews = Review::where('to_user_id', $tutorId)
            ->with('fromUser')
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $totalReviews = Review::where('to_user_id', $tutorId)->count();

        return [
            'total' => $totalReviews,
            'current_page' => $page,
            'per_page' => $perPage,
            'has_more' => ($offset + $perPage) < $totalReviews,
            'data' => $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'reviewer' => [
                        'name' => $review->fromUser->name ?? 'Rakan',
                        'photo_url' => $review->fromUser->profile_photo_url ?? null,
                    ],
                    'rating' => [
                        'stars' => $review->rate,
                        'quality' => $review->quality,
                        'delivery' => $review->delivery,
                        'attitude' => $review->attitude,
                        'benefit' => $review->benefit,
                    ],
                    'review_text' => $review->review,
                    'date' => $review->created_at->format('d/m/Y'),
                ];
            }),
        ];
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Get day name from day number
     */
    private function getDayName($day)
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return $days[$day] ?? 'Unknown';
    }

    /**
     * Get available time slots for a specific date
     * 
     * @OA\Get(
     *   path="/api/tutor-profile/{id}/available-slots",
     *   tags={"Tutor Profile"},
     *   summary="Cek slot waktu tersedia untuk tanggal tertentu",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="date",
     *     in="query",
     *     required=true,
     *     description="Tanggal yang ingin dicek (format: Y-m-d)",
     *     @OA\Schema(type="string", format="date")
     *   ),
     *   @OA\Response(response=200, description="Available slots")
     * )
     */
    public function availableSlots(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = $request->input('date');
        $dayOfWeek = date('N', strtotime($date)); // 1 (Monday) to 7 (Sunday)

        // Get schedules for this day
        $schedules = ScheduleTutor::where('user_id', $id)
            ->where('day', $dayOfWeek)
            ->get();

        // Check which ones are already taken for this date
        $availableSlots = $schedules->map(function($schedule) use ($date) {
            $isTaken = TakenSchedule::where('schedule_tutor_id', $schedule->id)
                ->where('date', $date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            return [
                'schedule_id' => $schedule->id,
                'time' => $schedule->time,
                'is_available' => !$isTaken,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'day_name' => $this->getDayName($dayOfWeek),
                'slots' => $availableSlots,
            ]
        ]);
    }
}
