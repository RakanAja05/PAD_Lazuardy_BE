<?php

namespace App\Services\Tutors;

use App\DTOs\ResponseDTO;
use App\Enums\TutorStatusEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FindTutorService
{
    public function search(Request $request): ResponseDTO
    {
        $user = Auth::user();

        if (!$user->latitude || !$user->longitude) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Lokasi Anda belum tersedia. Mohon aktifkan GPS atau lengkapi data alamat.',
                'errors' => [
                    'location' => 'missing',
                ],
            ], 400);
        }

        $lat = $user->latitude;
        $lng = $user->longitude;

        $radius = $request->input('radius', 10);
        $subjectId = $request->input('subject_id');
        $classId = $request->input('class_id');
        $minRating = $request->input('min_rating');
        $gender = $request->input('gender');

        $userProvince = null;
        if ($user->home_address && is_array($user->home_address)) {
            $userProvince = $user->home_address['province'] ?? null;
        } elseif ($user->home_address && is_string($user->home_address)) {
            $addressData = json_decode($user->home_address, true);
            $userProvince = $addressData['province'] ?? null;
        }

        $limit = 9;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;

        $weightRating = $request->input('weight_rating', 0.6);
        $weightDistance = $request->input('weight_distance', 0.4);

        $weightRating = max(0, min(1, $weightRating));
        $weightDistance = max(0, min(1, $weightDistance));

        $query = User::select('users.*')
            ->selectRaw("\n                (6371 * acos(\n                    cos(radians(?)) * cos(radians(latitude)) *\n                    cos(radians(longitude) - radians(?)) +\n                    sin(radians(?)) * sin(radians(latitude))\n                )) AS distance,\n                COALESCE(AVG(reviews.rate), 0) as avg_rating,\n                COUNT(reviews.id) as review_count\n            ", [$lat, $lng, $lat])
            ->join('tutors', 'users.id', '=', 'tutors.user_id')
            ->leftJoin('reviews', 'users.id', '=', 'reviews.to_user_id')
            ->where('users.role', 'tutor')
            ->where('tutors.status', TutorStatusEnum::ACTIVE->value)
            ->whereNotNull('users.latitude')
            ->whereNotNull('users.longitude')
            ->groupBy('users.id');

        if ($userProvince) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(users.home_address, '$.province')) = ?", [$userProvince]);
        }

        if ($gender) {
            $query->where('users.gender', $gender);
        }

        if ($subjectId) {
            $query->join('tutor_subjects', 'tutors.user_id', '=', 'tutor_subjects.user_id')
                ->where('tutor_subjects.subject_id', $subjectId);
        }

        if ($classId) {
            if (!$subjectId) {
                $query->join('tutor_subjects', 'tutors.user_id', '=', 'tutor_subjects.user_id')
                    ->join('subjects', 'tutor_subjects.subject_id', '=', 'subjects.id')
                    ->where('subjects.class_id', $classId);
            } else {
                $query->join('subjects', 'tutor_subjects.subject_id', '=', 'subjects.id')
                    ->where('subjects.class_id', $classId);
            }
        }

        $query->having('distance', '<=', $radius);

        if ($minRating) {
            $query->havingRaw('COALESCE(AVG(reviews.rate), 0) >= ?', [$minRating]);
        }

        $query->with(['tutor']);

        $allTutors = $query->get();

        $tutorsWithScore = $allTutors->map(function ($tutor) use ($radius, $weightRating, $weightDistance) {
            $normalizedRating = $tutor->avg_rating / 5;
            $normalizedDistance = 1 - ($tutor->distance / $radius);

            $recommendationScore = ($normalizedRating * $weightRating) + ($normalizedDistance * $weightDistance);

            $tutor->recommendation_score = round($recommendationScore, 4);
            $tutor->normalized_rating = round($normalizedRating, 4);
            $tutor->normalized_distance = round($normalizedDistance, 4);

            return $tutor;
        });

        $tutorsWithScore = $tutorsWithScore->sortByDesc('recommendation_score')->values();

        $totalTutors = $tutorsWithScore->count();
        $totalPages = (int) ceil($totalTutors / $limit);

        $tutors = $tutorsWithScore->slice($offset, $limit)->values();

        $result = $tutors->map(function ($tutor, $index) use ($offset) {
            $address = is_string($tutor->home_address)
                ? json_decode($tutor->home_address, true)
                : $tutor->home_address;

            return [
                'rank' => $offset + $index + 1,
                'recommendation_score' => $tutor->recommendation_score,
                'score_breakdown' => [
                    'normalized_rating' => $tutor->normalized_rating,
                    'normalized_distance' => $tutor->normalized_distance,
                ],
                'user_id' => $tutor->id,
                'name' => $tutor->name,
                'profile_photo_url' => $tutor->profile_photo_url,
                'gender' => $tutor->gender,
                'distance' => round($tutor->distance, 2),
                'distance_text' => round($tutor->distance, 2) . ' km',
                'address' => [
                    'subdistrict' => $address['subdistrict'] ?? null,
                    'district' => $address['district'] ?? null,
                    'regency' => $address['regency'] ?? null,
                    'province' => $address['province'] ?? null,
                    'full_text' => implode(', ', array_filter([
                        $address['subdistrict'] ?? null,
                        $address['district'] ?? null,
                        $address['regency'] ?? null,
                    ])),
                ],
                'tutor_info' => [
                    'price' => $tutor->tutor->price ?? null,
                    'price_formatted' => $tutor->tutor->price ? 'Rp ' . number_format($tutor->tutor->price, 0, ',', '.') : null,
                    'experience' => $tutor->tutor->experience ?? null,
                    'badge' => $tutor->tutor->badge ?? null,
                    'status' => $tutor->tutor->status ?? null,
                ],
                'rating' => [
                    'average' => round($tutor->avg_rating ?? 0, 1),
                    'count' => (int) ($tutor->review_count ?? 0),
                    'stars_text' => round($tutor->avg_rating ?? 0, 1) . ' ⭐',
                ],
                'maps_url' => "https://www.google.com/maps?q={$tutor->latitude},{$tutor->longitude}",
                'is_verified' => !empty($tutor->tutor->badge),
            ];
        });

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Tutors sorted by recommendation score',
            'data' => [
                'tutors' => $result,
                'pagination' => [
                    'current_page' => $page,
                    'total' => $totalTutors,
                    'per_page' => $limit,
                    'total_pages' => $totalPages,
                    'has_more' => $page < $totalPages,
                    'next_page' => $page < $totalPages ? $page + 1 : null,
                ],
                'filters' => [
                    'radius_km' => $radius,
                    'subject_id' => $subjectId,
                    'class_id' => $classId,
                    'min_rating' => $minRating,
                    'gender' => $gender,
                ],
                'auto_optimizations' => [
                    'province_filter' => $userProvince ? "Auto-filtered to {$userProvince} province" : 'No auto-filter applied',
                ],
                'algorithm' => [
                    'description' => 'Recommendation score = (normalized_rating x weight_rating) + (normalized_distance x weight_distance)',
                    'weights' => [
                        'rating' => $weightRating,
                        'distance' => $weightDistance,
                    ],
                ],
                'meta' => [
                    'user_location' => [
                        'latitude' => $lat,
                        'longitude' => $lng,
                    ],
                ],
            ],
        ], 200);
    }

    public function show(Request $request, $id): ResponseDTO
    {
        $user = Auth::user();

        if (!$user->latitude || !$user->longitude) {
            return new ResponseDTO([
                'status' => 'error',
                'message' => 'Lokasi Anda belum tersedia.',
                'errors' => [
                    'location' => 'missing',
                ],
            ], 400);
        }

        $lat = $user->latitude;
        $lng = $user->longitude;

        $tutor = User::selectRaw("users.*,\n            (6371 * acos(\n                cos(radians(?)) * cos(radians(latitude)) *\n                cos(radians(longitude) - radians(?)) +\n                sin(radians(?)) * sin(radians(latitude))\n            )) AS distance", [$lat, $lng, $lat])
            ->with(['tutor'])
            ->where('users.id', $id)
            ->where('users.role', 'tutor')
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

        $address = is_string($tutor->home_address)
            ? json_decode($tutor->home_address, true)
            : $tutor->home_address;

        return new ResponseDTO([
            'status' => 'success',
            'message' => 'Berhasil mengambil detail tutor',
            'data' => [
                'user_id' => $tutor->id,
                'name' => $tutor->name,
                'email' => $tutor->email,
                'telephone_number' => $tutor->telephone_number,
                'profile_photo_url' => $tutor->profile_photo_url,
                'gender' => $tutor->gender,
                'date_of_birth' => $tutor->date_of_birth,
                'religion' => $tutor->religion,
                'distance' => round($tutor->distance, 2),
                'address' => $address,
                'tutor_info' => $tutor->tutor,
            ],
        ], 200);
    }
}
