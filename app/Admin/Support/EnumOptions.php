<?php

namespace App\Admin\Support;

use BackedEnum;

final class EnumOptions
{
    /**
     * @param  list<BackedEnum>  $cases
     * @return array<int|string, string>
     */
    public static function from(array $cases): array
    {
        $options = [];

        foreach ($cases as $case) {
            $label = [$case, 'label'];
            $options[$case->value] = match (true) {
                is_callable($label) => (string) $label(),
                default => $case->name,
            };
        }

        return $options;
    }
}
