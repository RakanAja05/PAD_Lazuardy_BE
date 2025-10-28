<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;

class StudentService
{
    /**
     * Create a new class instance.
     */
    public function showStudentProfile(Student $query)
    {
        $data = [
            'school' => $query->school,
            'class' => $query->class->name,
            'curriculum' => $query->curriculum->name,
            'parent' => $query->parent,
            'parent_telephone_number' => $query->parent_telephone_number
        ];

        return $data;
    }
}
