<?php

namespace App\Enums;

enum OtpTypeEnum:string
{
    case REGISTER = 'register';
    case UPDATE_PASSWORD = 'update_password';
    case FORGOT_PASSWORD = 'forgot_password';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::REGISTER => 'Registrasi',
            self::UPDATE_PASSWORD => 'Ubah password',
            self::FORGOT_PASSWORD => 'Lupa password',
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
