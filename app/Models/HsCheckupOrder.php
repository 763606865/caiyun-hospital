<?php

namespace App\Models;

use App\Enums\HsCheckupOrderStatus;
use App\Enums\HsSchedulePeriod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 体检预约单。
 *
 * @property int $id
 * @property string $order_no
 * @property int $user_id
 * @property int $patient_id
 * @property int $campus_id
 * @property int $package_id
 * @property int $slot_id
 * @property Carbon $appointment_date
 * @property HsSchedulePeriod $period
 * @property string $price
 * @property HsCheckupOrderStatus $status
 * @property string|null $cancel_reason
 * @property Carbon|null $cancelled_at
 * @property Carbon|null $completed_at
 * @property string|null $remark
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read HsPatient $patient
 * @property-read HsCampus $campus
 * @property-read HsCheckupPackage $package
 * @property-read HsCheckupSlot $slot
 */
#[Table(name: 'hs_checkup_orders')]
#[Fillable([
    'order_no', 'user_id', 'patient_id', 'campus_id', 'package_id', 'slot_id',
    'appointment_date', 'period', 'price', 'status', 'cancel_reason',
    'cancelled_at', 'completed_at', 'remark',
])]
class HsCheckupOrder extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => HsCheckupOrderStatus::Pending->value,
        'price' => 0,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'period' => HsSchedulePeriod::class,
            'price' => 'decimal:2',
            'status' => HsCheckupOrderStatus::class,
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<HsPatient, $this> */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(HsPatient::class, 'patient_id');
    }

    /** @return BelongsTo<HsCampus, $this> */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(HsCampus::class, 'campus_id');
    }

    /** @return BelongsTo<HsCheckupPackage, $this> */
    public function package(): BelongsTo
    {
        return $this->belongsTo(HsCheckupPackage::class, 'package_id');
    }

    /** @return BelongsTo<HsCheckupSlot, $this> */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(HsCheckupSlot::class, 'slot_id');
    }
}
