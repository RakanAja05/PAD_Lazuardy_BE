<?php

namespace Database\Seeders;

use App\Enums\DayEnum;
use App\Models\ScheduleTutor;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduleTutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tutors
        $tutors = User::whereHas('tutor')->get();

        if ($tutors->isEmpty()) {
            $this->command->warn('No tutors found. Run TutorSeeder first.');
            return;
        }

        $days = DayEnum::cases();
        $timeSlots = [
            '08:00:00', '09:00:00', '10:00:00', '11:00:00',
            '13:00:00', '14:00:00', '15:00:00', '16:00:00',
            '17:00:00', '18:00:00', '19:00:00', '20:00:00',
        ];

        $count = 0;
        foreach ($tutors as $tutor) {
            // Each tutor gets 3-5 random days with 2-4 time slots per day
            $tutorDays = fake()->randomElements($days, rand(3, 5));
            
            foreach ($tutorDays as $day) {
                $tutorSlots = fake()->randomElements($timeSlots, rand(2, 4));
                
                foreach ($tutorSlots as $time) {
                    ScheduleTutor::create([
                        'user_id' => $tutor->id,
                        'day' => $day->value,
                        'time' => $time,
                    ]);
                    $count++;
                }
            }
        }

        $this->command->info("✅ Created {$count} schedule slots for {$tutors->count()} tutors");
    }
}
