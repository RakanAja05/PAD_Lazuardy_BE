<?php

namespace Database\Factories;

use App\Models\ClassModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassStudentFactory extends Factory
{
    protected $model = ClassModel::class;

    public function definition(): array
    {
        $name = $this->faker->sentence(2);
        return [
            'name' => $name,
        ];
    }
}
