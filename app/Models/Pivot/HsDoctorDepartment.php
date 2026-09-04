<?php

namespace App\Models\Pivot;

use App\Models\HsDepartment;
use App\Models\HsDoctor;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * 医生与科室关联中间表。
 *
 * @property int $doctor_id 医生 ID
 * @property int $department_id 科室 ID
 * @property bool $is_primary 是否主科室
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property-read HsDoctor $doctor 医生
 * @property-read HsDepartment $department 科室
 */
#[Table(name: 'hs_doctor_department')]
#[Fillable(['doctor_id', 'department_id', 'is_primary'])]
class HsDoctorDepartment extends Pivot
{
    public $incrementing = false;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /** @return BelongsTo<HsDoctor, $this> */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(HsDoctor::class, 'doctor_id');
    }

    /** @return BelongsTo<HsDepartment, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(HsDepartment::class, 'department_id');
    }
}
