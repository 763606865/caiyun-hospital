<?php

namespace App\Services\Hospital;

use App\Enums\HsSchedulePeriod;
use App\Models\HsCheckupPackage;
use App\Models\HsCheckupSlot;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CheckupSlotGenerationService
{
    /**
     * @param  array<int, int|string>  $packageIds
     * @param  array<int, int|string>  $weekdays
     * @param  array<int, HsSchedulePeriod|string>  $periods
     * @return array{created_slots: int, skipped_existing: int, days: int}
     */
    public function generate(
        array $packageIds,
        CarbonInterface|string $from,
        CarbonInterface|string $to,
        array $weekdays,
        array $periods,
        int $total,
    ): array {
        $fromDate = $this->toDate($from);
        $toDate = $this->toDate($to);

        if ($toDate->lt($fromDate)) {
            throw new InvalidArgumentException('结束日期不能早于开始日期。');
        }

        if ($fromDate->diffInDays($toDate) > 366) {
            throw new InvalidArgumentException('单次生成的日期范围不能超过 366 天。');
        }

        if ($total < 1) {
            throw new InvalidArgumentException('每场名额必须大于 0。');
        }

        $selectedWeekdays = collect($weekdays)->map(fn ($weekday): int => (int) $weekday)->unique();
        $selectedPeriods = collect($periods)
            ->map(fn (HsSchedulePeriod|string $period): HsSchedulePeriod => $period instanceof HsSchedulePeriod ? $period : HsSchedulePeriod::from($period))
            ->unique(fn (HsSchedulePeriod $period): string => $period->value);
        $packages = HsCheckupPackage::query()->whereKey($packageIds)->get();

        if ($packages->isEmpty() || $selectedWeekdays->isEmpty() || $selectedPeriods->isEmpty()) {
            throw new InvalidArgumentException('请至少选择一个套餐、星期和场次。');
        }

        $created = 0;
        $skippedExisting = 0;
        $days = 0;

        DB::transaction(function () use (
            $fromDate,
            $toDate,
            $selectedWeekdays,
            $selectedPeriods,
            $packages,
            $total,
            &$created,
            &$skippedExisting,
            &$days,
        ): void {
            for ($date = $fromDate; $date->lte($toDate); $date = $date->addDay()) {
                if (! $selectedWeekdays->contains($date->dayOfWeekIso)) {
                    continue;
                }

                $days++;

                foreach ($packages as $package) {
                    foreach ($selectedPeriods as $period) {
                        $slot = HsCheckupSlot::query()->firstOrCreate(
                            [
                                'package_id' => $package->id,
                                'slot_date' => $date,
                                'period' => $period,
                            ],
                            [
                                'campus_id' => $package->campus_id,
                                'total' => $total,
                                'remaining' => $total,
                                'is_enabled' => true,
                            ],
                        );

                        if ($slot->wasRecentlyCreated) {
                            $created++;
                        } else {
                            $skippedExisting++;
                        }
                    }
                }
            }
        });

        return [
            'created_slots' => $created,
            'skipped_existing' => $skippedExisting,
            'days' => $days,
        ];
    }

    private function toDate(CarbonInterface|string $date): CarbonImmutable
    {
        return $date instanceof CarbonInterface
            ? CarbonImmutable::instance($date)->startOfDay()
            : CarbonImmutable::parse($date)->startOfDay();
    }
}
