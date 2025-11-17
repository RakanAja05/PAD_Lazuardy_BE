<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentPackage;
use App\Models\Student;
use App\Models\Package;
use App\Models\Subject;
use App\Models\Tutor;

class StudentPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data yang diperlukan
        $students = Student::all();
        $packages = Package::all();
        $subjects = Subject::all();
        $tutors = Tutor::all();

        if ($students->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada data student. Jalankan StudentSeeder terlebih dahulu.');
            return;
        }

        if ($packages->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada data package. Jalankan PackageSeeder terlebih dahulu.');
            return;
        }

        if ($subjects->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada data subject. Jalankan seeder untuk Subject terlebih dahulu.');
            return;
        }

        if ($tutors->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada data tutor. Jalankan TutorSeeder terlebih dahulu.');
            return;
        }

        // Data student packages
        $studentPackages = [];
        $totalCount = 0;

        foreach ($students as $student) {
            // Setiap student beli 1-2 paket random
            $packageCount = rand(1, 2);
            $selectedPackages = $packages->random($packageCount);

            foreach ($selectedPackages as $package) {
                // Jika package punya 1 subject, buat 1 student_package
                if ($package->subject_amount == 1) {
                    $subject = $subjects->random();
                    $tutor = $tutors->random();
                    $remainingSession = rand(0, $package->session);

                    $studentPackages[] = [
                        'package_id' => $package->id,
                        'student_user_id' => $student->user_id,
                        'subject_id' => $subject->id,
                        'tutor_user_id' => $tutor->user_id,
                        'remaining_session' => $remainingSession,
                        'created_at' => now()->subDays(rand(0, 60)),
                        'updated_at' => now(),
                    ];
                    $totalCount++;
                } 
                // Jika package punya 2+ subject, buat multiple student_packages
                else {
                    // Ambil subjects sejumlah subject_amount (tanpa duplikat)
                    $selectedSubjects = $subjects->random($package->subject_amount);
                    
                    foreach ($selectedSubjects as $subject) {
                        $tutor = $tutors->random();
                        $remainingSession = rand(0, $package->session);

                        $studentPackages[] = [
                            'package_id' => $package->id,
                            'student_user_id' => $student->user_id,
                            'subject_id' => $subject->id,
                            'tutor_user_id' => $tutor->user_id,
                            'remaining_session' => $remainingSession,
                            'created_at' => now()->subDays(rand(0, 60)),
                            'updated_at' => now(),
                        ];
                        $totalCount++;
                    }
                }
            }
        }

        // Insert data
        foreach ($studentPackages as $studentPackage) {
            StudentPackage::create($studentPackage);
        }

        $this->command->info('✅ StudentPackageSeeder berhasil! Total: ' . $totalCount . ' student packages');
    }
}
