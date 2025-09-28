<?php

namespace App\Enums;

enum Rank: string
{
    case AMATEUR = "pemula";

    
    public function label() : string 
    {
        return match($this) 
        {
        self::AMATEUR => 'mandiri'
        };
    }
}
