<?php

namespace App\Enums;

enum FileType: string
{
    case IJAZAH = 'ijazah';
    case KTP = 'ktp';
    case CV = 'cv';
    case PORTOFOLIO = 'portofolio';
    case CERTIFICATE = 'sertifikat';

    public function label() : string 
    {
        return match($this) 
        {
            self::IJAZAH => 'Ijazah',
            self::KTP => 'KTP',
            self::CV => 'CV',
            self::PORTOFOLIO => 'Portofolio',
            self::CERTIFICATE => 'Sertifikat',
        };
    }
}
