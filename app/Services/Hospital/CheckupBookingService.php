<?php

namespace App\Services\Hospital;

use App\Enums\HsCheckupGenderLimit;
use App\Enums\HsCheckupOrderStatus;
use App\Enums\UserGender;
use App\Exceptions\ConflictException;
use App\Exceptions\InvalidArgumentException;
use App\Models\HsCheckupOrder;
use App\Models\HsCheckupSetting;
use App\Models\HsCheckupSlot;
use App\Models\HsPatient;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckupBookingService
{
    public function create(User $user, int $patientId, int $slotId, ?string $remark = null): HsCheckupOrder
    {
        $settings = HsCheckupSetting::current();

        if (! $settings->booking_enabled) {
            throw new ConflictException('当前未开放在线体检预约。');
        }

        $patient = $this->findOwnedPatient($user, $patientId);
        $this->assertNotBannedByNoShow($user, $settings);

        return DB::transaction(function () use ($user, $patient, $slotId, $remark, $settings): HsCheckupOrder {
            /** @var HsCheckupSlot $slot */
            $slot = HsCheckupSlot::query()
                ->whereKey($slotId)
                ->lockForUpdate()
                ->with(['package'])
                ->firstOrFail();

            $package = $slot->package;

            if (! $slot->is_enabled || $slot->remaining < 1) {
                throw new ConflictException('该场次名额已满或不可预约。');
            }

            if (! $package || ! $package->is_enabled) {
                throw new ConflictException('该体检套餐已下架或不可预约。');
            }

            $this->assertGenderAllowed($package->gender_limit, $patient->gender);

            $rawDate = $slot->slot_date;
            if (! $rawDate instanceof CarbonInterface) {
                throw new InvalidArgumentException('场次日期无效。');
            }

            $appointmentDate = $rawDate->startOfDay();
            $today = now()->startOfDay();
            if ($appointmentDate->lt($today)) {
                throw new ConflictException('不能预约过去的日期。');
            }

            $maxDate = $today->addDays(max(0, (int) $settings->advance_days));
            if ($appointmentDate->gt($maxDate)) {
                throw new ConflictException("仅支持预约 {$settings->advance_days} 天内的体检场次。");
            }

            $existsPending = HsCheckupOrder::query()
                ->where('user_id', $user->id)
                ->where('patient_id', $patient->id)
                ->where('slot_id', $slot->id)
                ->where('status', HsCheckupOrderStatus::Pending)
                ->exists();

            if ($existsPending) {
                throw new ConflictException('该体检人已预约此时段，请勿重复预约。');
            }

            $slot->remaining = max(0, $slot->remaining - 1);
            $slot->save();

            return HsCheckupOrder::query()->create([
                'order_no' => $this->generateOrderNo(),
                'user_id' => $user->id,
                'patient_id' => $patient->id,
                'campus_id' => $slot->campus_id,
                'package_id' => $package->id,
                'slot_id' => $slot->id,
                'appointment_date' => $appointmentDate->toDateString(),
                'period' => $slot->period,
                'price' => $package->price,
                'status' => HsCheckupOrderStatus::Pending,
                'remark' => $remark,
            ]);
        });
    }

    public function cancel(User $user, HsCheckupOrder $order, ?string $reason = null): HsCheckupOrder
    {
        if ($order->user_id !== $user->id) {
            throw new InvalidArgumentException('无权操作该体检预约单。');
        }

        if ($order->status !== HsCheckupOrderStatus::Pending) {
            throw new ConflictException('仅待到检的预约单可以取消。');
        }

        $settings = HsCheckupSetting::current();
        $this->assertCancellableByTime($order, $settings);

        return DB::transaction(function () use ($order, $reason): HsCheckupOrder {
            /** @var HsCheckupOrder $locked */
            $locked = HsCheckupOrder::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== HsCheckupOrderStatus::Pending) {
                throw new ConflictException('仅待到检的预约单可以取消。');
            }

            /** @var HsCheckupSlot $slot */
            $slot = HsCheckupSlot::query()->whereKey($locked->slot_id)->lockForUpdate()->firstOrFail();
            $slot->remaining = min($slot->total, $slot->remaining + 1);
            $slot->save();

            $locked->forceFill([
                'status' => HsCheckupOrderStatus::Cancelled,
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ])->save();

            return $locked->refresh();
        });
    }

    protected function findOwnedPatient(User $user, int $patientId): HsPatient
    {
        $patient = HsPatient::query()
            ->whereKey($patientId)
            ->where('user_id', $user->id)
            ->first();

        if (! $patient) {
            throw new InvalidArgumentException('就诊人不存在或不属于当前用户。');
        }

        return $patient;
    }

    protected function assertGenderAllowed(HsCheckupGenderLimit $limit, ?UserGender $gender): void
    {
        if ($limit === HsCheckupGenderLimit::All) {
            return;
        }

        if ($limit === HsCheckupGenderLimit::Male && $gender === UserGender::Male) {
            return;
        }

        if ($limit === HsCheckupGenderLimit::Female && $gender === UserGender::Female) {
            return;
        }

        throw new ConflictException('该套餐不适用于当前体检人的性别。');
    }

    protected function assertNotBannedByNoShow(User $user, HsCheckupSetting $settings): void
    {
        $limit = (int) $settings->no_show_limit;
        $banDays = (int) $settings->no_show_ban_days;

        if ($limit <= 0 || $banDays <= 0) {
            return;
        }

        $noShowCount = HsCheckupOrder::query()
            ->where('user_id', $user->id)
            ->where('status', HsCheckupOrderStatus::NoShow)
            ->where('appointment_date', '>=', now()->subDays($banDays)->toDateString())
            ->count();

        if ($noShowCount >= $limit) {
            throw new ConflictException("近期体检爽约次数过多，{$banDays} 天内暂不可预约。");
        }
    }

    protected function assertCancellableByTime(HsCheckupOrder $order, HsCheckupSetting $settings): void
    {
        $hours = (int) $settings->cancel_hours_before;
        if ($hours <= 0) {
            return;
        }

        $date = $order->appointment_date;
        if (! $date instanceof CarbonInterface) {
            throw new InvalidArgumentException('预约日期无效。');
        }

        // 体检场次通常按午别，取消时限按当天 00:00 起算再减小时
        $visitAt = Carbon::parse($date->format('Y-m-d').' 00:00:00');

        if (now()->greaterThanOrEqualTo($visitAt->copy()->subHours($hours))) {
            throw new ConflictException("到检前 {$hours} 小时内不可取消预约。");
        }
    }

    protected function generateOrderNo(): string
    {
        return 'CK'.now()->format('YmdHis').Str::upper(Str::random(4));
    }
}
