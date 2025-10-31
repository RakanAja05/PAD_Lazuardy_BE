<?php

namespace App\Enums;

enum ScheduleStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function list(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function displayList(): array
    {
        return [
            self::PENDING->value => 'Menunggu',
            self::COMPLETED->value => 'Selesai',
            self::CANCELLED->value => 'Dibatalkan',
        ];
    }

    public function display(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }
}
