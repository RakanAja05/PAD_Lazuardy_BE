<?php

namespace App\Enums;

enum Gender: string
{
    case MAN = 'pria';
    case WOMAN = 'wanita';

    public function label() : string 
    {
        return match($this) 
        {
            self::MAN => 'Laki-laki',
            self::WOMAN => 'Perempuan',
        };
    }
}