<?php

namespace App\Enums;

enum VerificationType: string
{
    case REGISTRATION = 'registrasi';
    case RESET_PASSWORD = 'reset_password';
    case FORGET_PASSWORD = 'lupa_password';

    public function label() : string 
    {
        return match($this) 
        {
            self::REGISTRATION => 'Registrasi',
            self::RESET_PASSWORD => 'Reset password',
            self::FORGET_PASSWORD => 'Lupa password',
        };
    }
}
