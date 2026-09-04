<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 医院院区。
 *
 * @property int $id 院区主键
 * @property string $name 院区名称
 * @property string $slug URL 标识
 * @property string|null $address 地址
 * @property string|null $phone 联系电话
 * @property string|null $latitude 纬度
 * @property string|null $longitude 经度
 * @property string|null $open_hours 开放时间说明
 * @property int $sort 排序值
 * @property bool $is_enabled 是否启用
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 软删除时间
 * @property-read Collection<int, HsDepartment> $departments 下属科室
 * @property-read Collection<int, HsSchedule> $schedules 出诊排班
 * @property-read Collection<int, HsAppointment> $appointments 预约单
 *
 * @method static Builder<static> enabled() 只查询已启用的院区
 */
#[Table(name: 'hs_campuses')]
#[Fillable([
    'name', 'slug', 'address', 'phone', 'latitude', 'longitude',
    'open_hours', 'sort', 'is_enabled',
])]
class HsCampus extends Model
{
    use SoftDeletes;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_enabled' => 'boolean',
        ];
    }

    /** @return HasMany<HsDepartment, $this> */
    public function departments(): HasMany
    {
        return $this->hasMany(HsDepartment::class, 'campus_id')->orderBy('sort');
    }

    /** @return HasMany<HsSchedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(HsSchedule::class, 'campus_id');
    }

    /** @return HasMany<HsAppointment, $this> */
    public function appointments(): HasMany
    {
        return $this->hasMany(HsAppointment::class, 'campus_id');
    }

    /**
     * @param  Builder<HsCampus>  $query
     * @return Builder<HsCampus>
     */
    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
