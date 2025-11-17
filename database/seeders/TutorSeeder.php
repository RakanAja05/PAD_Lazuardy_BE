<?php

namespace Database\Seeders;

use App\Enums\BadgeEnum;
use App\Enums\CourseModeEnum;
use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Enums\RoleEnum;
use App\Enums\TutorStatusEnum;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data tutor dummy dengan variasi untuk testing Find Tutor scoring
        // Lokasi: Semua tutor di sekitar Surabaya dengan jarak bervariasi dari titik pusat (Airlangga)
        // Rating: Akan ditambahkan via ReviewSeeder
        // Subject: Akan ditambahkan via tutor_subjects pivot table
        
        $tutors = [
            // TIER 1: Rating Tinggi (4.5-5.0) + Dekat (1-3 km) → Score tertinggi
            [
                'name' => 'Prof. Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'telephone_number' => '081234567801',
                'gender' => GenderEnum::MAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1985-05-15',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=4F46E5&color=fff',
                'latitude' => -7.2745, // ~0.5 km dari pusat Airlangga
                'longitude' => 112.7544,
                'home_address' => [
                    'street' => 'Jl. Airlangga No. 10',
                    'subdistrict' => 'Airlangga',
                    'district' => 'Gubeng',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S2', 'major' => 'Matematika', 'university' => 'Universitas Airlangga', 'year' => '2010']
                    ],
                    'price' => 150000,
                    'description' => 'Profesor matematika berpengalaman 15 tahun. Spesialis Olimpiade Matematika dan SBMPTN.',
                    'learning_method' => 'Metode problem solving intensif dengan pendekatan konsep dasar yang kuat.',
                    'qualification' => [
                        'Doktor Matematika',
                        'Pelatih Tim Olimpiade Matematika Nasional',
                        'Penulis 5 Buku Matematika'
                    ],
                    'experience' => '15 tahun mengajar matematika SD, SMP, SMA',
                    'organization' => [
                        ['name' => 'Ikatan Guru Matematika Indonesia', 'position' => 'Ketua Cabang Surabaya']
                    ],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::ONLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [1, 2, 3], // Matematika SD, SMP, SMA
                ],
            ],
            
            [
                'name' => 'Dr. Siti Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'telephone_number' => '081234567802',
                'gender' => GenderEnum::WOMAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1988-08-20',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=EC4899&color=fff',
                'latitude' => -7.2755, // ~1 km dari pusat
                'longitude' => 112.7554,
                'home_address' => [
                    'street' => 'Jl. Airlangga No. 25',
                    'subdistrict' => 'Airlangga',
                    'district' => 'Gubeng',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S2', 'major' => 'Pendidikan Bahasa Inggris', 'university' => 'Universitas Negeri Surabaya', 'year' => '2012']
                    ],
                    'price' => 140000,
                    'description' => 'Native-level English teacher dengan sertifikasi internasional.',
                    'learning_method' => 'Conversational approach dengan fokus praktis speaking, listening, reading, writing.',
                    'qualification' => [
                        'TOEFL Score 650',
                        'IELTS 8.5',
                        'Cambridge CELTA Certified',
                        'TOEIC Trainer'
                    ],
                    'experience' => '12 tahun mengajar bahasa Inggris',
                    'organization' => [
                        ['name' => 'English Teachers Association', 'position' => 'Senior Trainer']
                    ],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::ONLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [4, 5, 6], // B. Inggris SD, SMP, SMA
                ],
            ],

            // TIER 2: Rating Tinggi (4.0-4.5) + Jarak Sedang (3-5 km)
            [
                'name' => 'Ahmad Zainudin, M.Sc',
                'email' => 'ahmad.zainudin@example.com',
                'telephone_number' => '081234567803',
                'gender' => GenderEnum::MAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1990-03-10',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Ahmad+Zainudin&background=10B981&color=fff',
                'latitude' => -7.2900, // ~3 km dari pusat
                'longitude' => 112.7700,
                'home_address' => [
                    'street' => 'Jl. Pucang Anom No. 15',
                    'subdistrict' => 'Pucang Sewu',
                    'district' => 'Gubeng',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S2', 'major' => 'Fisika', 'university' => 'Institut Teknologi Sepuluh Nopember', 'year' => '2015']
                    ],
                    'price' => 130000,
                    'description' => 'Pengajar fisika dengan metode pembelajaran yang mudah dipahami.',
                    'learning_method' => 'Demonstrasi praktis dan simulasi untuk memahami konsep fisika.',
                    'qualification' => [
                        'Magister Fisika',
                        'Peneliti BRIN',
                        'Juara Lomba Karya Ilmiah Fisika'
                    ],
                    'experience' => '10 tahun mengajar fisika SMP dan SMA',
                    'organization' => [
                        ['name' => 'Himpunan Fisika Indonesia', 'position' => 'Ketua Cabang Surabaya']
                    ],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::ONLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [7, 8], // Fisika SMP, SMA
                ],
            ],

            [
                'name' => 'Dewi Sartika, S.Pd',
                'email' => 'dewi.sartika@example.com',
                'telephone_number' => '081234567804',
                'gender' => GenderEnum::WOMAN->value,
                'religion' => ReligionEnum::KRISTEN->value,
                'date_of_birth' => '1992-11-05',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Dewi+Sartika&background=F59E0B&color=fff',
                'latitude' => -7.3000, // ~4 km dari pusat
                'longitude' => 112.7400,
                'home_address' => [
                    'street' => 'Jl. Ketintang Baru No. 8',
                    'subdistrict' => 'Ketintang',
                    'district' => 'Gayungan',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S1', 'major' => 'Kimia', 'university' => 'Universitas Surabaya', 'year' => '2014']
                    ],
                    'price' => 110000,
                    'description' => 'Tutor kimia yang sabar dan ramah untuk siswa SMP dan SMA.',
                    'learning_method' => 'Pembelajaran konsep dengan banyak latihan soal dan praktikum sederhana.',
                    'qualification' => [
                        'Lulusan Terbaik Kimia',
                        'Asisten Lab Kimia 4 tahun'
                    ],
                    'experience' => '8 tahun mengajar kimia',
                    'organization' => [],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::OFFLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [9, 10], // Kimia SMP, SMA
                ],
            ],

            // TIER 3: Rating Sedang (3.5-4.0) + Dekat (1-3 km) atau Rating Tinggi + Jauh (6-8 km)
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono@example.com',
                'telephone_number' => '081234567805',
                'gender' => GenderEnum::MAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1991-07-12',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Rudi+Hartono&background=8B5CF6&color=fff',
                'latitude' => -7.2765, // ~2 km dari pusat
                'longitude' => 112.7500,
                'home_address' => [
                    'street' => 'Jl. Dukuh Pakis No. 50',
                    'subdistrict' => 'Dukuh Pakis',
                    'district' => 'Dukuh Pakis',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S1', 'major' => 'Biologi', 'university' => 'Universitas Airlangga', 'year' => '2013']
                    ],
                    'price' => 100000,
                    'description' => 'Pengajar biologi dengan pendekatan pembelajaran yang menyenangkan.',
                    'learning_method' => 'Visual learning dengan gambar, video, dan demonstrasi.',
                    'qualification' => [
                        'Sertifikat Pengajar Biologi',
                        'Peneliti Biodiversitas'
                    ],
                    'experience' => '7 tahun mengajar biologi SMP dan SMA',
                    'organization' => [
                        ['name' => 'Perhimpunan Biologi Indonesia', 'position' => 'Anggota']
                    ],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::OFFLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [11, 12], // Biologi SMP, SMA
                ],
            ],

            [
                'name' => 'Linda Wijaya, S.E',
                'email' => 'linda.wijaya@example.com',
                'telephone_number' => '081234567806',
                'gender' => GenderEnum::WOMAN->value,
                'religion' => ReligionEnum::BUDDHA->value,
                'date_of_birth' => '1993-09-25',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Linda+Wijaya&background=EF4444&color=fff',
                'latitude' => -7.3200, // ~7 km dari pusat
                'longitude' => 112.8000,
                'home_address' => [
                    'street' => 'Jl. Keputih Tegal No. 22',
                    'subdistrict' => 'Keputih',
                    'district' => 'Sukolilo',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S1', 'major' => 'Ekonomi', 'university' => 'Universitas Airlangga', 'year' => '2015']
                    ],
                    'price' => 95000,
                    'description' => 'Tutor ekonomi dan akuntansi untuk SMA dan persiapan SBMPTN.',
                    'learning_method' => 'Studi kasus nyata dan latihan soal ujian.',
                    'qualification' => [
                        'Akuntan Publik Bersertifikat',
                        'Trainer Akuntansi'
                    ],
                    'experience' => '6 tahun mengajar ekonomi dan akuntansi',
                    'organization' => [],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::ONLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [13, 14], // Ekonomi SMP, SMA
                ],
            ],

            // TIER 4: Rating Sedang (3.0-3.5) + Jarak Jauh (8-10 km)
            [
                'name' => 'Agus Prasetyo',
                'email' => 'agus.prasetyo@example.com',
                'telephone_number' => '081234567807',
                'gender' => GenderEnum::MAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1989-12-18',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Agus+Prasetyo&background=06B6D4&color=fff',
                'latitude' => -7.3400, // ~9 km dari pusat
                'longitude' => 112.7850,
                'home_address' => [
                    'street' => 'Jl. Tenggilis Mejoyo No. 30',
                    'subdistrict' => 'Tenggilis Mejoyo',
                    'district' => 'Tenggilis Mejoyo',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S1', 'major' => 'Teknik Informatika', 'university' => 'Institut Teknologi Sepuluh Nopember', 'year' => '2011']
                    ],
                    'price' => 120000,
                    'description' => 'Pengajar programming dan komputer untuk pemula hingga advanced.',
                    'learning_method' => 'Project-based learning dengan praktik langsung.',
                    'qualification' => [
                        'Certified Python Developer',
                        'Web Developer Professional'
                    ],
                    'experience' => '9 tahun mengajar programming',
                    'organization' => [
                        ['name' => 'Indonesia Developer Community', 'position' => 'Mentor']
                    ],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::ONLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [15], // TIK/Komputer SMA
                ],
            ],

            [
                'name' => 'Rina Kusuma',
                'email' => 'rina.kusuma@example.com',
                'telephone_number' => '081234567808',
                'gender' => GenderEnum::WOMAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1994-04-30',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Rina+Kusuma&background=A855F7&color=fff',
                'latitude' => -7.3100, // ~5 km dari pusat
                'longitude' => 112.7300,
                'home_address' => [
                    'street' => 'Jl. Wonokromo No. 45',
                    'subdistrict' => 'Wonokromo',
                    'district' => 'Wonokromo',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S1', 'major' => 'Bahasa Indonesia', 'university' => 'Universitas Negeri Surabaya', 'year' => '2016']
                    ],
                    'price' => 85000,
                    'description' => 'Tutor bahasa Indonesia untuk SD hingga SMA.',
                    'learning_method' => 'Diskusi dan analisis teks dengan latihan menulis.',
                    'qualification' => [
                        'Juara Lomba Karya Tulis',
                        'Editor Buku Pelajaran'
                    ],
                    'experience' => '5 tahun mengajar bahasa Indonesia',
                    'organization' => [],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::OFFLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [16, 17, 18], // B. Indonesia SD, SMP, SMA
                ],
            ],

            // TIER 5: Rating Rendah (2.5-3.0) - untuk testing filter min_rating
            [
                'name' => 'Bambang Wijaya',
                'email' => 'bambang.wijaya@example.com',
                'telephone_number' => '081234567809',
                'gender' => GenderEnum::MAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1996-06-15',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Bambang+Wijaya&background=64748B&color=fff',
                'latitude' => -7.2750, // ~1 km (dekat tapi rating rendah)
                'longitude' => 112.7560,
                'home_address' => [
                    'street' => 'Jl. Airlangga No. 88',
                    'subdistrict' => 'Airlangga',
                    'district' => 'Gubeng',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S1', 'major' => 'Pendidikan Matematika', 'university' => 'Universitas Terbuka', 'year' => '2018']
                    ],
                    'price' => 70000,
                    'description' => 'Tutor matematika SD dan SMP. Masih belajar meningkatkan metode mengajar.',
                    'learning_method' => 'Pembelajaran dasar dengan banyak latihan.',
                    'qualification' => [
                        'Fresh Graduate'
                    ],
                    'experience' => '2 tahun mengajar matematika SD dan SMP',
                    'organization' => [],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::OFFLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [1, 2], // Matematika SD, SMP
                ],
            ],

            // TIER 6: Rating Tinggi + Multi-Subject (untuk test filter)
            [
                'name' => 'Maya Kusuma, M.Pd',
                'email' => 'maya.kusuma@example.com',
                'telephone_number' => '081234567810',
                'gender' => GenderEnum::WOMAN->value,
                'religion' => ReligionEnum::ISLAM->value,
                'date_of_birth' => '1987-02-20',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Maya+Kusuma&background=F97316&color=fff',
                'latitude' => -7.2800, // ~2.5 km dari pusat
                'longitude' => 112.7600,
                'home_address' => [
                    'street' => 'Jl. Manyar No. 12',
                    'subdistrict' => 'Manyar Sabrangan',
                    'district' => 'Mulyorejo',
                    'regency' => 'Surabaya',
                    'province' => 'Jawa Timur',
                ],
                'tutor' => [
                    'education' => [
                        ['degree' => 'S2', 'major' => 'Pendidikan', 'university' => 'Universitas Negeri Surabaya', 'year' => '2012']
                    ],
                    'price' => 160000,
                    'description' => 'Tutor berpengalaman untuk semua mata pelajaran SD. Spesialis persiapan ujian.',
                    'learning_method' => 'Metode belajar menyenangkan dengan games dan aktivitas interaktif.',
                    'qualification' => [
                        'Magister Pendidikan',
                        'Juara Guru Berprestasi Nasional 2020',
                        'Penulis Buku Panduan Belajar SD'
                    ],
                    'experience' => '13 tahun mengajar SD semua pelajaran',
                    'organization' => [
                        ['name' => 'Ikatan Guru Indonesia', 'position' => 'Pengurus Wilayah']
                    ],
                    'badge' => BadgeEnum::AMATEUR->value,
                    'course_mode' => CourseModeEnum::ONLINE->value,
                    'status' => TutorStatusEnum::ACTIVE->value,
                    'subjects' => [1, 4, 16], // Matematika SD, B. Inggris SD, B. Indonesia SD (multi-subject)
                ],
            ],
        ];

        foreach ($tutors as $tutorData) {
            $tutorInfo = $tutorData['tutor'];
            $subjectIds = $tutorInfo['subjects'] ?? [];
            unset($tutorData['tutor']);
            unset($tutorInfo['subjects']);

            // Create user
            $user = User::create([
                'name' => $tutorData['name'],
                'email' => $tutorData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // Default password
                'role' => RoleEnum::TUTOR->value,
                'telephone_number' => $tutorData['telephone_number'],
                'telephone_verified_at' => now(),
                'profile_photo_url' => $tutorData['profile_photo_url'],
                'date_of_birth' => $tutorData['date_of_birth'],
                'gender' => $tutorData['gender'],
                'religion' => $tutorData['religion'],
                'home_address' => $tutorData['home_address'],
                'latitude' => $tutorData['latitude'],
                'longitude' => $tutorData['longitude'],
            ]);

            // Create tutor
            $tutor = Tutor::create([
                'user_id' => $user->id,
                'education' => $tutorInfo['education'],
                'price' => $tutorInfo['price'],
                'description' => $tutorInfo['description'],
                'learning_method' => $tutorInfo['learning_method'],
                'qualification' => $tutorInfo['qualification'],
                'experience' => $tutorInfo['experience'],
                'organization' => $tutorInfo['organization'],
                'badge' => $tutorInfo['badge'],
                'course_mode' => $tutorInfo['course_mode'],
                'status' => $tutorInfo['status'],
                'salary' => 0,
                'sanction_amount' => 0,
            ]);

            // Attach subjects to tutor (pivot table)
            if (!empty($subjectIds)) {
                $tutor->subjects()->attach($subjectIds);
            }
        }

        $this->command->info('✅ Berhasil membuat ' . count($tutors) . ' tutor dummy untuk testing Find Tutor!');
        $this->command->info('📍 Lokasi: Bervariasi dari 0.5 km - 9 km dari pusat Airlangga, Surabaya');
        $this->command->info('⭐ Rating: Akan ditambahkan via ReviewSeeder (2.5 - 5.0 bintang)');
        $this->command->info('📚 Subject: Sudah di-attach ke tutor_subjects pivot table');
        $this->command->info('📧 Email: budi.santoso@example.com, siti.nurhaliza@example.com, dst...');
        $this->command->info('🔑 Password: password123');
    }
}
