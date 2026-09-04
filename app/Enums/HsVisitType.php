<?php

namespace App\Enums;

/**
 * 挂号号别。
 */
enum HsVisitType: string
{
    case Normal = 'normal';
    case Expert = 'expert';

    public function label(): string
    {
        return match ($this) {
            self::Normal => '普通号',
            self::Expert => '专家号',
        };
    }
}
