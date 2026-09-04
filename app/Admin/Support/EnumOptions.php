<?php

namespace App\Admin\Support;

use BackedEnum;

final class EnumOptions
{
    /**
     * @param  array<int, BackedEnum&object{label(): string}>  $cases
     * @return array<int|string, string>
     */
    public static function from(array $cases): array
    {
        $options = [];

        foreach ($cases as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
