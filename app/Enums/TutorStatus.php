<?php

namespace App\Enums;

enum TutorStatus: string
{
    case ACTIVE = "active";
    case PENDING = "pending";
    case INACTIVE = "inactive";
    case REJECTED = "rejected";

    public function label() : string 
    {
        return match($this) 
        {
            self::ACTIVE => 'Aktif',
            self::PENDING => 'Menunggu konfirmasi',
            self::INACTIVE => 'Tidak aktif',
            self::REJECTED => 'Ditolak',
        };
    }
}
