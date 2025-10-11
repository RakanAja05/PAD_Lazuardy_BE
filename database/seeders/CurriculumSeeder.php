<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $curriculums = [
            ['name' => 'ktsp-13'],
            ['name' => 'merdeka'],
        ];
        
        Curriculum::insert($curriculums);
    }
}
