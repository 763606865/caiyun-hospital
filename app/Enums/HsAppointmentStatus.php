<?php

namespace App\Enums;

/**
 * 挂号预约单状态。
 */
enum HsAppointmentStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Pending => '待就诊',
            self::Completed => '已完成',
            self::Cancelled => '已取消',
            self::NoShow => '爽约',
        };
    }
}
