<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 体检预约规则配置。
 *
 * @property int $id
 * @property string $key
 * @property bool $booking_enabled
 * @property int $advance_days
 * @property int $cancel_hours_before
 * @property int $no_show_limit
 * @property int $no_show_ban_days
 * @property string|null $notice
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table(name: 'hs_checkup_settings')]
#[Fillable([
    'key', 'booking_enabled', 'advance_days', 'cancel_hours_before',
    'no_show_limit', 'no_show_ban_days', 'notice',
])]
class HsCheckupSetting extends Model
{
    public const DEFAULT_KEY = 'default';

    /** @var array<string, mixed> */
    protected $attributes = [
        'key' => self::DEFAULT_KEY,
        'booking_enabled' => true,
        'advance_days' => 14,
        'cancel_hours_before' => 24,
        'no_show_limit' => 3,
        'no_show_ban_days' => 30,
    ];

    /**
     * 获取当前体检预约规则，尚未创建时返回带默认值的新模型。
     */
    public static function current(): self
    {
        return self::query()->where('key', self::DEFAULT_KEY)->first() ?? new self;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'booking_enabled' => 'boolean',
            'advance_days' => 'integer',
            'cancel_hours_before' => 'integer',
            'no_show_limit' => 'integer',
            'no_show_ban_days' => 'integer',
        ];
    }
}
