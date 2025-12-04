<?php

namespace App\Enums;

enum DayEnum: string
{
    case SUNDAY = 'minggu';
    case MONDAY = 'senin';
    case TUESDAY = 'selasa';
    case WEDNESDAY = 'rabu';
    case THURSDAY = 'kamis';
    case FRIDAY = 'jumat';
    case SATURDAY = 'sabtu';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::SUNDAY => 'Minggu',
            self::MONDAY => 'Senin',
            self::TUESDAY => 'Selasa',
            self::WEDNESDAY => 'Rabu',
            self::THURSDAY => 'Kamis',
            self::FRIDAY => 'Jumat',
            self::SATURDAY => 'Sabtu'
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
