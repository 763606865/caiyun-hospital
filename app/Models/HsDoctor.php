<?php

namespace App\Models;

use App\Models\Pivot\HsDoctorDepartment;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 医院医生。
 *
 * @property int $id 医生主键
 * @property string $name 医生姓名
 * @property string $slug URL 标识
 * @property string|null $title 职称
 * @property string|null $specialties 擅长
 * @property string|null $summary 简介
 * @property string|null $body 详细介绍
 * @property string|null $avatar 头像路径
 * @property string $fee 默认挂号费
 * @property int $sort 排序值
 * @property bool $is_enabled 是否启用
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 软删除时间
 * @property-read Collection<int, HsDepartment> $departments 所属科室
 * @property-read Collection<int, HsSchedule> $schedules 出诊排班
 * @property-read Collection<int, HsAppointment> $appointments 预约单
 * @property-read HsDoctorDepartment|null $pivot 科室关联中间表
 *
 * @method static Builder<static> enabled() 只查询已启用的医生
 */
#[Table(name: 'hs_doctors')]
#[Fillable([
    'name', 'slug', 'title', 'specialties', 'summary', 'body',
    'avatar', 'fee', 'sort', 'is_enabled',
])]
class HsDoctor extends Model
{
    use SoftDeletes;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<HsDepartment, $this, HsDoctorDepartment>
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(HsDepartment::class, (new HsDoctorDepartment)->getTable(), 'doctor_id', 'department_id')
            ->using(HsDoctorDepartment::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /** @return HasMany<HsSchedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(HsSchedule::class, 'doctor_id');
    }

    /** @return HasMany<HsAppointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(HsAppointment::class, 'doctor_id');
    }

    /**
     * @param  Builder<HsDoctor>  $query
     * @return Builder<HsDoctor>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
