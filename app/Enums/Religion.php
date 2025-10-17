<?php

namespace App\Enums;

enum Religion: string
{
    case ISLAM = 'islam';
    case KRISTEN = 'kristen';
    case KATOLIK = 'katolik';
    case HINDU = 'hindu';
    case BUDDHA = 'buddha';
    case KONGHUCU = 'konghucu';

    
    public static function displayName(string $value): string
    {
        return match ($value) {
            self::ISLAM->value => 'Islam',
            self::KRISTEN->value => 'Kristen',
            self::KATOLIK->value => 'Katolik',
            self::HINDU->value => 'Hindu',
            self::BUDDHA->value => 'Buddha',
            self::KONGHUCU->value => 'Konghucu',
        };
    }
    
    public static function list(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
    public static function displayList(): array
    {
        return array_map(fn($case) => $case->displayName($case->value), self::cases());
    }


}
