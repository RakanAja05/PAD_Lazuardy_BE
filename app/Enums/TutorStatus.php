<?php

namespace App\Enums;

enum TutorStatus: string
{
    case ACTIVE = "active";
    case VERIFY = "verify";
    case INACTIVE = "inactive";
    case REJECTED = "rejected";

    public function label() : string 
    {
        return match($this) 
        {
            self::ACTIVE => 'Aktif',
            self::VERIFY => 'Menunggu konfirmasi',
            self::INACTIVE => 'Tidak aktif',
            self::REJECTED => 'Ditolak',
        };
    }
}
