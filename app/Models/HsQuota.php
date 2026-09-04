<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * 排班号源时段。
 *
 * @property int $id 号源主键
 * @property int $schedule_id 所属排班 ID
 * @property string $start_time 时段开始
 * @property string $end_time 时段结束
 * @property int $total 号源总量
 * @property int $remaining 剩余可约
 * @property int $locked 锁定中数量
 * @property int $sort 排序值
 * @property bool $is_enabled 是否可约
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property-read HsSchedule $schedule 所属排班
 * @property-read Collection<int, HsAppointment> $appointments 预约单
 *
 * @method static Builder<static> available() 只查询仍有余号且可约的时段
 */
#[Table(name: 'hs_quotas')]
#[Fillable([
    'schedule_id', 'start_time', 'end_time', 'total', 'remaining',
    'locked', 'sort', 'is_enabled',
])]
class HsQuota extends Model
{
    /** @var array<string, mixed> */
    protected $attributes = [
        'total' => 0,
        'remaining' => 0,
        'locked' => 0,
        'sort' => 0,
        'is_enabled' => true,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<HsSchedule, $this> */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(HsSchedule::class, 'schedule_id');
    }

    /** @return HasMany<HsAppointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(HsAppointment::class, 'quota_id');
    }

    /**
     * @param  Builder<HsQuota>  $query
     * @return Builder<HsQuota>
     */
    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->where('is_enabled', true)->where('remaining', '>', 0);
    }
}
