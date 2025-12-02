<?php

namespace Database\Seeders;

use App\Enums\TakenScheduleStatusEnum;
use App\Models\ScheduleTutor;
use App\Models\StudentPackage;
use App\Models\TakenSchedule;
use Illuminate\Database\Seeder;

class TakenScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active student packages
        $studentPackages = StudentPackage::with(['student', 'tutor'])->get();

        if ($studentPackages->isEmpty()) {
            $this->command->warn('No student packages found. Run StudentPackageSeeder first.');
            return;
        }

        $count = 0;

        foreach ($studentPackages as $sp) {
            // Get tutor's available schedules
            $tutorSchedules = ScheduleTutor::where('user_id', $sp->tutor_user_id)->get();

            if ($tutorSchedules->isEmpty()) {
                continue;
            }

            // Student books 2-4 schedule slots from this tutor
            $numBookings = min(rand(2, 4), $tutorSchedules->count());
            $bookedSchedules = $tutorSchedules->random($numBookings);

            foreach ($bookedSchedules as $schedule) {
                // Generate date within the last 30 days
                $date = now()->subDays(rand(1, 30))->format('Y-m-d');

                // Status: 70% active, 20% completed, 10% cancelled
                $statusRoll = rand(1, 100);
                if ($statusRoll <= 70) {
                    $status = TakenScheduleStatusEnum::ACTIVE;
                } elseif ($statusRoll <= 90) {
                    $status = TakenScheduleStatusEnum::COMPLETED;
                } else {
                    $status = TakenScheduleStatusEnum::CANCELLED;
                }

                TakenSchedule::create([
                    'user_id' => $sp->student_user_id,
                    'schedule_tutor_id' => $schedule->id,
                    'subject_id' => $sp->subject_id,
                    'date' => $date,
                    'status' => $status->value,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Created {$count} taken schedule records");
    }
}
