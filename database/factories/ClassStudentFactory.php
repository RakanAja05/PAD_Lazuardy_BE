<?php

namespace Database\Factories;

use App\Models\ClassStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassStudentFactory extends Factory
{
    protected $model = ClassStudent::class;

    public function definition(): array
    {
        $name = $this->faker->sentence(2);
        return [
            'name' => $name,
        ];
    }
}
