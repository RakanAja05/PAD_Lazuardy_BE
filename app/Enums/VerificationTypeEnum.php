<?php

namespace App\Enums;

enum VerificationTypeEnum: string
{
    case REGISTER = 'register';
    case UPDATE_PASSWORD = 'update_password';
    case FORGOT_PASSWORD = 'forgot_password';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::REGISTER => 'Registrasi',
            self::UPDATE_PASSWORD => 'Ubah kata sandi',
            self::FORGOT_PASSWORD => 'Lupa kata sandi',
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
