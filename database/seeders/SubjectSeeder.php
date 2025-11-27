<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Default: Kurikulum Merdeka (ID 2)
        $subjects = [
            // Matematika (ID 1-3)
            ['name' => 'Matematika SD', 'curriculum_id' => 2, 'class_id' => 1], // ID 1
            ['name' => 'Matematika SMP', 'curriculum_id' => 2, 'class_id' => 2], // ID 2
            ['name' => 'Matematika SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 3
            
            // Bahasa Inggris (ID 4-6)
            ['name' => 'Bahasa Inggris SD', 'curriculum_id' => 2, 'class_id' => 1], // ID 4
            ['name' => 'Bahasa Inggris SMP', 'curriculum_id' => 2, 'class_id' => 2], // ID 5
            ['name' => 'Bahasa Inggris SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 6
            
            // Fisika (ID 7-8)
            ['name' => 'Fisika SMP', 'curriculum_id' => 2, 'class_id' => 2], // ID 7
            ['name' => 'Fisika SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 8
            
            // Kimia (ID 9-10)
            ['name' => 'Kimia SMP', 'curriculum_id' => 2, 'class_id' => 2], // ID 9
            ['name' => 'Kimia SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 10
            
            // Biologi (ID 11-12)
            ['name' => 'Biologi SMP', 'curriculum_id' => 2, 'class_id' => 2], // ID 11
            ['name' => 'Biologi SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 12
            
            // Ekonomi (ID 13-14)
            ['name' => 'Ekonomi SMP', 'curriculum_id' => 2, 'class_id' => 2], // ID 13
            ['name' => 'Ekonomi SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 14
            
            // TIK/Komputer (ID 15)
            ['name' => 'TIK/Komputer SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 15
            
            // Bahasa Indonesia (ID 16-18)
            ['name' => 'Bahasa Indonesia SD', 'curriculum_id' => 2, 'class_id' => 1], // ID 16
            ['name' => 'Bahasa Indonesia SMP', 'curriculum_id' => 2, 'class_id' => 2], // ID 17
            ['name' => 'Bahasa Indonesia SMA', 'curriculum_id' => 2, 'class_id' => 3], // ID 18
            
            // IPA Terpadu SD (ID 19)
            ['name' => 'IPA Terpadu SD', 'curriculum_id' => 2, 'class_id' => 1], // ID 19
            
            // IPS (ID 20)
            ['name' => 'IPS SD', 'curriculum_id' => 2, 'class_id' => 1], // ID 20
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
