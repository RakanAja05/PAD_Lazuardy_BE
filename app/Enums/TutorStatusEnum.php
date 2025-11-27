<?php

namespace App\Enums;

enum TutorStatusEnum: string
{
    case VERIFY = "verify";
    case ACTIVE = "active";
    case REJECTED = "rejected";

    public function displayName() : string 
    {
        return match($this) 
        {
            self::ACTIVE => 'Aktif',
            self::VERIFY => 'Menunggu konfirmasi',
            self::REJECTED => 'Ditolak', 
        };
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
