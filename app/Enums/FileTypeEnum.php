<?php

namespace App\Enums;

enum FileTypeEnum: string
{
    case IJAZAH = 'ijazah';
    case KTP = 'ktp';
    case CV = 'cv';
    case PORTOFOLIO = 'portofolio';
    case CERTIFICATE = 'sertifikat';

    public function displayName() : string 
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
