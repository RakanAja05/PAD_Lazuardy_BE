<?php

namespace Database\Seeders;

use App\Enums\TutorStatusEnum;
use App\Models\Presence;
use App\Models\TakenSchedule;
use Illuminate\Database\Seeder;

class PresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get completed taken schedules (ACTIVE status means lesson happened)
        $takenSchedules = TakenSchedule::with(['student', 'scheduleTutor.user'])
            ->where('status', TutorStatusEnum::ACTIVE->value)
            ->get();

        if ($takenSchedules->isEmpty()) {
            $this->command->warn('No completed schedules found. Run TakenScheduleSeeder first.');
            return;
        }

        $evaluations = [
            'Siswa sangat aktif dan responsif dalam pembelajaran. Pemahaman konsep baik.',
            'Perlu lebih banyak latihan soal. Siswa cukup memahami materi dasar.',
            'Siswa menunjukkan progress yang baik. Mulai memahami konsep lebih dalam.',
            'Perlu tambahan waktu untuk pemahaman. Siswa berusaha keras mengikuti materi.',
            'Excellent progress! Student grasps concepts quickly and asks great questions.',
            'Siswa cukup fokus, namun perlu review ulang materi sebelumnya.',
            'Pembelajaran berjalan lancar. Siswa aktif bertanya dan berdiskusi.',
        ];

        $count = 0;

        foreach ($takenSchedules as $ts) {
            // 80% of completed schedules have presence records
            if (rand(1, 100) <= 80) {
                $tutorUserId = $ts->scheduleTutor->user_id;
                
                Presence::create([
                    'taken_schedule_id' => $ts->id,
                    'tutor_user_id' => $tutorUserId,
                    'student_user_id' => $ts->user_id,
                    'evaluation' => fake()->randomElement($evaluations),
                    'report' => rand(70, 100), // Score 70-100
                    'pbm_image_url' => 'https://example.com/presence/pbm_' . $ts->id . '.jpg',
                    'created_at' => now()->subDays(rand(1, 25)),
                    'updated_at' => now()->subDays(rand(0, 20)),
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Created {$count} presence/attendance records");
    }
}
