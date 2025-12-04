<?php

namespace App\Enums;

enum GenderEnum: string
{
    case MAN = 'male';
    case WOMAN = 'female';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::MAN => 'Laki-laki',
            self::WOMAN => 'Perempuan',
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