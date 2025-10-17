<?php

namespace App\Enums;

enum CourseMode: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
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
