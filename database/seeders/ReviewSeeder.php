<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Student;
use App\Models\User;
use App\Enums\RatingOptionEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua students untuk jadi reviewer
        $students = Student::with('user')->get();
        
        if ($students->isEmpty()) {
            Log::warning('❌ Tidak ada student untuk ReviewSeeder. Jalankan StudentSeeder dulu!');
            return;
        }

        // Mapping tutor email → target rating & jumlah review
        $tutorRatingMap = [
            // TIER 1: High rating + Close distance
            'budi.santoso@example.com' => [
                'target_avg' => 4.8,
                'review_count' => 25,
            ],
            'siti.nurhaliza@example.com' => [
                'target_avg' => 4.7,
                'review_count' => 20,
            ],
            
            // TIER 2: High rating + Medium distance
            'ahmad.zainudin@example.com' => [
                'target_avg' => 4.3,
                'review_count' => 18,
            ],
            'dewi.sartika@example.com' => [
                'target_avg' => 4.2,
                'review_count' => 15,
            ],
            
            // TIER 3: Medium rating
            'rudi.hartono@example.com' => [
                'target_avg' => 3.8,
                'review_count' => 12,
            ],
            
            // TIER 4: High rating + Far distance
            'linda.wijaya@example.com' => [
                'target_avg' => 4.5,
                'review_count' => 10,
            ],
            
            // TIER 5: Low rating + Far distance
            'agus.prasetyo@example.com' => [
                'target_avg' => 3.2,
                'review_count' => 8,
            ],
            'rina.kusuma@example.com' => [
                'target_avg' => 3.4,
                'review_count' => 7,
            ],
            
            // TIER 6: Low rating for filter testing
            'bambang.wijaya@example.com' => [
                'target_avg' => 2.8,
                'review_count' => 5,
            ],
            
            // TIER 7: Perfect rating + Medium distance
            'maya.kusuma@example.com' => [
                'target_avg' => 4.9,
                'review_count' => 22,
            ],
        ];

        // Rating options enum values
        $ratingOptions = RatingOptionEnum::cases();

        foreach ($tutorRatingMap as $email => $config) {
            $tutor = User::where('email', $email)->first();
            
            if (!$tutor) {
                Log::warning("❌ Tutor dengan email {$email} tidak ditemukan!");
                continue;
            }

            $targetAvg = $config['target_avg'];
            $reviewCount = $config['review_count'];

            // Generate rating distribution untuk mencapai target average
            $ratings = $this->generateRatingDistribution($targetAvg, $reviewCount);

            // Buat reviews
            foreach ($ratings as $index => $rating) {
                // Random student sebagai reviewer
                $student = $students->random();
                
                // Skip jika sudah ada review dari student ini
                $exists = Review::where('from_user_id', $student->user_id)
                    ->where('to_user_id', $tutor->id)
                    ->exists();
                    
                if ($exists) {
                    continue;
                }

                // Map rating ke enum options
                $enumValue = $this->mapRatingToEnum($rating, $ratingOptions);

                Review::create([
                    'from_user_id' => $student->user_id,
                    'to_user_id' => $tutor->id,
                    'quality' => $enumValue,
                    'delivery' => $enumValue,
                    'attitude' => $enumValue,
                    'benefit' => $enumValue,
                    'rate' => $rating,
                    'review' => $this->getReviewComment($rating),
                ]);
            }
        }

        echo "✅ Berhasil membuat reviews untuk 10 tutors!\n";
        echo "⭐ Rating range: 2.8 - 4.9 bintang\n";
        echo "📝 Total reviews: ~150 reviews\n";
    }

    /**
     * Generate rating distribution yang menghasilkan target average
     */
    private function generateRatingDistribution(float $targetAvg, int $count): array
    {
        $ratings = [];
        
        // Strategy: Buat distribusi berdasarkan target
        if ($targetAvg >= 4.5) {
            // Mostly 5 stars dengan sedikit 4 stars
            $fiveStarCount = (int)($count * 0.85);
            $fourStarCount = $count - $fiveStarCount;
            
            $ratings = array_merge(
                array_fill(0, $fiveStarCount, 5.0),
                array_fill(0, $fourStarCount, 4.0)
            );
        } elseif ($targetAvg >= 4.0) {
            // Mix 5, 4, dan sedikit 3 stars
            $fiveStarCount = (int)($count * 0.5);
            $fourStarCount = (int)($count * 0.4);
            $threeStarCount = $count - $fiveStarCount - $fourStarCount;
            
            $ratings = array_merge(
                array_fill(0, $fiveStarCount, 5.0),
                array_fill(0, $fourStarCount, 4.0),
                array_fill(0, $threeStarCount, 3.0)
            );
        } elseif ($targetAvg >= 3.5) {
            // Mix 4, 3, dan 2 stars
            $fourStarCount = (int)($count * 0.4);
            $threeStarCount = (int)($count * 0.4);
            $twoStarCount = $count - $fourStarCount - $threeStarCount;
            
            $ratings = array_merge(
                array_fill(0, $fourStarCount, 4.0),
                array_fill(0, $threeStarCount, 3.0),
                array_fill(0, $twoStarCount, 2.0)
            );
        } else {
            // Mostly low ratings (3, 2, 1 stars)
            $threeStarCount = (int)($count * 0.4);
            $twoStarCount = (int)($count * 0.4);
            $oneStarCount = $count - $threeStarCount - $twoStarCount;
            
            $ratings = array_merge(
                array_fill(0, $threeStarCount, 3.0),
                array_fill(0, $twoStarCount, 2.0),
                array_fill(0, $oneStarCount, 1.0)
            );
        }

        shuffle($ratings);
        return $ratings;
    }

    /**
     * Map numeric rating to RatingOptionEnum
     */
    private function mapRatingToEnum(float $rating, array $options): string
    {
        if ($rating >= 4.5) {
            return $options[0]->value; // Excellent
        } elseif ($rating >= 3.5) {
            return $options[1]->value; // Good
        } elseif ($rating >= 2.5) {
            return $options[2]->value; // Average
        } else {
            return $options[3]->value; // Poor
        }
    }

    /**
     * Get review comment based on rating
     */
    private function getReviewComment(float $rating): string
    {
        if ($rating >= 4.5) {
            $comments = [
                'Sangat puas dengan pengajarannya! Highly recommended!',
                'Tutor terbaik! Penjelasannya sangat jelas dan mudah dipahami.',
                'Luar biasa! Anak saya jadi lebih semangat belajar.',
                'Metode mengajarnya sangat efektif. Worth it!',
                'Excellent tutor! Sabar dan profesional.',
            ];
        } elseif ($rating >= 3.5) {
            $comments = [
                'Cukup bagus, penjelasannya lumayan jelas.',
                'Good tutor, tapi kadang agak terburu-buru.',
                'Overall bagus, tapi bisa lebih interaktif lagi.',
                'Memuaskan, anak saya ada progress.',
                'Pengajarannya oke, recommended.',
            ];
        } elseif ($rating >= 2.5) {
            $comments = [
                'Biasa saja, tidak terlalu istimewa.',
                'Kurang interaktif, tapi masih bisa dipahami.',
                'Average, sesuai harga lah.',
                'Lumayan, tapi masih bisa lebih baik.',
                'Cukup membantu, tapi tidak wow.',
            ];
        } else {
            $comments = [
                'Kurang memuaskan, penjelasan terlalu cepat.',
                'Tidak sesuai ekspektasi.',
                'Kurang sabar dalam mengajar.',
                'Banyak yang tidak dijelaskan dengan baik.',
                'Perlu improvement dalam metode mengajar.',
            ];
        }

        return $comments[array_rand($comments)];
    }
}
