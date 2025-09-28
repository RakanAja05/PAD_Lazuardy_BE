<?php

namespace App\Enums;

enum FileType: string
{
    case IJAZAH = 'ijazah';
    case CERTIFICATE = 'sertifikat';

    public function label() : string 
    {
        return match($this) 
        {
            self::IJAZAH => 'ijazah',
            self::CERTIFICATE => 'sertifikat',
        };
    }
}
