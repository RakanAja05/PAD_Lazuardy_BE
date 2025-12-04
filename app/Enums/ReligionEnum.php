<?php

namespace App\Enums;

enum ReligionEnum: string
{
    case ISLAM = 'islam';
    case KRISTEN = 'kristen';
    case KATOLIK = 'katolik';
    case HINDU = 'hindu';
    case BUDDHA = 'buddha';
    case KONGHUCU = 'konghucu';

    
    public function displayName() : string 
    {
        return match($this) 
        {
            self::ISLAM->value => 'Islam',
            self::KRISTEN->value => 'Kristen',
            self::KATOLIK->value => 'Katolik',
            self::HINDU->value => 'Hindu',
            self::BUDDHA->value => 'Buddha',
            self::KONGHUCU->value => 'Konghucu',
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

    public static function list(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
    public static function displayList(): array
    {
        return array_map(fn($case) => $case->displayName($case->value), self::cases());
    }


}
