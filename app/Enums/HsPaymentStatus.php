<?php

namespace App\Enums;

/**
 * 医疗预约支付状态，与预约业务状态相互独立。
 */
enum HsPaymentStatus: string
{
    case NotRequired = 'not_required';
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Refunding = 'refunding';
    case Refunded = 'refunded';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::NotRequired => '无需支付',
            self::Unpaid => '待支付',
            self::Paid => '已支付',
            self::Refunding => '退款中',
            self::Refunded => '已退款',
            self::Closed => '已关闭',
        };
    }
}
