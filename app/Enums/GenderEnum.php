<?php

namespace App\Enums;

enum GenderEnum: string
{
    case MAN = 'pria';
    case WOMAN = 'wanita';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::MAN => 'Laki-laki',
            self::WOMAN => 'Perempuan',
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