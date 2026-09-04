<?php

namespace App\Enums;

/**
 * 出诊午别。
 */
enum HsSchedulePeriod: string
{
    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Evening = 'evening';

    public function label(): string
    {
        return match ($this) {
            self::Morning => '上午',
            self::Afternoon => '下午',
            self::Evening => '晚上',
        };
    }
}
