<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ClassSeeder::class,
            CurriculumSeeder::class,
            SubjectSeeder::class,
            TutorSeeder::class,         // Buat tutors + attach subjects
            StudentSeeder::class,        // Buat students (untuk reviewer)
            ReviewSeeder::class,         // Buat reviews untuk tutors (PENTING untuk scoring!)
            PackageSeeder::class,
            StudentPackageSeeder::class,
        ]);
    }
}
