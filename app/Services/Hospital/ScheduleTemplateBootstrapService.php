<?php

namespace App\Services\Hospital;

use App\Enums\HsSchedulePeriod;
use App\Enums\HsVisitType;
use App\Enums\HsWeekday;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
use App\Models\HsScheduleTemplate;
use App\Models\HsScheduleTemplateSlot;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * 为一键生成医生「周一～周日 × 上/下/晚」出诊周模板及默认号源时段。
 */
class ScheduleTemplateBootstrapService
{
    /** 默认每时段号源量 */
    public const DEFAULT_SLOT_TOTAL = 5;

    /** 默认时段长度（分钟） */
    public const DEFAULT_SLOT_MINUTES = 30;

    /**
     * @return array{
     *     created_templates: int,
     *     skipped_existing: int,
     *     created_slots: int,
     * }
     */
    public function bootstrap(
        HsDoctor $doctor,
        HsDepartment $department,
        int $slotTotal = self::DEFAULT_SLOT_TOTAL,
        int $slotMinutes = self::DEFAULT_SLOT_MINUTES,
    ): array {
        if ((int) $department->campus_id <= 0) {
            throw new InvalidArgumentException('所选科室未关联院区。');
        }

        $belongsToDoctor = $doctor->departments()
            ->where('hs_departments.id', $department->id)
            ->exists();

        if (! $belongsToDoctor) {
            throw new InvalidArgumentException('所选科室不属于该医生。');
        }

        $createdTemplates = 0;
        $skippedExisting = 0;
        $createdSlots = 0;

        $periodWindows = $this->periodWindows();

        DB::transaction(function () use (
            $doctor,
            $department,
            $slotTotal,
            $slotMinutes,
            $periodWindows,
            &$createdTemplates,
            &$skippedExisting,
            &$createdSlots,
        ): void {
            foreach (HsWeekday::cases() as $weekday) {
                foreach ($periodWindows as $periodValue => [$start, $end]) {
                    $period = HsSchedulePeriod::from($periodValue);

                    $exists = HsScheduleTemplate::query()
                        ->where('doctor_id', $doctor->id)
                        ->where('department_id', $department->id)
                        ->where('weekday', $weekday)
                        ->where('period', $period)
                        ->exists();

                    if ($exists) {
                        $skippedExisting++;

                        continue;
                    }

                    $template = HsScheduleTemplate::query()->create([
                        'campus_id' => $department->campus_id,
                        'department_id' => $department->id,
                        'doctor_id' => $doctor->id,
                        'weekday' => $weekday,
                        'period' => $period,
                        'visit_type' => HsVisitType::Normal,
                        'fee' => null,
                        'is_enabled' => true,
                    ]);

                    $slots = $this->buildSlots($start, $end, $slotMinutes, $slotTotal);
                    foreach ($slots as $slot) {
                        HsScheduleTemplateSlot::query()->create([
                            'template_id' => $template->id,
                            ...$slot,
                        ]);
                        $createdSlots++;
                    }

                    $createdTemplates++;
                }
            }
        });

        return [
            'created_templates' => $createdTemplates,
            'skipped_existing' => $skippedExisting,
            'created_slots' => $createdSlots,
        ];
    }

    /**
     * 按医生主科室（或第一所属科室）生成模板；无科室或已有模板时跳过。
     *
     * @return array{
     *     created_templates: int,
     *     skipped_existing: int,
     *     created_slots: int,
     * }|null
     */
    public function bootstrapForDoctor(HsDoctor $doctor): ?array
    {
        $department = $doctor->departments()
            ->wherePivot('is_primary', true)
            ->first()
            ?? $doctor->departments()->orderBy('hs_departments.sort')->first();

        if (! $department) {
            return null;
        }

        if ($doctor->scheduleTemplates()->exists()) {
            return [
                'created_templates' => 0,
                'skipped_existing' => 0,
                'created_slots' => 0,
            ];
        }

        return $this->bootstrap($doctor, $department);
    }

    /**
     * 普遍门诊工作时间窗。
     *
     * @return array<string, array{0: string, 1: string}>
     */
    protected function periodWindows(): array
    {
        return [
            HsSchedulePeriod::Morning->value => ['08:00', '12:00'],
            HsSchedulePeriod::Afternoon->value => ['13:30', '17:30'],
            HsSchedulePeriod::Evening->value => ['17:00', '23:00'],
        ];
    }

    /**
     * @return list<array{start_time: string, end_time: string, total: int, sort: int, is_enabled: bool}>
     */
    protected function buildSlots(string $start, string $end, int $minutes, int $total): array
    {
        $minutes = max(5, $minutes);
        $total = max(0, $total);

        $cursor = CarbonImmutable::parse('2000-01-01 '.$start);
        $endAt = CarbonImmutable::parse('2000-01-01 '.$end);

        $slots = [];
        $sort = 1;

        while ($cursor->addMinutes($minutes)->lte($endAt)) {
            $slotEnd = $cursor->addMinutes($minutes);
            $slots[] = [
                'start_time' => $cursor->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'total' => $total,
                'sort' => $sort,
                'is_enabled' => true,
            ];
            $cursor = $slotEnd;
            $sort++;
        }

        return $slots;
    }
}
