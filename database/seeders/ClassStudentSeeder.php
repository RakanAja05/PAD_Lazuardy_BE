<?php

namespace Database\Seeders;

use App\Models\ClassStudent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassStudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [];

        for ($i=1; $i<=6; $i++){
            $classes[] = ['name' => 'kelas ' . $i];
        }

        ClassStudent::insert($classes);
    }
}
