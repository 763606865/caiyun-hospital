<?php

namespace App\Console\Commands;

use App\Models\HsAppointmentSetting;
use App\Services\Hospital\ScheduleGenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

class GenerateHospitalSchedulesCommand extends Command
{
    protected $signature = 'hospital:generate-schedules
        {--days= : 覆盖预约规则，指定从今天起生成多少天（含今天）}
        {--from= : 开始日期 Y-m-d}
        {--to= : 结束日期 Y-m-d}
        {--dry-run : 仅统计将生成数量，不写库}';

    protected $description = '按出诊周模板批量生成排班与号源（默认覆盖预约规则提前放号天数窗口）';

    public function handle(ScheduleGenerationService $generator): int
    {
        try {
            [$from, $to] = $this->resolveDateRange();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $this->info(sprintf(
            '生成窗口：%s ~ %s%s',
            $from->toDateString(),
            $to->toDateString(),
            $dryRun ? '（dry-run）' : '',
        ));

        $result = $generator->generate($from, $to, $dryRun);

        $this->table(
            ['项目', '数量'],
            [
                ['覆盖天数', $result['days']],
                [$dryRun ? '将新建排班' : '新建排班', $result['created_schedules']],
                ['已存在跳过', $result['skipped_existing']],
                ['无时段模板跳过', $result['skipped_no_slots']],
            ],
        );

        return self::SUCCESS;
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    protected function resolveDateRange(): array
    {
        $fromOption = $this->option('from');
        $toOption = $this->option('to');
        $daysOption = $this->option('days');

        if (filled($fromOption) || filled($toOption)) {
            if (! filled($fromOption) || ! filled($toOption)) {
                throw new \InvalidArgumentException('--from 与 --to 需同时指定。');
            }

            return [
                CarbonImmutable::parse((string) $fromOption)->startOfDay(),
                CarbonImmutable::parse((string) $toOption)->startOfDay(),
            ];
        }

        if (filled($daysOption)) {
            $days = max(0, (int) $daysOption);
            $from = CarbonImmutable::now()->startOfDay();

            return [$from, $from->addDays($days)];
        }

        $settingsDays = max(0, (int) HsAppointmentSetting::current()->advance_days);
        $from = CarbonImmutable::now()->startOfDay();

        return [$from, $from->addDays($settingsDays)];
    }
}
