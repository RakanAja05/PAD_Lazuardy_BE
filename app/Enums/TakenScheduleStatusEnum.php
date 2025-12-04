<?php

namespace App\Enums;

enum TakenScheduleStatusEnum: string
{
    case ACTIVE = "active";
    case COMPLETED = 'completed';
    case EXPIRED = "expired";
    case CANCELLED = "cancelled";

    public function displayName() : string 
    {
        return match($this) 
        {
            self::ACTIVE => 'Aktif',
            self::COMPLETED => 'Tuntas',
            self::EXPIRED => 'Terlewat',
            self::CANCELLED => 'Dibatalkan',
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
