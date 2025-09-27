<?php

namespace App\Enums;

enum Day: string
{
    case SUNDAY = 'minggu';
    case MONDAY = 'senin';
    case TUESDAY = 'selasa';
    case WEDNESDAY = 'rabu';
    case THURSDAY = 'kamis';
    case FRIDAY = 'jumat';
    case SATURDAY = 'sabtu';

    public function label() : string 
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
}
