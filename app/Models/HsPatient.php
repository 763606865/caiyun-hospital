<?php

namespace App\Models;

use App\Enums\HsPatientIdType;
use App\Enums\HsPatientRelation;
use App\Enums\UserGender;
use App\Models\Concerns\BelongsToOrganization;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 就诊人。
 *
 * @property int $id 就诊人主键
 * @property int $user_id 所属前台用户 ID
 * @property string $name 就诊人姓名
 * @property HsPatientIdType $id_type 证件类型
 * @property string $id_number 证件号码
 * @property string $phone 手机号
 * @property UserGender|null $gender 性别
 * @property Carbon|null $birthday 出生日期
 * @property HsPatientRelation $relation 与账号关系
 * @property bool $is_default 是否默认就诊人
 * @property-read bool $is_minor 是否未成年（由生日推算，未填生日则为 false）
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 软删除时间
 * @property-read User $user 所属用户
 */
#[Table(name: 'hs_patients')]
#[Fillable([
    'user_id', 'name', 'id_type', 'id_number', 'phone',
    'gender', 'birthday', 'relation', 'is_default',
])]
class HsPatient extends Model
{
    use BelongsToOrganization, SoftDeletes;

    /** @var list<string> */
    protected $appends = [
        'is_minor',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'id_type' => HsPatientIdType::IdCard->value,
        'relation' => HsPatientRelation::Self->value,
        'is_default' => false,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'id_type' => HsPatientIdType::class,
            'gender' => UserGender::class,
            'birthday' => 'date',
            'relation' => HsPatientRelation::class,
            'is_default' => 'boolean',
        ];
    }

    /**
     * @return Attribute<bool, never>
     */
    protected function isMinor(): Attribute
    {
        return Attribute::get(function (): bool {
            if (! $this->birthday instanceof CarbonInterface) {
                return false;
            }

            return $this->birthday->age < 18;
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
