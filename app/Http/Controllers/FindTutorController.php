<?php

namespace App\Http\Controllers;

use App\Enums\TutorStatusEnum;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FindTutorController extends Controller
{
    /**
     * Cari tutor dengan filter + scoring algorithm
     * 
     * Algoritma:
     * 1. Apply filter (subject, class, min_rating, radius)
     * 2. Hitung recommendation score untuk setiap tutor
     * 3. Sort by score DESC dan ambil top 6 per halaman
     * 
     * @OA\Get(
     *   path="/api/find-tutor",
     *   tags={"Tutor Search"},
     *   summary="Cari tutor dengan filter dan scoring algorithm",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="radius",
     *     in="query",
     *     description="Radius pencarian dalam km (default: 10)",
     *     @OA\Schema(type="number", example=10)
     *   ),
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Nomor halaman untuk infinite scroll (default: 1, fixed 6 tutors per page)",
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Parameter(
     *     name="subject_id",
     *     in="query",
     *     description="Filter berdasarkan mata pelajaran tertentu",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="class_id",
     *     in="query",
     *     description="Filter berdasarkan kelas (SD, SMP, SMA, dll)",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="min_rating",
     *     in="query",
     *     description="Filter rating minimum (0-5)",
     *     @OA\Schema(type="number", example=4.0)
     *   ),
     *   @OA\Parameter(
     *     name="weight_rating",
     *     in="query",
     *     description="Bobot rating dalam scoring (0-1, default: 0.6)",
     *     @OA\Schema(type="number", example=0.6)
     *   ),
     *   @OA\Parameter(
     *     name="weight_distance",
     *     in="query",
     *     description="Bobot jarak dalam scoring (0-1, default: 0.4)",
     *     @OA\Schema(type="number", example=0.4)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Daftar tutor dengan score tertinggi"
     *   )
     * )
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        // Validasi user harus sudah ada koordinat
        if (!$user->latitude || !$user->longitude) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi Anda belum tersedia. Mohon aktifkan GPS atau lengkapi data alamat.'
            ], 400);
        }

        $lat = $user->latitude;
        $lng = $user->longitude;
        
        // Filter dari frontus
        $radius = $request->input('radius', 10); // Default 10 km
        $subjectId = $request->input('subject_id');
        $classId = $request->input('class_id');
        $minRating = $request->input('min_rating');
        $gender = $request->input('gender'); // 'man' atau 'woman'
        
        // Auto-detect province dari user location untuk optimasi query (hidden filter)
        $userProvince = null;
        if ($user->home_address && is_array($user->home_address)) {
            $userProvince = $user->home_address['province'] ?? null;
        } elseif ($user->home_address && is_string($user->home_address)) {
            $addressData = json_decode($user->home_address, true);
            $userProvince = $addressData['province'] ?? null;
        }
        
        // Pagination
        $limit = 9; 
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $limit;
        
        // Bobot untuk scoring (customizable via query params)
        $weightRating = $request->input('weight_rating', 0.6); // 60% rating
        $weightDistance = $request->input('weight_distance', 0.4); // 40% distance
        
        // Validasi weight harus 0-1
        $weightRating = max(0, min(1, $weightRating));
        $weightDistance = max(0, min(1, $weightDistance));

        // Build query dengan filter
        $query = User::select('users.*')
            ->selectRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance,
                COALESCE(AVG(reviews.rate), 0) as avg_rating,
                COUNT(reviews.id) as review_count
            ", [$lat, $lng, $lat])
            ->join('tutors', 'users.id', '=', 'tutors.user_id')
            ->leftJoin('reviews', 'users.id', '=', 'reviews.to_user_id')
            ->where('users.role', 'tutor')
            ->where('tutors.status', TutorStatusEnum::ACTIVE->value)
            ->whereNotNull('users.latitude')
            ->whereNotNull('users.longitude')
            ->groupBy('users.id');

        // AUTO-FILTER: Province dari user location (hidden filter untuk optimasi traffic)
        // Hanya cari tutor dalam province yang sama dengan user untuk mengurangi data yang diproses
        if ($userProvince) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(users.home_address, '$.province')) = ?", [$userProvince]);
        }

        // FILTER FRONTEND: Gender
        if ($gender) {
            $query->where('users.gender', $gender);
        }

        // FILTER FRONTEND: Subject
        if ($subjectId) {
            $query->join('tutor_subjects', 'tutors.user_id', '=', 'tutor_subjects.user_id')
                ->where('tutor_subjects.subject_id', $subjectId);
        }

        // FILTER FRONTEND: Class (via subjects)
        if ($classId) {
            if (!$subjectId) {
                // Jika subject_id tidak ada, join ke tutor_subjects dan subjects
                $query->join('tutor_subjects', 'tutors.user_id', '=', 'tutor_subjects.user_id')
                    ->join('subjects', 'tutor_subjects.subject_id', '=', 'subjects.id')
                    ->where('subjects.class_id', $classId);
            } else {
                // Jika subject_id sudah ada, tinggal tambahkan join ke subjects
                $query->join('subjects', 'tutor_subjects.subject_id', '=', 'subjects.id')
                    ->where('subjects.class_id', $classId);
            }
        }

        // FILTER FRONTEND: Radius
        $query->having('distance', '<=', $radius);

        // FILTER FRONTEND: Min Rating
        if ($minRating) {
            $query->havingRaw('COALESCE(AVG(reviews.rate), 0) >= ?', [$minRating]);
        }

        // Load relation
        $query->with(['tutor']);

        // Get all filtered tutors
        $allTutors = $query->get();

        // Hitung recommendation score untuk setiap tutor
        $tutorsWithScore = $allTutors->map(function($tutor) use ($radius, $weightRating, $weightDistance) {
            // Normalize rating (0-1): rating 5 = 1.0
            $normalizedRating = $tutor->avg_rating / 5;
            
            // Normalize distance (0-1): jarak 0 = 1.0, jarak = radius = 0
            $normalizedDistance = 1 - ($tutor->distance / $radius);
            
            // Hitung recommendation score
            $recommendationScore = ($normalizedRating * $weightRating) + ($normalizedDistance * $weightDistance);
            
            $tutor->recommendation_score = round($recommendationScore, 4);
            $tutor->normalized_rating = round($normalizedRating, 4);
            $tutor->normalized_distance = round($normalizedDistance, 4);
            
            return $tutor;
        });

        // Sort by recommendation_score DESC (tertinggi dulu)
        $tutorsWithScore = $tutorsWithScore->sortByDesc('recommendation_score')->values();

        // Get total count for pagination
        $totalTutors = $tutorsWithScore->count();
        $totalPages = ceil($totalTutors / $limit);

        // Apply pagination (slice collection)
        $tutors = $tutorsWithScore->slice($offset, $limit)->values();

        // Format response - FRONTEND FRIENDLY
        $result = $tutors->map(function($tutor, $index) use ($offset) {
            $address = is_string($tutor->home_address) 
                ? json_decode($tutor->home_address, true) 
                : $tutor->home_address;

            return [
                'rank' => $offset + $index + 1, // Ranking global (sesuai pagination)
                'recommendation_score' => $tutor->recommendation_score, // Skor total (0-1)
                'score_breakdown' => [
                    'normalized_rating' => $tutor->normalized_rating, // Kontribusi rating
                    'normalized_distance' => $tutor->normalized_distance, // Kontribusi jarak
                ],
                'user_id' => $tutor->id,
                'name' => $tutor->name,
                'profile_photo_url' => $tutor->profile_photo_url,
                'gender' => $tutor->gender,
                'distance' => round($tutor->distance, 2), // dalam km
                'distance_text' => round($tutor->distance, 2) . ' km', // Helper untuk display
                'address' => [
                    'subdistrict' => $address['subdistrict'] ?? null,
                    'district' => $address['district'] ?? null,
                    'regency' => $address['regency'] ?? null,
                    'province' => $address['province'] ?? null,
                    'full_text' => implode(', ', array_filter([
                        $address['subdistrict'] ?? null,
                        $address['district'] ?? null,
                        $address['regency'] ?? null,
                    ])), // Helper untuk display alamat lengkap
                ],
                'tutor_info' => [
                    'price' => $tutor->tutor->price ?? null,
                    'price_formatted' => $tutor->tutor->price ? 'Rp ' . number_format($tutor->tutor->price, 0, ',', '.') : null, // Helper untuk display
                    'experience' => $tutor->tutor->experience ?? null,
                    'badge' => $tutor->tutor->badge ?? null,
                    'status' => $tutor->tutor->status ?? null,
                ],
                'rating' => [
                    'average' => round($tutor->avg_rating ?? 0, 1), // Rating rata-rata 0-5
                    'count' => (int) ($tutor->review_count ?? 0), // Jumlah review
                    'stars_text' => round($tutor->avg_rating ?? 0, 1) . ' ⭐', // Helper untuk display
                ],
                // Extra helpers untuk frontend
                'maps_url' => "https://www.google.com/maps?q={$tutor->latitude},{$tutor->longitude}", // Direct Google Maps link
                'is_verified' => !empty($tutor->tutor->badge), // Helper untuk cek verified badge
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Tutors sorted by recommendation score',
            'data' => $result,
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
                'description' => 'Recommendation score = (normalized_rating × weight_rating) + (normalized_distance × weight_distance)',
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
            ]
        ]);
    }

    /**
     * Get tutor detail by ID dengan informasi jarak
     * 
     * @OA\Get(
     *   path="/api/find-tutor/{id}",
     *   tags={"Tutor Search"},
     *   summary="Detail tutor dengan perhitungan jarak",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="Detail tutor")
     * )
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->latitude || !$user->longitude) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi Anda belum tersedia.'
            ], 400);
        }

        $lat = $user->latitude;
        $lng = $user->longitude;

        $tutor = User::selectRaw("users.*, 
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance", [$lat, $lng, $lat])
            ->with(['tutor'])
            ->where('users.id', $id)
            ->where('users.role', 'tutor')
            ->first();

        if (!$tutor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tutor tidak ditemukan'
            ], 404);
        }

        $address = is_string($tutor->home_address) 
            ? json_decode($tutor->home_address, true) 
            : $tutor->home_address;

        return response()->json([
            'status' => 'success',
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
            ]
        ]);
    }
}
