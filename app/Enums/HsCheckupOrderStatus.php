<?php

namespace App\Enums;

/**
 * 体检预约单状态。
 */
enum HsCheckupOrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Pending => '待到检',
            self::Completed => '已完成',
            self::Cancelled => '已取消',
            self::NoShow => '爽约',
        };
    }
}
