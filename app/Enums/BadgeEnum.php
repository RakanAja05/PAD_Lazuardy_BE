<?php

namespace App\Enums;

enum BadgeEnum: string
{
    case BRONZE = "bronze";
    case SILVER = "silver";
    case GOLD = "gold";

    
    public function displayName() : string 
    {
        return match($this) 
        {
            self::BRONZE => 'Perunggu',
            self::SILVER => 'Perak',
            self::GOLD => 'Emas',
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
