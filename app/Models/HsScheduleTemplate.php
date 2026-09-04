<?php

namespace App\Models;

use App\Enums\HsSchedulePeriod;
use App\Enums\HsVisitType;
use App\Enums\HsWeekday;
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
 * 医生出诊周模板。
 *
 * @property int $id
 * @property int $campus_id
 * @property int $department_id
 * @property int $doctor_id
 * @property HsWeekday $weekday
 * @property HsSchedulePeriod $period
 * @property HsVisitType $visit_type
 * @property string|null $room
 * @property string|null $fee
 * @property bool $is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read HsCampus $campus
 * @property-read HsDepartment $department
 * @property-read HsDoctor $doctor
 * @property-read Collection<int, HsScheduleTemplateSlot> $slots
 *
 * @method static Builder<static> enabled()
 */
#[Table(name: 'hs_schedule_templates')]
#[Fillable([
    'campus_id', 'department_id', 'doctor_id', 'weekday', 'period',
    'visit_type', 'room', 'fee', 'is_enabled',
])]
class HsScheduleTemplate extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'visit_type' => HsVisitType::Normal->value,
        'is_enabled' => true,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'weekday' => HsWeekday::class,
            'period' => HsSchedulePeriod::class,
            'visit_type' => HsVisitType::class,
            'fee' => 'decimal:2',
            'is_enabled' => 'boolean',
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

    /** @return HasMany<HsScheduleTemplateSlot, $this> */
    public function slots(): HasMany
    {
        return $this->hasMany(HsScheduleTemplateSlot::class, 'template_id')->orderBy('sort');
    }

    /**
     * @param  Builder<HsScheduleTemplate>  $query
     * @return Builder<HsScheduleTemplate>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }

    /**
     * 后台展示用短标签：星期 午别 · 科室 · 院区。
     */
    public function adminLabel(): string
    {
        $weekday = $this->weekday?->label() ?? '-';
        $period = $this->period?->label() ?? '-';
        $department = $this->department?->name ?? '-';
        $campus = $this->campus?->name ?? '-';

        return "{$weekday} {$period} · {$department} · {$campus}";
    }
}
