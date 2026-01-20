<?php

namespace Database\Seeders;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
use App\Models\ScheduleTutor;
use App\Models\StudentPackage;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class PendingPaymentSeeder extends Seeder
{
    /**
     * Seed pending payment data untuk testing
     * Membuat: Tutor dengan salary > 0, Student yang diajar, Paket aktif
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Ambil beberapa tutor yang sudah dibuat (dari TutorSeeder)
            $tutors = Tutor::where('status', 'active')->take(3)->get();

            if ($tutors->count() < 3) {
                $this->command->warn('⚠️ Kurang dari 3 tutor aktif tersedia');
                return;
            }

            // Ambil paket yang sudah dibuat
            $packages = Package::take(2)->get();
            if ($packages->count() < 2) {
                $this->command->warn('⚠️ Kurang dari 2 paket tersedia');
                return;
            }

            // Ambil subject untuk student packages
            $subjects = Subject::take(2)->get();
            if ($subjects->count() < 2) {
                $this->command->warn('⚠️ Kurang dari 2 subject tersedia');
                return;
            }

            // Ambil students yang sudah dibuat (dari StudentSeeder)
            $students = User::where('role', 'student')->take(5)->get();
            if ($students->count() < 5) {
                $this->command->warn('⚠️ Kurang dari 5 student tersedia');
                return;
            }

            $this->command->info('🔄 Membuat data Pending Payment...');

            // ==== TUTOR 1: Salary 500000 ====
            $tutor1 = $tutors[0];
            $package1 = $packages[0];

            // Buat 2 student packages untuk tutor 1
            $studentPackage1 = StudentPackage::create([
                'student_user_id' => $students[0]->id,
                'package_id' => $package1->id,
                'tutor_user_id' => $tutor1->user_id,
                'subject_id' => $subjects[0]->id,
                'remaining_session' => 3,
            ]);

            $studentPackage2 = StudentPackage::create([
                'student_user_id' => $students[1]->id,
                'package_id' => $package1->id,
                'tutor_user_id' => $tutor1->user_id,
                'subject_id' => $subjects[0]->id,
                'remaining_session' => 2,
            ]);

            // Buat schedule tutors untuk tutor 1 (jadwal tersedia)
            $schedules = ScheduleTutor::where('user_id', $tutor1->user_id)->take(3)->get();

            // Buat orders dan payments untuk student packages
            $order1 = Order::create([
                'user_id' => $students[0]->id,
                'package_id' => $package1->id,
                'total_amount' => $package1->price,
                'status' => OrderStatusEnum::PAID->value,
                'created_at' => now()->subMonths(2),
            ]);

            Payment::create([
                'order_id' => $order1->id,
                'amount' => $package1->price,
                'status' => PaymentStatusEnum::VALIDATED->value,
                'payment_method' => PaymentMethodEnum::BANK_MANDIRI->value,
                'created_at' => now()->subMonths(2),
            ]);

            $order2 = Order::create([
                'user_id' => $students[1]->id,
                'package_id' => $package1->id,
                'total_amount' => $package1->price,
                'status' => OrderStatusEnum::PAID->value,
                'created_at' => now()->subMonths(1),
            ]);

            Payment::create([
                'order_id' => $order2->id,
                'amount' => $package1->price,
                'status' => PaymentStatusEnum::VALIDATED->value,
                'payment_method' => PaymentMethodEnum::BANK_BNI->value,
                'created_at' => now()->subMonths(1),
            ]);

            // Set salary untuk tutor 1 (hasil dari mengajar)
            $tutor1->salary = 500000;
            $tutor1->save();

            // ==== TUTOR 2: Salary 750000 ====
            $tutor2 = $tutors[1];
            $package2 = $packages[1] ?? $packages[0];

            $studentPackage3 = StudentPackage::create([
                'student_user_id' => $students[2]->id,
                'package_id' => $package2->id,
                'tutor_user_id' => $tutor2->user_id,
                'subject_id' => $subjects[1]->id,
                'remaining_session' => 5,
            ]);

            $studentPackage4 = StudentPackage::create([
                'student_user_id' => $students[3]->id,
                'package_id' => $package2->id,
                'tutor_user_id' => $tutor2->user_id,
                'subject_id' => $subjects[1]->id,
                'remaining_session' => 4,
            ]);

            $order3 = Order::create([
                'user_id' => $students[2]->id,
                'package_id' => $package2->id,
                'total_amount' => $package2->price,
                'status' => OrderStatusEnum::PAID->value,
                'created_at' => now()->subMonths(3),
            ]);

            Payment::create([
                'order_id' => $order3->id,
                'amount' => $package2->price,
                'status' => PaymentStatusEnum::VALIDATED->value,
                'payment_method' => PaymentMethodEnum::BANK_BRI->value,
                'created_at' => now()->subMonths(3),
            ]);

            $order4 = Order::create([
                'user_id' => $students[3]->id,
                'package_id' => $package2->id,
                'total_amount' => $package2->price,
                'status' => OrderStatusEnum::PAID->value,
                'created_at' => now()->subMonths(2),
            ]);

            Payment::create([
                'order_id' => $order4->id,
                'amount' => $package2->price,
                'status' => PaymentStatusEnum::VALIDATED->value,
                'payment_method' => PaymentMethodEnum::BANK_BPR->value,
                'created_at' => now()->subMonths(2),
            ]);

            $tutor2->salary = 750000;
            $tutor2->save();

            // ==== TUTOR 3: Salary 300000 ====
            $tutor3 = $tutors[2];

            $studentPackage5 = StudentPackage::create([
                'student_user_id' => $students[4]->id,
                'package_id' => $package1->id,
                'tutor_user_id' => $tutor3->user_id,
                'subject_id' => $subjects[0]->id,
                'remaining_session' => 1,
            ]);

            $order5 = Order::create([
                'user_id' => $students[4]->id,
                'package_id' => $package1->id,
                'total_amount' => $package1->price,
                'status' => OrderStatusEnum::PAID->value,
                'created_at' => now()->subWeeks(3),
            ]);

            Payment::create([
                'order_id' => $order5->id,
                'amount' => $package1->price,
                'status' => PaymentStatusEnum::VALIDATED->value,
                'payment_method' => PaymentMethodEnum::BANK_QRIS->value,
                'created_at' => now()->subWeeks(3),
            ]);

            $tutor3->salary = 300000;
            $tutor3->save();

            DB::commit();

            $this->command->info('✅ Pending Payment data berhasil dibuat!');
            $this->command->info('');
            $this->command->info('📊 Data yang dibuat:');
            $this->command->info('  • Tutor 1 (ID: ' . $tutor1->user_id . '): Salary Rp 500.000 - 2 Student packages');
            $this->command->info('  • Tutor 2 (ID: ' . $tutor2->user_id . '): Salary Rp 750.000 - 2 Student packages');
            $this->command->info('  • Tutor 3 (ID: ' . $tutor3->user_id . '): Salary Rp 300.000 - 1 Student package');
            $this->command->info('');
            $this->command->info('Total Pending Salary: Rp ' . number_format(500000 + 750000 + 300000, 0, ',', '.'));
            $this->command->info('');
            $this->command->info('💡 Sekarang testing endpoint:');
            $this->command->info('  GET /api/tutor-salary/pending-payment');
            $this->command->info('  POST /api/tutor-salary/confirm-batch-with-invoice');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
        }
    }
}
