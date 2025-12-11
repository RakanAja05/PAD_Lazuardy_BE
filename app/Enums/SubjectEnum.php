<?php

namespace App\Enums;

enum SubjectEnum: String
{
    case MATEMATIKA = 'matematika';
    case FISIKA = 'fisika';
    case KIMIA = 'kimia';
    case BIOLOGI = 'biologi';
    case BAHASA_INDONESIA = 'bahasa indonesia';
    case BAHASA_INGGRIS = 'bahasa inggris';
    case SEJARAH_INDONESIA = 'sejarah indonesia';
    case EKONOMI = 'ekonomi';
    case SOSIOLOGI = 'sosiologi';
    case GEOGRAFI = 'geografi';
    case INFORMATIKA = 'informatika';
    case AGAMA_ISLAM = 'agama islam';
    case MENGAJI = 'mengaji';

    public function displayName(): string
    {
        return match($this)
        {
            self::MATEMATIKA => 'Matematika',
            self::FISIKA => 'Fisika',
            self::KIMIA => 'Kimia',
            self::BIOLOGI => 'Biologi',
            self::BAHASA_INDONESIA => 'Bahasa Indonesia',
            self::BAHASA_INGGRIS => 'Bahasa Inggris',
            self::SEJARAH_INDONESIA => 'Sejarah Indonesia',
            self::EKONOMI => 'Ekonomi',
            self::SOSIOLOGI => 'Sosiologi',
            self::GEOGRAFI => 'Geografi',
            self::INFORMATIKA => 'Informatika',
            self::AGAMA_ISLAM => 'Agama Islam',
            self::MENGAJI => 'Mengaji',
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
