<?php

namespace Database\Seeders;

use App\Enums\FileTypeEnum;
use App\Enums\TutorStatusEnum;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tutors with VERIFY status (pending verification)
        $pendingTutors = User::whereHas('tutor', function($q) {
            $q->where('status', TutorStatusEnum::VERIFY->value);
        })->get();

        if ($pendingTutors->isEmpty()) {
            $this->command->warn('No pending tutors found for file seeding.');
            return;
        }

        $fileTypes = [
            FileTypeEnum::KTP,
            FileTypeEnum::IJAZAH,
            FileTypeEnum::CV,
        ];

        $optionalTypes = [
            FileTypeEnum::CERTIFICATE,
            FileTypeEnum::PORTOFOLIO,
        ];

        $count = 0;
        foreach ($pendingTutors as $tutor) {
            // Required files
            foreach ($fileTypes as $type) {
                File::create([
                    'user_id' => $tutor->id,
                    'name' => $type->displayName() . ' - ' . $tutor->name,
                    'type' => $type->value,
                    'path_url' => 'https://example.com/files/' . strtolower($type->value) . '_' . $tutor->id . '.pdf',
                ]);
                $count++;
            }

            // Optional files (50% chance)
            if (fake()->boolean(50)) {
                $randomOptional = fake()->randomElement($optionalTypes);
                File::create([
                    'user_id' => $tutor->id,
                    'name' => $randomOptional->displayName() . ' - ' . $tutor->name,
                    'type' => $randomOptional->value,
                    'path_url' => 'https://example.com/files/' . strtolower($randomOptional->value) . '_' . $tutor->id . '.pdf',
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Created {$count} file records for {$pendingTutors->count()} pending tutors");
    }
}
