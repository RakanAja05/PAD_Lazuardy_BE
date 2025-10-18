<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [];

        for ($i=1; $i<=6; $i++){
            $classes[] = ['name' => 'kelas ' . $i];
        }

        ClassModel::insert($classes);
    }
}
