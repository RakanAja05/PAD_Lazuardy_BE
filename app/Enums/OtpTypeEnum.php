<?php

namespace App\Enums;

enum OtpTypeEnum:string
{
    case EMAIL = 'email';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::EMAIL => 'Email',
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
