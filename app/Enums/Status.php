<?php

namespace App\Enums;

enum Status: string
{
    case PENDING = "menunggu";

    public function label() : string 
    {
        return match($this) 
        {
            self::PENDING => 'Menunggu'
        };
    }
}
