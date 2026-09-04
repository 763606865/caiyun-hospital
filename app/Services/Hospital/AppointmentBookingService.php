<?php

namespace App\Services\Hospital;

use App\Enums\HsAppointmentStatus;
use App\Enums\HsSchedulePeriod;
use App\Enums\HsScheduleStatus;
use App\Exceptions\ConflictException;
use App\Exceptions\InvalidArgumentException;
use App\Models\HsAppointment;
use App\Models\HsAppointmentSetting;
use App\Models\HsPatient;
use App\Models\HsQuota;
use App\Models\HsSchedule;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentBookingService
{
    public function create(User $user, int $patientId, int $quotaId, ?string $remark = null): HsAppointment
    {
        $settings = HsAppointmentSetting::current();

        if (! $settings->registration_enabled) {
            throw new ConflictException('当前未开放在线挂号。');
        }

        $this->assertPatientBelongsToUser($user, $patientId);
        $this->assertNotBannedByNoShow($user, $settings);

        return DB::transaction(function () use ($user, $patientId, $quotaId, $remark, $settings): HsAppointment {
            /** @var HsQuota $quota */
            $quota = HsQuota::query()
                ->whereKey($quotaId)
                ->lockForUpdate()
                ->with(['schedule.doctor', 'schedule.department', 'schedule.campus'])
                ->firstOrFail();

            $schedule = $quota->schedule;

            if (! $quota->is_enabled || $quota->remaining < 1) {
                throw new ConflictException('该时段号源已满或不可预约。');
            }

            if (! $schedule || $schedule->status !== HsScheduleStatus::Normal) {
                throw new ConflictException('该出诊排班已停诊或不可预约。');
            }

            $rawDate = $schedule->schedule_date;
            if (! $rawDate instanceof CarbonInterface) {
                throw new InvalidArgumentException('排班日期无效。');
            }

            $appointmentDate = $rawDate->startOfDay();
            $today = now()->startOfDay();
            if ($appointmentDate->lt($today)) {
                throw new ConflictException('不能预约过去的日期。');
            }

            $maxDate = $today->addDays(max(0, (int) $settings->advance_days));
            if ($appointmentDate->gt($maxDate)) {
                throw new ConflictException("仅支持预约 {$settings->advance_days} 天内的号源。");
            }

            $existsPending = HsAppointment::query()
                ->where('user_id', $user->id)
                ->where('patient_id', $patientId)
                ->where('quota_id', $quota->id)
                ->where('status', HsAppointmentStatus::Pending)
                ->exists();

            if ($existsPending) {
                throw new ConflictException('该就诊人已预约此时段，请勿重复挂号。');
            }

            $quota->remaining = max(0, $quota->remaining - 1);
            $quota->save();

            $fee = $schedule->fee ?? $schedule->doctor?->fee ?? 0;
            $appointmentNo = $this->generateAppointmentNo();
            $ticketNo = $this->nextTicketNo($schedule);

            return HsAppointment::query()->create([
                'appointment_no' => $appointmentNo,
                'user_id' => $user->id,
                'patient_id' => $patientId,
                'campus_id' => $schedule->campus_id,
                'department_id' => $schedule->department_id,
                'doctor_id' => $schedule->doctor_id,
                'schedule_id' => $schedule->id,
                'quota_id' => $quota->id,
                'appointment_date' => $appointmentDate->toDateString(),
                'period' => $schedule->period,
                'start_time' => $quota->start_time,
                'end_time' => $quota->end_time,
                'fee' => $fee,
                'ticket_no' => $ticketNo,
                'voucher_code' => $appointmentNo,
                'status' => HsAppointmentStatus::Pending,
                'remark' => $remark,
            ]);
        });
    }

    public function cancel(User $user, HsAppointment $appointment, ?string $reason = null): HsAppointment
    {
        if ($appointment->user_id !== $user->id) {
            throw new InvalidArgumentException('无权操作该预约单。');
        }

        if ($appointment->status !== HsAppointmentStatus::Pending) {
            throw new ConflictException('仅待就诊的预约单可以取消。');
        }

        $settings = HsAppointmentSetting::current();
        $this->assertCancellableByTime($appointment, $settings);

        return DB::transaction(function () use ($appointment, $reason): HsAppointment {
            /** @var HsAppointment $locked */
            $locked = HsAppointment::query()->whereKey($appointment->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== HsAppointmentStatus::Pending) {
                throw new ConflictException('仅待就诊的预约单可以取消。');
            }

            /** @var HsQuota $quota */
            $quota = HsQuota::query()->whereKey($locked->quota_id)->lockForUpdate()->firstOrFail();
            $quota->remaining = min($quota->total, $quota->remaining + 1);
            $quota->save();

            $locked->forceFill([
                'status' => HsAppointmentStatus::Cancelled,
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ])->save();

            return $locked->refresh();
        });
    }

    protected function assertPatientBelongsToUser(User $user, int $patientId): void
    {
        $exists = HsPatient::query()
            ->whereKey($patientId)
            ->where('user_id', $user->id)
            ->exists();

        if (! $exists) {
            throw new InvalidArgumentException('就诊人不存在或不属于当前用户。');
        }
    }

    protected function assertNotBannedByNoShow(User $user, HsAppointmentSetting $settings): void
    {
        $limit = (int) $settings->no_show_limit;
        $banDays = (int) $settings->no_show_ban_days;

        if ($limit <= 0 || $banDays <= 0) {
            return;
        }

        $noShowCount = HsAppointment::query()
            ->where('user_id', $user->id)
            ->where('status', HsAppointmentStatus::NoShow)
            ->where('appointment_date', '>=', now()->subDays($banDays)->toDateString())
            ->count();

        if ($noShowCount >= $limit) {
            throw new ConflictException("近期爽约次数过多，{$banDays} 天内暂不可预约。");
        }
    }

    protected function assertCancellableByTime(HsAppointment $appointment, HsAppointmentSetting $settings): void
    {
        $hours = (int) $settings->cancel_hours_before;
        if ($hours <= 0) {
            return;
        }

        $date = $appointment->appointment_date;
        if (! $date instanceof CarbonInterface) {
            throw new InvalidArgumentException('预约日期无效。');
        }

        $start = (string) ($appointment->start_time ?: '00:00:00');
        $visitAt = Carbon::parse($date->format('Y-m-d').' '.$start);

        if (now()->greaterThanOrEqualTo($visitAt->copy()->subHours($hours))) {
            throw new ConflictException("就诊前 {$hours} 小时内不可取消预约。");
        }
    }

    protected function generateAppointmentNo(): string
    {
        return 'AP'.now()->format('YmdHis').Str::upper(Str::random(4));
    }

    /**
     * 按排班生成取号号码（同排班递增）。
     */
    protected function nextTicketNo(HsSchedule $schedule): string
    {
        $prefix = match ($schedule->period) {
            HsSchedulePeriod::Morning => 'A',
            HsSchedulePeriod::Afternoon => 'B',
            HsSchedulePeriod::Evening => 'C',
            default => 'A',
        };

        $seq = HsAppointment::query()
            ->where('schedule_id', $schedule->id)
            ->where('status', '!=', HsAppointmentStatus::Cancelled)
            ->count() + 1;

        return $prefix.'-'.str_pad((string) $seq, 2, '0', STR_PAD_LEFT);
    }
}
