<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';
    case TUTOR = 'tutor';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::ADMIN => 'Admin',
            self::TUTOR => 'Mentor',
            self::STUDENT => 'Siswa'
        };
    }

    public static function list() : array 
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function displayList() : array 
    {
        return array_map(fn($case) => $case->displayName(), self::cases());
    }
}
