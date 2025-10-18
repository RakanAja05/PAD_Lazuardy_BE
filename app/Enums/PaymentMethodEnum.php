<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case BANK_MANDIRI = 'mandiri';
    case BANK_BNI = 'bni';
    case BANK_BRI = 'bri';
    case BANK_BPR = 'bpr';
    case BANK_BPD = 'bpd';
    case BANK_QRIS = 'qris';

    public function displayName() : string 
    {
        return match($this) 
        {
            self::BANK_MANDIRI => 'mandiri',
            self::BANK_BNI => 'bni',
            self::BANK_BRI => 'bri',
            self::BANK_BPR => 'bpr',
            self::BANK_BPD => 'bpd',
            self::BANK_QRIS => 'qris',
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
