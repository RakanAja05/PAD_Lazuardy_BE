<?php

namespace App\Enums;

enum SubjectEnum: string
{
    case MATEMATIKA_SD = 'Matematika SD';
    case MATEMATIKA_SMP = 'Matematika SMP';
    case MATEMATIKA_SMA = 'Matematika SMA';
    case BAHASA_INGGRIS_SD = 'Bahasa Inggris SD';
    case BAHASA_INGGRIS_SMP = 'Bahasa Inggris SMP';
    case BAHASA_INGGRIS_SMA = 'Bahasa Inggris SMA';
    case FISIKA_SMP = 'Fisika SMP';
    case FISIKA_SMA = 'Fisika SMA';
    case KIMIA_SMP = 'Kimia SMP';
    case KIMIA_SMA = 'Kimia SMA';
    case BIOLOGI_SMP = 'Biologi SMP';
    case BIOLOGI_SMA = 'Biologi SMA';
    case EKONOMI_SMP = 'Ekonomi SMP';
    case EKONOMI_SMA = 'Ekonomi SMA';
    case TIK_KOMPUTER_SMA = 'TIK/Komputer SMA';
    case BAHASA_INDONESIA_SD = 'Bahasa Indonesia SD';
    case BAHASA_INDONESIA_SMP = 'Bahasa Indonesia SMP';
    case BAHASA_INDONESIA_SMA = 'Bahasa Indonesia SMA';
    case IPA_TERPADU_SD = 'IPA Terpadu SD';
    case IPS_SD = 'IPS SD';

    public function displayName(): string
    {
        return match($this)
        {
            self::MATEMATIKA_SD => 'Matematika SD',
            self::MATEMATIKA_SMP => 'Matematika SMP',
            self::MATEMATIKA_SMA => 'Matematika SMA',
            self::BAHASA_INGGRIS_SD => 'Bahasa Inggris SD',
            self::BAHASA_INGGRIS_SMP => 'Bahasa Inggris SMP',
            self::BAHASA_INGGRIS_SMA => 'Bahasa Inggris SMA',
            self::FISIKA_SMP => 'Fisika SMP',
            self::FISIKA_SMA => 'Fisika SMA',
            self::KIMIA_SMP => 'Kimia SMP',
            self::KIMIA_SMA => 'Kimia SMA',
            self::BIOLOGI_SMP => 'Biologi SMP',
            self::BIOLOGI_SMA => 'Biologi SMA',
            self::EKONOMI_SMP => 'Ekonomi SMP',
            self::EKONOMI_SMA => 'Ekonomi SMA',
            self::TIK_KOMPUTER_SMA => 'TIK/Komputer SMA',
            self::BAHASA_INDONESIA_SD => 'Bahasa Indonesia SD',
            self::BAHASA_INDONESIA_SMP => 'Bahasa Indonesia SMP',
            self::BAHASA_INDONESIA_SMA => 'Bahasa Indonesia SMA',
            self::IPA_TERPADU_SD => 'IPA Terpadu SD',
            self::IPS_SD => 'IPS SD',
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
