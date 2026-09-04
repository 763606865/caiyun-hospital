<?php

namespace App\Enums;

/**
 * 体检套餐适用性别。
 */
enum HsCheckupGenderLimit: string
{
    case All = 'all';
    case Male = 'male';
    case Female = 'female';

    public function label(): string
    {
        return match ($this) {
            self::All => '不限',
            self::Male => '仅男性',
            self::Female => '仅女性',
        };
    }
}
