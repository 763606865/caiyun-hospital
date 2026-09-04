<?php

namespace App\Services\Hospital;

use App\Enums\HsScheduleStatus;
use App\Models\HsAppointmentSetting;
use App\Models\HsQuota;
use App\Models\HsSchedule;
use App\Models\HsScheduleTemplate;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduleGenerationService
{
    /**
     * 按出诊周模板，为日期窗口内缺失的排班补齐（含号源时段）。
     *
     * @return array{created_schedules: int, skipped_existing: int, skipped_no_slots: int, days: int}
     */
    public function generate(?CarbonInterface $from = null, ?CarbonInterface $to = null, bool $dryRun = false): array
    {
        $settings = HsAppointmentSetting::current();
        $fromDate = ($from ?? now())->copy()->startOfDay();
        $toDate = ($to ?? now()->addDays(max(0, (int) $settings->advance_days)))->copy()->startOfDay();

        if ($toDate->lt($fromDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $templates = HsScheduleTemplate::query()
            ->enabled()
            ->with([
                'doctor:id,fee,is_enabled',
                'slots' => fn ($query) => $query->where('is_enabled', true)->orderBy('sort')->orderBy('start_time'),
            ])
            ->whereHas('doctor', fn ($q) => $q->where('is_enabled', true))
            ->whereHas('department', fn ($q) => $q->where('is_enabled', true))
            ->whereHas('campus', fn ($q) => $q->where('is_enabled', true))
            ->get()
            ->groupBy(fn (HsScheduleTemplate $template) => $template->weekday->value);

        $created = 0;
        $skippedExisting = 0;
        $skippedNoSlots = 0;
        $days = 0;

        for ($date = $fromDate->copy(); $date->lte($toDate); $date = $date->addDay()) {
            $days++;
            $weekday = (int) $date->dayOfWeekIso;
            /** @var Collection<int, HsScheduleTemplate> $dayTemplates */
            $dayTemplates = $templates->get($weekday, collect());

            foreach ($dayTemplates as $template) {
                $enabledSlots = $template->slots;
                if ($enabledSlots->isEmpty()) {
                    $skippedNoSlots++;

                    continue;
                }

                $exists = HsSchedule::query()
                    ->where('doctor_id', $template->doctor_id)
                    ->where('department_id', $template->department_id)
                    ->whereDate('schedule_date', $date->toDateString())
                    ->where('period', $template->period)
                    ->exists();

                if ($exists) {
                    $skippedExisting++;

                    continue;
                }

                if ($dryRun) {
                    $created++;

                    continue;
                }

                DB::transaction(function () use ($template, $date, $enabledSlots, &$created): void {
                    $totalQuota = (int) $enabledSlots->sum('total');
                    $fee = $template->fee ?? $template->doctor?->fee;

                    $schedule = HsSchedule::query()->create([
                        'campus_id' => $template->campus_id,
                        'department_id' => $template->department_id,
                        'doctor_id' => $template->doctor_id,
                        'schedule_date' => $date->toDateString(),
                        'period' => $template->period,
                        'visit_type' => $template->visit_type,
                        'room' => $template->room,
                        'total_quota' => $totalQuota,
                        'status' => HsScheduleStatus::Normal,
                        'fee' => $fee,
                    ]);

                    foreach ($enabledSlots as $index => $slot) {
                        HsQuota::query()->create([
                            'schedule_id' => $schedule->id,
                            'start_time' => $slot->start_time,
                            'end_time' => $slot->end_time,
                            'total' => $slot->total,
                            'remaining' => $slot->total,
                            'locked' => 0,
                            'sort' => $slot->sort ?: ($index + 1),
                            'is_enabled' => true,
                        ]);
                    }

                    $created++;
                });
            }
        }

        return [
            'created_schedules' => $created,
            'skipped_existing' => $skippedExisting,
            'skipped_no_slots' => $skippedNoSlots,
            'days' => $days,
        ];
    }

    /**
     * 按预约规则提前放号天数，生成「今天 → 今天+N 天」窗口内的排班。
     *
     * @return array{created_schedules: int, skipped_existing: int, skipped_no_slots: int, days: int}
     */
    public function generateAdvanceWindow(bool $dryRun = false): array
    {
        $settings = HsAppointmentSetting::current();
        $from = now()->startOfDay();
        $to = now()->startOfDay()->addDays(max(0, (int) $settings->advance_days));

        return $this->generate($from, $to, $dryRun);
    }
}
