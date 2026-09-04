<?php

namespace Tests\Feature\Api;

use App\Enums\HsAppointmentStatus;
use App\Enums\HsSchedulePeriod;
use App\Enums\HsScheduleStatus;
use App\Enums\HsVisitType;
use App\Enums\UserGender;
use App\Models\HsAppointmentSetting;
use App\Models\HsCampus;
use App\Models\HsDepartment;
use App\Models\HsDepartmentCategory;
use App\Models\HsDoctor;
use App\Models\HsPatient;
use App\Models\HsQuota;
use App\Models\HsSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientAppDesignApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_hospital_home_and_department_categories(): void
    {
        $campus = HsCampus::query()->create(['name' => '总院', 'slug' => 'main', 'is_enabled' => true]);
        $internal = HsDepartmentCategory::query()->create([
            'name' => '内科系',
            'slug' => 'internal',
            'sort' => 1,
            'is_enabled' => true,
        ]);
        $surgery = HsDepartmentCategory::query()->create([
            'name' => '外科系',
            'slug' => 'surgery',
            'sort' => 2,
            'is_enabled' => true,
        ]);
        HsDepartment::query()->create([
            'campus_id' => $campus->id,
            'category_id' => $internal->id,
            'name' => '内分泌一科',
            'slug' => 'endo-1',
            'is_featured' => true,
            'is_enabled' => true,
        ]);
        HsDepartment::query()->create([
            'campus_id' => $campus->id,
            'category_id' => $surgery->id,
            'name' => '普外科',
            'slug' => 'general-surgery',
            'is_featured' => false,
            'is_enabled' => true,
        ]);

        $this->getJson('/api/hospital/home')
            ->assertOk()
            ->assertJsonPath('data.featured_departments.0.slug', 'endo-1')
            ->assertJsonPath('data.featured_departments.0.category.slug', 'internal');

        $this->getJson('/api/hospital/department-categories?campus=main')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'internal', 'department_count' => 1]);

        $this->getJson('/api/hospital/departments?'.http_build_query([
            'campus' => 'main',
            'category' => 'internal',
            'with_today_remaining' => 1,
        ]))
            ->assertOk()
            ->assertJsonPath('data.data.0.slug', 'endo-1')
            ->assertJsonPath('data.data.0.category.slug', 'internal')
            ->assertJsonPath('data.data.0.today_status', 'none');
    }

    public function test_doctors_with_today_schedule_and_booking_returns_ticket(): void
    {
        HsAppointmentSetting::query()->create([
            'key' => HsAppointmentSetting::DEFAULT_KEY,
            'registration_enabled' => true,
            'advance_days' => 7,
            'cancel_hours_before' => 0,
        ]);

        $campus = HsCampus::query()->create(['name' => '总院', 'slug' => 'main', 'is_enabled' => true]);
        $category = HsDepartmentCategory::query()->create([
            'name' => '内科系',
            'slug' => 'internal',
            'is_enabled' => true,
        ]);
        $department = HsDepartment::query()->create([
            'campus_id' => $campus->id,
            'category_id' => $category->id,
            'name' => '内分泌一科',
            'slug' => 'endo-1',
            'location' => '3号楼 2层 A诊区',
            'is_enabled' => true,
        ]);
        $doctor = HsDoctor::query()->create([
            'name' => '周晓芳',
            'slug' => 'zhou',
            'title' => '主任医师',
            'fee' => 50,
            'is_enabled' => true,
        ]);
        $doctor->departments()->attach($department->id, ['is_primary' => true]);

        $schedule = HsSchedule::query()->create([
            'campus_id' => $campus->id,
            'department_id' => $department->id,
            'doctor_id' => $doctor->id,
            'schedule_date' => now()->toDateString(),
            'period' => HsSchedulePeriod::Morning,
            'visit_type' => HsVisitType::Expert,
            'room' => 'A01',
            'total_quota' => 5,
            'status' => HsScheduleStatus::Normal,
            'fee' => 50,
        ]);
        $quota = HsQuota::query()->create([
            'schedule_id' => $schedule->id,
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'total' => 5,
            'remaining' => 5,
            'is_enabled' => true,
        ]);

        $this->getJson('/api/hospital/doctors?department=endo-1&with_today_schedule=1&visit_type=expert')
            ->assertOk()
            ->assertJsonPath('data.data.0.slug', 'zhou')
            ->assertJsonPath('data.data.0.today_status', 'available')
            ->assertJsonPath('data.data.0.today_remaining', 5);

        $user = User::factory()->create(['real_name' => '张明']);
        $patient = HsPatient::query()->create([
            'user_id' => $user->id,
            'name' => '张大爷',
            'id_number' => '110101195001011234',
            'phone' => '13800138000',
            'gender' => UserGender::Male,
            'birthday' => '2015-01-01',
            'is_default' => true,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/me/overview')
            ->assertOk()
            ->assertJsonPath('data.user.is_verified', true)
            ->assertJsonPath('data.default_patient.name', '张大爷')
            ->assertJsonPath('data.default_patient.is_minor', true)
            ->assertJsonPath('data.stats.patients', 1);

        $book = $this->withToken($token)->postJson('/api/appointments', [
            'patient_id' => $patient->id,
            'quota_id' => $quota->id,
        ])->assertCreated();

        $book->assertJsonPath('data.status', HsAppointmentStatus::Pending->value)
            ->assertJsonPath('data.ticket_no', 'A-01')
            ->assertJsonPath('data.voucher_code', $book->json('data.appointment_no'));

        $this->withToken($token)->getJson('/api/appointments?tab=ticket&patient_id='.$patient->id)
            ->assertOk()
            ->assertJsonCount(1, 'data.data');
    }
}
