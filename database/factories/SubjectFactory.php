<?php

namespace Database\Factories;

use App\Models\ClassStudent;
use App\Models\Curriculum;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->sentence();
        $icon_image_url = $this->faker->sentence();
        $class_id = ClassStudent::pluck('id')->random();
        $major_id = Major::pluck('id')->random();
        $curriculum_id = Curriculum::pluck('id')->random();
        
        return [
            'name' => $name,
            'icon_image_url' => $icon_image_url,
            'class_id' => $class_id,
            'major_id' => $major_id,
            'curriculum_id' => $curriculum_id,
        ];
    }
}
