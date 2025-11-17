<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Curriculum;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data class dan curriculum
        $classes = ClassModel::all();
        $curriculums = Curriculum::all();

        if ($classes->isEmpty() || $curriculums->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada data class atau curriculum. Jalankan ClassSeeder dan CurriculumSeeder terlebih dahulu.');
            return;
        }

        // Data dummy students
        $students = [
            [
                'user' => [
                    'name' => 'Andi Wijaya',
                    'email' => 'andi.student@gmail.com',
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'gender' => 'pria',
                    'date_of_birth' => '2007-05-15',
                    'telephone_number' => '081234567890',
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=Andi+Wijaya',
                    'home_address' => json_encode([
                        'province' => 'Jawa Timur',
                        'regency' => 'Surabaya',
                        'district' => 'Gubeng',
                        'subdistrict' => 'Airlangga',
                        'street' => 'Jl. Airlangga No. 10',
                    ]),
                    'latitude' => -7.2699803,
                    'longitude' => 112.7520883,
                ],
                'student' => [
                    'school' => 'SMA Negeri 1 Surabaya',
                    'parent' => 'Ibu Siti Aisyah',
                    'parent_telephone_number' => '082345678901',
                ],
            ],
            [
                'user' => [
                    'name' => 'Budi Santoso',
                    'email' => 'budi.student@gmail.com',
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'gender' => 'pria',
                    'date_of_birth' => '2008-08-20',
                    'telephone_number' => '085678901234',
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=Budi+Santoso',
                    'home_address' => json_encode([
                        'province' => 'Jawa Timur',
                        'regency' => 'Surabaya',
                        'district' => 'Tegalsari',
                        'subdistrict' => 'Kedungdoro',
                        'street' => 'Jl. Kedungdoro No. 123',
                    ]),
                    'latitude' => -7.2656783,
                    'longitude' => 112.7384693,
                ],
                'student' => [
                    'school' => 'SMP Negeri 5 Surabaya',
                    'parent' => 'Bapak Ahmad Hidayat',
                    'parent_telephone_number' => '081987654321',
                ],
            ],
            [
                'user' => [
                    'name' => 'Citra Dewi',
                    'email' => 'citra.student@gmail.com',
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'gender' => 'wanita',
                    'date_of_birth' => '2006-12-10',
                    'telephone_number' => '089012345678',
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=Citra+Dewi',
                    'home_address' => json_encode([
                        'province' => 'Jawa Timur',
                        'regency' => 'Surabaya',
                        'district' => 'Rungkut',
                        'subdistrict' => 'Kalirungkut',
                        'street' => 'Jl. Rungkut Asri No. 45',
                    ]),
                    'latitude' => -7.3207283,
                    'longitude' => 112.7647693,
                ],
                'student' => [
                    'school' => 'SMA Negeri 5 Surabaya',
                    'parent' => 'Ibu Dewi Sartika',
                    'parent_telephone_number' => '087654321098',
                ],
            ],
            [
                'user' => [
                    'name' => 'Dika Pratama',
                    'email' => 'dika.student@gmail.com',
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'gender' => 'pria',
                    'date_of_birth' => '2007-03-25',
                    'telephone_number' => '081122334455',
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=Dika+Pratama',
                    'home_address' => json_encode([
                        'province' => 'Jawa Timur',
                        'regency' => 'Surabaya',
                        'district' => 'Wonokromo',
                        'subdistrict' => 'Darmo',
                        'street' => 'Jl. Darmo Permai No. 88',
                    ]),
                    'latitude' => -7.2848283,
                    'longitude' => 112.7318693,
                ],
                'student' => [
                    'school' => 'SMA Kristen Petra 1',
                    'parent' => 'Bapak Pratama Wijaya',
                    'parent_telephone_number' => '085566778899',
                ],
            ],
            [
                'user' => [
                    'name' => 'Eka Putri',
                    'email' => 'eka.student@gmail.com',
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'gender' => 'wanita',
                    'date_of_birth' => '2008-06-18',
                    'telephone_number' => '082233445566',
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=Eka+Putri',
                    'home_address' => json_encode([
                        'province' => 'Jawa Timur',
                        'regency' => 'Surabaya',
                        'district' => 'Sukolilo',
                        'subdistrict' => 'Keputih',
                        'street' => 'Jl. ITS Raya No. 21',
                    ]),
                    'latitude' => -7.2827283,
                    'longitude' => 112.7950693,
                ],
                'student' => [
                    'school' => 'SMP Negeri 12 Surabaya',
                    'parent' => 'Ibu Putri Handayani',
                    'parent_telephone_number' => '089988776655',
                ],
            ],
        ];

        $createdCount = 0;
        foreach ($students as $data) {
            // Create user
            $user = User::create($data['user']);

            // Create student dengan random class dan curriculum
            Student::create([
                'user_id' => $user->id,
                'class_id' => $classes->random()->id,
                'curriculum_id' => $curriculums->random()->id,
                'school' => $data['student']['school'],
                'parent' => $data['student']['parent'],
                'parent_telephone_number' => $data['student']['parent_telephone_number'],
            ]);

            $createdCount++;
        }

        $this->command->info('✅ Berhasil membuat ' . $createdCount . ' student dummy!');
        $this->command->info('📧 Email: andi.student@gmail.com, budi.student@gmail.com, dll');
        $this->command->info('🔑 Password: password123');
    }
}
