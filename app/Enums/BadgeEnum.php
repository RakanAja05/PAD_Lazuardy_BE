<?php

namespace App\Enums;

enum BadgeEnum: string
{
    case AMATEUR = "pemula";

    
    public function displayName() : string 
    {
        return match($this) 
        {
        self::AMATEUR => 'Pemula'
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
