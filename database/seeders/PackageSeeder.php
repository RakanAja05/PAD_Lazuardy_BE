<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Paket 1 Mapel',
                'session' => 8,
                'price' => 400000,
                'discount' => 0,
                'description' => 'Paket belajar untuk 1 mata pelajaran dengan 8 sesi pertemuan',
                'benefit' => json_encode([
                    '8 sesi pertemuan',
                    '1 mata pelajaran',
                    'Materi pembelajaran lengkap',
                    'Konsultasi via chat'
                ]),
                'image_url' => 'https://example.com/package-1-subject.jpg',
                'subject_amount' => 1,
            ],
            [
                'name' => 'Paket 2 Mapel',
                'session' => 8,
                'price' => 750000,
                'discount' => 5,
                'description' => 'Paket belajar untuk 2 mata pelajaran dengan 8 sesi per mapel',
                'benefit' => json_encode([
                    '8 sesi per mata pelajaran (total 16 sesi)',
                    '2 mata pelajaran',
                    'Materi pembelajaran lengkap',
                    'Konsultasi via chat',
                    'Diskon 5%'
                ]),
                'image_url' => 'https://example.com/package-2-subjects.jpg',
                'subject_amount' => 2,
            ],
            [
                'name' => 'Paket 3 Mapel',
                'session' => 8,
                'price' => 1080000,
                'discount' => 10,
                'description' => 'Paket belajar untuk 3 mata pelajaran dengan 8 sesi per mapel',
                'benefit' => json_encode([
                    '8 sesi per mata pelajaran (total 24 sesi)',
                    '3 mata pelajaran',
                    'Materi pembelajaran lengkap',
                    'Konsultasi via chat & video call',
                    'Progress report',
                    'Diskon 10%'
                ]),
                'image_url' => 'https://example.com/package-3-subjects.jpg',
                'subject_amount' => 3,
            ],
            [
                'name' => 'Paket 4 Mapel',
                'session' => 8,
                'price' => 1400000,
                'discount' => 12,
                'description' => 'Paket belajar untuk 4 mata pelajaran dengan 8 sesi per mapel',
                'benefit' => json_encode([
                    '8 sesi per mata pelajaran (total 32 sesi)',
                    '4 mata pelajaran',
                    'Materi pembelajaran lengkap',
                    'Konsultasi via chat & video call',
                    'Progress report',
                    'Try out gratis',
                    'Diskon 12%'
                ]),
                'image_url' => 'https://example.com/package-4-subjects.jpg',
                'subject_amount' => 4,
            ],
            [
                'name' => 'Paket 5 Mapel',
                'session' => 8,
                'price' => 1700000,
                'discount' => 15,
                'description' => 'Paket belajar untuk 5 mata pelajaran dengan 8 sesi per mapel',
                'benefit' => json_encode([
                    '8 sesi per mata pelajaran (total 40 sesi)',
                    '5 mata pelajaran',
                    'Materi pembelajaran lengkap',
                    'Konsultasi via chat & video call',
                    'Progress report mingguan',
                    'Try out unlimited',
                    'Prioritas support',
                    'Diskon 15%'
                ]),
                'image_url' => 'https://example.com/package-5-subjects.jpg',
                'subject_amount' => 5,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
