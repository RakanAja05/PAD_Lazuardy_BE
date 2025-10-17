<?php

namespace App\Enums;

enum VerificationType: string
{
    case REGISTRATION = 'registrasi';
    case RESET_PASSWORD = 'reset_password';
    case FORGET_PASSWORD = 'lupa_password';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::REGISTRATION => 'Registrasi',
            self::RESET_PASSWORD => 'Reset password',
            self::FORGET_PASSWORD => 'Lupa password',
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
