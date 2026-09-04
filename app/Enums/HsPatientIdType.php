<?php

namespace App\Enums;

/**
 * 就诊人证件类型。
 */
enum HsPatientIdType: string
{
    case IdCard = 'id_card';

    public function label(): string
    {
        return match ($this) {
            self::IdCard => '身份证',
        };
    }
}
