<?php

namespace App\Models;

use App\Enums\HsAppointmentStatus;
use App\Enums\HsPaymentStatus;
use App\Enums\HsSchedulePeriod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 挂号预约单。
 *
 * @property int $id 预约单主键
 * @property string $appointment_no 预约单号
 * @property int $user_id 下单用户 ID
 * @property int $patient_id 就诊人 ID
 * @property int $campus_id 院区 ID
 * @property int $department_id 科室 ID
 * @property int $doctor_id 医生 ID
 * @property int $schedule_id 排班 ID
 * @property int $quota_id 号源时段 ID
 * @property Carbon $appointment_date 就诊日期
 * @property HsSchedulePeriod $period 午别
 * @property string|null $start_time 时段开始
 * @property string|null $end_time 时段结束
 * @property string $fee 挂号费
 * @property string|null $ticket_no 取号号码
 * @property string|null $voucher_code 凭证码
 * @property HsAppointmentStatus $status 状态
 * @property HsPaymentStatus $payment_status 支付状态
 * @property string $paid_amount 实付金额
 * @property Carbon|null $paid_at 支付时间
 * @property string|null $cancel_reason 取消原因
 * @property Carbon|null $cancelled_at 取消时间
 * @property Carbon|null $completed_at 完成就诊时间
 * @property Carbon|null $notified_at 最近通知时间
 * @property Carbon|null $checked_in_at 取号/报到时间
 * @property string|null $remark 备注
 * @property Carbon|null $created_at 创建时间
 * @property Carbon|null $updated_at 更新时间
 * @property Carbon|null $deleted_at 软删除时间
 * @property-read User $user 下单用户
 * @property-read HsPatient $patient 就诊人
 * @property-read HsCampus $campus 院区
 * @property-read HsDepartment $department 科室
 * @property-read HsDoctor $doctor 医生
 * @property-read HsSchedule $schedule 排班
 * @property-read HsQuota $quota 号源时段
 */
#[Table(name: 'hs_appointments')]
#[Fillable([
    'appointment_no', 'user_id', 'patient_id', 'campus_id', 'department_id',
    'doctor_id', 'schedule_id', 'quota_id', 'appointment_date', 'period',
    'start_time', 'end_time', 'fee', 'ticket_no', 'voucher_code', 'status', 'payment_status',
    'paid_amount', 'paid_at',
    'cancel_reason', 'cancelled_at', 'completed_at', 'notified_at', 'checked_in_at', 'remark',
])]
class HsAppointment extends Model
{
    use SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'status' => HsAppointmentStatus::Pending->value,
        'payment_status' => HsPaymentStatus::NotRequired->value,
        'fee' => 0,
        'paid_amount' => 0,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'period' => HsSchedulePeriod::class,
            'fee' => 'decimal:2',
            'status' => HsAppointmentStatus::class,
            'payment_status' => HsPaymentStatus::class,
            'paid_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
            'notified_at' => 'datetime',
            'checked_in_at' => 'datetime',
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

    /** @return BelongsTo<HsSchedule, $this> */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(HsSchedule::class, 'schedule_id');
    }

    /** @return BelongsTo<HsQuota, $this> */
    public function quota(): BelongsTo
    {
        return $this->belongsTo(HsQuota::class, 'quota_id');
    }
}
