<?php

namespace App\Models;

use App\Models\Pivot\HsDoctorDepartment;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 医院科室。
 *
 * @property int $id 科室主键
 * @property int $campus_id 所属院区 ID
 * @property string $name 科室名称
 * @property string $slug URL 标识
 * @property string|null $summary 简介
 * @property string|null $specialties 擅长方向
 * @property string|null $body 详细介绍
 * @property string|null $cover 封面图路径
 * @property string|null $location 位置/楼层/诊区
 * @property int $sort 排序值
 * @property bool $is_enabled 是否启用
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 软删除时间
 * @property-read HsCampus $campus 所属院区
 * @property-read Collection<int, HsDoctor> $doctors 科室医生
 * @property-read Collection<int, HsSchedule> $schedules 出诊排班
 * @property-read Collection<int, HsAppointment> $appointments 预约单
 * @property-read HsDoctorDepartment|null $pivot 医生关联中间表
 *
 * @method static Builder<static> enabled() 只查询已启用的科室
 */
#[Table(name: 'hs_departments')]
#[Fillable([
    'campus_id', 'name', 'slug', 'summary', 'specialties', 'body',
    'cover', 'location', 'sort', 'is_enabled',
])]
class HsDepartment extends Model
{
    use SoftDeletes;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    /** @return BelongsTo<HsCampus, $this> */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(HsCampus::class, 'campus_id');
    }

    /**
     * @return BelongsToMany<HsDoctor, $this, HsDoctorDepartment>
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(HsDoctor::class, (new HsDoctorDepartment)->getTable(), 'department_id', 'doctor_id')
            ->using(HsDoctorDepartment::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /** @return HasMany<HsSchedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(HsSchedule::class, 'department_id');
    }

    /** @return HasMany<HsAppointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(HsAppointment::class, 'department_id');
    }

    /**
     * @param  Builder<HsDepartment>  $query
     * @return Builder<HsDepartment>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
