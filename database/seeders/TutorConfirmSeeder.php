<?php

namespace Database\Seeders;

use App\Enums\TutorStatusEnum;
use App\Models\TutorConfirm;
use App\Models\User;
use Illuminate\Database\Seeder;

class TutorConfirmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get students and tutors
        $students = User::whereHas('student')->get();
        $tutors = User::whereHas('tutor')->get();

        if ($students->isEmpty() || $tutors->isEmpty()) {
            $this->command->warn('Need both students and tutors. Run StudentSeeder and TutorSeeder first.');
            return;
        }

        $reasons = [
            'Tutor sangat berpengalaman dan metode mengajarnya cocok',
            'Jadwal tutor sesuai dengan kebutuhan saya',
            'Rekomendasi dari teman yang pernah belajar dengan tutor ini',
            'Harga terjangkau dan kualitas bagus',
            'Tutor memiliki spesialisasi di bidang yang saya butuhkan',
            null, // Some confirmations without reason
        ];

        $count = 0;

        // Each student confirms 1-2 tutors
        foreach ($students as $student) {
            $numConfirms = rand(1, 2);
            $selectedTutors = $tutors->random(min($numConfirms, $tutors->count()));

            foreach ($selectedTutors as $tutor) {
                // Status: 80% active (confirmed), 15% verify (pending), 5% rejected
                $statusRoll = rand(1, 100);
                if ($statusRoll <= 80) {
                    $status = TutorStatusEnum::ACTIVE;
                    $reason = fake()->randomElement($reasons);
                } elseif ($statusRoll <= 95) {
                    $status = TutorStatusEnum::VERIFY;
                    $reason = null; // Pending confirmations usually don't have reason yet
                } else {
                    $status = TutorStatusEnum::REJECTED;
                    $reason = 'Jadwal tidak cocok dengan kebutuhan saya';
                }

                TutorConfirm::create([
                    'student_user_id' => $student->id,
                    'tutor_user_id' => $tutor->id,
                    'reason' => $reason,
                    'status' => $status->value,
                    'created_at' => now()->subDays(rand(5, 60)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Created {$count} tutor confirmation records");
    }
}
