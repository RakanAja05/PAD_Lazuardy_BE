<?php

namespace App\Enums;

enum RatingOptionEnum: string
{
    case VERY_GOOD = 'sangat baik';
    case GOOD = 'baik';
    case ENOUGH = 'cukup';
    case BAD = 'buruk';
    case VERY_BAD = 'sangat buruk';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::VERY_GOOD => 'Sangat Baik',
            self::GOOD => 'Baik',
            self::ENOUGH => 'Cukup',
            self::BAD => 'Buruk',
            self::VERY_BAD => "Sangat Buruk"
        };
    }
    
    public static function tryFromDisplayName(string $displayName): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->displayName() === $displayName) {
                return $case;
            }
        }
        return null;
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
