<?php

namespace Tests\Feature\Hospital;

use App\Enums\HsSchedulePeriod;
use App\Enums\HsWeekday;
use App\Models\HsCampus;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
use App\Models\HsScheduleTemplate;
use App\Models\HsScheduleTemplateSlot;
use App\Services\Hospital\ScheduleTemplateBootstrapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Illuminate\Support\defer;

class ScheduleTemplateBootstrapTest extends TestCase
{
    use RefreshDatabase;

    public function test_bootstrap_creates_full_week_templates_and_slots(): void
    {
        [$doctor, $department] = $this->seedDoctorWithDepartment();

        $result = app(ScheduleTemplateBootstrapService::class)->bootstrap($doctor, $department);

        $this->assertSame(21, $result['created_templates']); // 7 days × 3 periods
        $this->assertSame(0, $result['skipped_existing']);
        // morning 8 + afternoon 8 + evening 12 = 28 slots/day × 7 = 196
        $this->assertSame(196, $result['created_slots']);
        $this->assertDatabaseCount('hs_schedule_templates', 21);
        $this->assertDatabaseCount('hs_schedule_template_slots', 196);

        $morning = HsScheduleTemplate::query()
            ->where('doctor_id', $doctor->id)
            ->where('weekday', HsWeekday::Monday)
            ->where('period', HsSchedulePeriod::Morning)
            ->firstOrFail();

        $this->assertDatabaseHas('hs_schedule_template_slots', [
            'template_id' => $morning->id,
            'start_time' => '08:00:00',
            'end_time' => '08:30:00',
            'total' => 5,
        ]);
        $this->assertTrue(
            HsScheduleTemplateSlot::query()
                ->where('template_id', $morning->id)
                ->where('start_time', '11:30:00')
                ->where('end_time', '12:00:00')
                ->exists()
        );

        // 再次执行应全部跳过
        $again = app(ScheduleTemplateBootstrapService::class)->bootstrap($doctor, $department);
        $this->assertSame(0, $again['created_templates']);
        $this->assertSame(21, $again['skipped_existing']);
        $this->assertDatabaseCount('hs_schedule_templates', 21);
    }

    public function test_creating_doctor_observer_bootstraps_templates_after_department_attached(): void
    {
        $campus = HsCampus::query()->create(['name' => '总院', 'slug' => 'main', 'is_enabled' => true]);
        $department = HsDepartment::query()->create([
            'campus_id' => $campus->id,
            'name' => '心内科',
            'slug' => 'cardiology',
            'is_enabled' => true,
        ]);

        $doctor = HsDoctor::query()->create([
            'name' => '李强',
            'slug' => 'li-qiang',
            'fee' => 30,
            'is_enabled' => true,
        ]);

        $this->assertDatabaseCount('hs_schedule_templates', 0);

        $doctor->departments()->attach($department->id, ['is_primary' => true]);

        defer()->invoke();

        $this->assertDatabaseCount('hs_schedule_templates', 21);
        $this->assertDatabaseCount('hs_schedule_template_slots', 196);
    }

    /**
     * @return array{0: HsDoctor, 1: HsDepartment}
     */
    protected function seedDoctorWithDepartment(): array
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

        // 避免本用例手动 bootstrap 与 Observer defer 互相干扰
        defer()->forget("hs-doctor-bootstrap-templates:{$doctor->id}");

        return [$doctor, $department];
    }
}
