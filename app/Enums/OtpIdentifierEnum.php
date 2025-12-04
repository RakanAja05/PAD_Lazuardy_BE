<?php

namespace App\Enums;

enum OtpIdentifierEnum: string
{
    case EMAIL = 'email';
    
    public function displayName() : string 
    {
        return match($this) 
        {
            self::EMAIL => 'Email',
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
