<?php

namespace App\Models;

use App\Enums\HsSchedulePeriod;
use App\Enums\HsScheduleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 医生出诊排班。
 *
 * @property int $id 排班主键
 * @property int $campus_id 院区 ID
 * @property int $department_id 出诊科室 ID
 * @property int $doctor_id 出诊医生 ID
 * @property Carbon $schedule_date 出诊日期
 * @property HsSchedulePeriod $period 午别
 * @property string|null $room 诊室
 * @property int $total_quota 当日该午别号源总量
 * @property HsScheduleStatus $status 状态
 * @property string|null $fee 本次出诊挂号费
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 软删除时间
 * @property-read HsCampus $campus 院区
 * @property-read HsDepartment $department 科室
 * @property-read HsDoctor $doctor 医生
 * @property-read Collection<int, HsQuota> $quotas 号源时段
 * @property-read Collection<int, HsAppointment> $appointments 预约单
 *
 * @method static Builder<static> bookable() 只查询可预约（正常出诊）排班
 */
#[Table(name: 'hs_schedules')]
#[Fillable([
    'campus_id', 'department_id', 'doctor_id', 'schedule_date', 'period',
    'room', 'total_quota', 'status', 'fee',
])]
class HsSchedule extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => HsScheduleStatus::Normal->value,
        'total_quota' => 0,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
            'period' => HsSchedulePeriod::class,
            'status' => HsScheduleStatus::class,
            'fee' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<HsCampus, $this> */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(HsCampus::class, 'campus_id');
    }

    /** @return BelongsTo<HsDepartment, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(HsDepartment::class, 'department_id');
    }

    /** @return BelongsTo<HsDoctor, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(HsDoctor::class, 'doctor_id');
    }

    /** @return HasMany<HsQuota, $this> */
    public function quotas(): HasMany
    {
        return $this->hasMany(HsQuota::class, 'schedule_id')->orderBy('sort');
    }

    /** @return HasMany<HsAppointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(HsAppointment::class, 'schedule_id');
    }

    /**
     * @param  Builder<HsSchedule>  $query
     * @return Builder<HsSchedule>
     */
    #[Scope]
    protected function bookable(Builder $query): Builder
    {
        return $query->where('status', HsScheduleStatus::Normal);
    }
}
