<?php

namespace Tests\Feature\Hospital;

use App\Enums\HsSchedulePeriod;
use App\Enums\HsVisitType;
use App\Enums\HsWeekday;
use App\Models\HsAppointmentSetting;
use App\Models\HsCampus;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
use App\Models\HsSchedule;
use App\Models\HsScheduleTemplate;
use App\Models\HsScheduleTemplateSlot;
use App\Services\Hospital\ScheduleGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ScheduleGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_generates_schedules_for_advance_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-07 10:00:00')); // 周一

        HsAppointmentSetting::query()->create([
            'key' => HsAppointmentSetting::DEFAULT_KEY,
            'advance_days' => 2,
            'registration_enabled' => true,
        ]);

        [$template] = $this->seedTemplate(HsWeekday::Monday);

        $this->artisan('hospital:generate-schedules')
            ->assertSuccessful();

        $this->assertDatabaseCount('hs_schedules', 1);
        $this->assertTrue(
            HsSchedule::query()
                ->where('doctor_id', $template->doctor_id)
                ->where('department_id', $template->department_id)
                ->whereDate('schedule_date', '2026-09-07')
                ->where('period', HsSchedulePeriod::Morning)
                ->exists()
        );
        $this->assertDatabaseCount('hs_quotas', 2);

        // 再次执行应跳过已存在
        $this->artisan('hospital:generate-schedules')->assertSuccessful();
        $this->assertDatabaseCount('hs_schedules', 1);

        Carbon::setTestNow();
    }

    public function test_dry_run_does_not_persist(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-07 10:00:00'));

        HsAppointmentSetting::query()->create([
            'key' => HsAppointmentSetting::DEFAULT_KEY,
            'advance_days' => 0,
        ]);

        $this->seedTemplate(HsWeekday::Monday);

        $result = app(ScheduleGenerationService::class)->generateAdvanceWindow(dryRun: true);

        $this->assertSame(1, $result['created_schedules']);
        $this->assertDatabaseCount('hs_schedules', 0);

        Carbon::setTestNow();
    }

    /**
     * @return array{0: HsScheduleTemplate}
     */
    protected function seedTemplate(HsWeekday $weekday): array
    {
        $campus = HsCampus::query()->create(['name' => '总院', 'slug' => 'main', 'is_enabled' => true]);
        $department = HsDepartment::query()->create([
            'campus_id' => $campus->id,
            'name' => '心内科',
            'slug' => 'cardiology',
            'is_enabled' => true,
        ]);
        $doctor = HsDoctor::query()->create([
            'name' => '张伟',
            'slug' => 'zhang-wei',
            'fee' => 50,
            'is_enabled' => true,
        ]);
        $doctor->departments()->attach($department->id, ['is_primary' => true]);

        $template = HsScheduleTemplate::query()->create([
            'campus_id' => $campus->id,
            'department_id' => $department->id,
            'doctor_id' => $doctor->id,
            'weekday' => $weekday,
            'period' => HsSchedulePeriod::Morning,
            'visit_type' => HsVisitType::Expert,
            'room' => 'A01',
            'is_enabled' => true,
        ]);

        HsScheduleTemplateSlot::query()->create([
            'template_id' => $template->id,
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'total' => 5,
            'sort' => 1,
            'is_enabled' => true,
        ]);
        HsScheduleTemplateSlot::query()->create([
            'template_id' => $template->id,
            'start_time' => '09:30:00',
            'end_time' => '10:00:00',
            'total' => 5,
            'sort' => 2,
            'is_enabled' => true,
        ]);

        return [$template];
    }
}
