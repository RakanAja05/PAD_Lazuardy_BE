<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\Curriculum;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->sentence();
        $icon_image_url = $this->faker->sentence();
        $class_id = ClassModel::pluck('id')->random();
        $curriculum_id = Curriculum::pluck('id')->random();
        
        return [
            'name' => $name,
            'icon_image_url' => $icon_image_url,
            'class_id' => $class_id,
            'curriculum_id' => $curriculum_id,
        ];
    }
}
