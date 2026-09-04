<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 预约规则配置。
 *
 * @property int $id 配置主键
 * @property string $key 配置实例标识
 * @property bool $registration_enabled 是否开放在线挂号
 * @property int $advance_days 提前放号天数
 * @property int $cancel_hours_before 就诊前多少小时可取消
 * @property int $no_show_limit 周期内爽约上限
 * @property int $no_show_ban_days 爽约超限后限制预约天数
 * @property string|null $notice 预约须知短文案
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 */
#[Table(name: 'hs_appointment_settings')]
#[Fillable([
    'registration_enabled', 'advance_days', 'cancel_hours_before',
    'no_show_limit', 'no_show_ban_days', 'notice',
])]
class HsAppointmentSetting extends Model
{
    public const DEFAULT_KEY = 'default';

    /** @var array<string, mixed> */
    protected $attributes = [
        'key' => self::DEFAULT_KEY,
        'registration_enabled' => true,
        'advance_days' => 7,
        'cancel_hours_before' => 2,
        'no_show_limit' => 3,
        'no_show_ban_days' => 30,
    ];

    /**
     * 获取当前预约规则，尚未创建时返回带默认值的新模型。
     */
    public static function current(): self
    {
        return self::query()->where('key', self::DEFAULT_KEY)->first() ?? new self;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'registration_enabled' => 'boolean',
            'advance_days' => 'integer',
            'cancel_hours_before' => 'integer',
            'no_show_limit' => 'integer',
            'no_show_ban_days' => 'integer',
        ];
    }
}
