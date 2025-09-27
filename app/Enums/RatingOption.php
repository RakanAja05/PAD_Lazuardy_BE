<?php

namespace App\Enums;

enum RatingOption: string
{
    case VERY_GOOD = 'sangat baik';
    case GOOD = 'baik';
    case ENOUGH = 'cukup';
    case BAD = 'buruk';
    case VERY_BAD = 'sangat buruk';

    public function label() : string 
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
}
