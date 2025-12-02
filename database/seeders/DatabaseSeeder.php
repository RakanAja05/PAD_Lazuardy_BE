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
            // 1. Master data & users
            AdminSeeder::class,
            ClassSeeder::class,
            CurriculumSeeder::class,
            SubjectSeeder::class,
            
            // 2. Tutors & Students
            TutorSeeder::class,         // Buat tutors + attach subjects
            StudentSeeder::class,        // Buat students (untuk reviewer)
            
            // 3. Tutor schedules & files (needs tutors)
            ScheduleTutorSeeder::class,  // Buat jadwal tersedia untuk tutor
            FileSeeder::class,           // Buat file dokumen untuk tutor pending
            
            // 4. Reviews & confirmations (needs tutors & students)
            ReviewSeeder::class,         // Buat reviews untuk tutors (PENTING untuk scoring!)
            TutorConfirmSeeder::class,   // Buat konfirmasi tutor oleh student
            
            // 5. Packages & orders (needs students & tutors)
            PackageSeeder::class,
            StudentPackageSeeder::class,
            OrderSeeder::class,          // Buat order paket oleh student
            
            // 6. Payments (needs orders)
            PaymentSeeder::class,        // Buat payment untuk order
            
            // 7. Schedules & presence (needs student packages & schedules)
            TakenScheduleSeeder::class,  // Buat jadwal yang diambil student
            PresenceSeeder::class,       // Buat catatan kehadiran & evaluasi
        ]);
    }
}
