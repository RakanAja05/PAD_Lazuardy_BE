<?php

namespace App\Enums;

enum SubjectApplicationEnum: string
{
    case VERIFY = 'verify';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::VERIFY => 'Menunggu konfirmasi',
            self::ACCEPTED => 'Diterima',
            self::REJECTED => 'Ditolak',
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
