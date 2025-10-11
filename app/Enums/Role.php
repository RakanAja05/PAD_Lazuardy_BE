<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';
    case TUTOR = 'tutor';

    public function label() : string 
    {
        return match($this) 
        {
            self::ADMIN => 'Admin',
            self::TUTOR => 'Mentor',
            self::STUDENT => 'Siswa'
        };
    }
}
