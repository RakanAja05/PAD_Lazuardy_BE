<?php

namespace App\Enums;

use PhpParser\Node\Stmt\Label;

enum ClassEnum: String
{
    case KELAS_1 = 'kelas 1';
    case KELAS_2 = 'kelas 2';
    case KELAS_3 = 'kelas 3';
    case KELAS_4 = 'kelas 4';
    case KELAS_5 = 'kelas 5';
    case KELAS_6 = 'kelas 6';
    case KELAS_7 = 'kelas 7';
    case KELAS_8 = 'kelas 8';
    case KELAS_9 = 'kelas 9';
    case KELAS_10 = 'kelas 10';
    case KELAS_11 = 'kelas 11';
    case KELAS_12 = 'kelas 12';
    case UMUM = 'umum';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::KELAS_1 => 'Kelas 1',
            self::KELAS_2 => 'Kelas 2',
            self::KELAS_3 => 'Kelas 3',
            self::KELAS_4 => 'Kelas 4',
            self::KELAS_5 => 'Kelas 5',
            self::KELAS_6 => 'Kelas 6',
            self::KELAS_7 => 'Kelas 7',
            self::KELAS_8 => 'Kelas 8',
            self::KELAS_9 => 'Kelas 9',
            self::KELAS_10 => 'Kelas 10',
            self::KELAS_11 => 'Kelas 11',
            self::KELAS_12 => 'Kelas 12',
            self::KELAS_12 => 'Umum',
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
