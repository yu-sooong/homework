<?php

namespace App\Enums;

enum Currency: string
{
    case TWD = 'TWD';
    case USD = 'USD';
    case JPY = 'JPY';
    case RMB = 'RMB';
    case MYR = 'MYR';

    /**
     * 取得所有有效值
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
