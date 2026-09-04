<?php

namespace App\Enums;

/**
 * 就诊人与账号关系。
 */
enum HsPatientRelation: string
{
    case Self = 'self';
    case Parent = 'parent';
    case Child = 'child';
    case Spouse = 'spouse';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Self => '本人',
            self::Parent => '父母',
            self::Child => '子女',
            self::Spouse => '配偶',
            self::Other => '其他',
        };
    }
}
