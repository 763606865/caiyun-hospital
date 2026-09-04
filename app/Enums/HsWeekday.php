<?php

namespace App\Enums;

/**
 * 星期（ISO：周一=1 … 周日=7）。
 */
enum HsWeekday: int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public function label(): string
    {
        return match ($this) {
            self::Monday => '周一',
            self::Tuesday => '周二',
            self::Wednesday => '周三',
            self::Thursday => '周四',
            self::Friday => '周五',
            self::Saturday => '周六',
            self::Sunday => '周日',
        };
    }
}
