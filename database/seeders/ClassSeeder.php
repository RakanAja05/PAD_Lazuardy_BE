<?php

namespace Database\Seeders;

use App\Enums\ClassEnum;
use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = array_map(
            fn($name) => ['name' => $name],
            ClassEnum::list()
        );

        ClassModel::insert($classes);
    }
}
