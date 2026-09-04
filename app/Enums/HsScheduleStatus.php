<?php

namespace App\Enums;

/**
 * 排班状态。
 */
enum HsScheduleStatus: string
{
    case Normal = 'normal';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Normal => '正常',
            self::Suspended => '停诊',
        };
    }
}
