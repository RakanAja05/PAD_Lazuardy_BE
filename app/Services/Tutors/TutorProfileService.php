<?php

namespace App\Services\Tutors;

use App\DTOs\ResponseDTO;
use App\Models\Review;
use App\Models\ScheduleTutor;
use App\Models\TakenSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorProfileService
{
    public function show(Request $request, $id): ResponseDTO
    {
        $user = Auth::user();

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
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan',
                'errors' => [
                    'tutor_id' => $id,
                ],
            ], 404);
        }

        $distance = null;
        if ($user->latitude && $user->longitude && $tutor->latitude && $tutor->longitude) {
            $distance = $this->calculateDistance(
                $user->latitude,
                $user->longitude,
                $tutor->latitude,
                $tutor->longitude
            );
        }

        $address = is_string($tutor->home_address)
            ? json_decode($tutor->home_address, true)
            : $tutor->home_address;

        $subjects = $tutor->tutor->subjects->map(function ($subject) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'class' => [
                    'id' => $subject->class->id ?? null,
                    'name' => $subject->class->name ?? null,
                ],
            ];
        });

        $availableSchedules = $this->getAvailableSchedules($id);
        $reviews = $this->getReviews($id, $request->input('review_page', 1));

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil profil tutor',
            'data' => [
                'user_id' => $tutor->id,
                'name' => $tutor->name,
                'profile_photo_url' => $tutor->profile_photo_url,
                'telephone_number' => $tutor->telephone_number,
                'gender' => $tutor->gender,
                'rating' => [
                    'average' => round($tutor->avg_rating, 1),
                    'count' => (int) $tutor->review_count,
                    'stars_text' => round($tutor->avg_rating, 1) . ' ⭐',
                ],
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
                'subjects' => $subjects,
                'qualification' => [
                    'education' => $tutor->tutor->education ?? [],
                    'experience' => $tutor->tutor->experience ?? null,
                    'specialization' => $tutor->tutor->qualification ?? [],
                ],
                'teaching_method' => [
                    'description' => $tutor->tutor->learning_method ?? null,
                    'course_mode' => $tutor->tutor->course_mode ?? null,
                ],
                'tutor_info' => [
                    'price' => $tutor->tutor->price ?? null,
                    'price_formatted' => $tutor->tutor->price
                        ? 'Rp ' . number_format($tutor->tutor->price, 0, ',', '.')
                        : null,
                    'description' => $tutor->tutor->description ?? null,
                    'badge' => $tutor->tutor->badge ?? null,
                    'status' => $tutor->tutor->status ?? null,
                ],
                'schedule_action' => [
                    'text' => 'Lihat Jadwal Mengajar',
                    'has_schedules' => !empty($availableSchedules['schedules']),
                ],
                'available_schedules' => $availableSchedules,
                'reviews' => $reviews,
            ],
        ], 200);
    }

    public function availableSlots(Request $request, $id): ResponseDTO
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = $request->input('date');
        $dayOfWeek = date('N', strtotime($date));

        $schedules = ScheduleTutor::where('user_id', $id)
            ->where('day', $dayOfWeek)
            ->get();

        $availableSlots = $schedules->map(function ($schedule) use ($date) {
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

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil jadwal tersedia',
            'data' => [
                'date' => $date,
                'day_name' => $this->getDayName($dayOfWeek),
                'slots' => $availableSlots,
            ],
        ], 200);
    }

    private function getAvailableSchedules($tutorId): array
    {
        $schedules = ScheduleTutor::where('user_id', $tutorId)
            ->orderBy('day')
            ->orderBy('time')
            ->get();

        $groupedByDay = $schedules->groupBy('day')->map(function ($daySchedules, $day) use ($tutorId) {
            return [
                'day' => $day,
                'day_name' => $this->getDayName($day),
                'time_slots' => $daySchedules->map(function ($schedule) use ($tutorId) {
                    $upcomingBookings = TakenSchedule::where('schedule_tutor_id', $schedule->id)
                        ->where('date', '>=', now()->toDateString())
                        ->count();

                    return [
                        'schedule_id' => $schedule->id,
                        'time' => $schedule->time,
                        'is_available' => true,
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

    private function getReviews($tutorId, $page = 1): array
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
            'data' => $reviews->map(function ($review) {
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

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function getDayName($day): string
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
}
