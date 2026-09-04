<?php

namespace Tests\Feature\Api;

use App\Enums\HsAppointmentStatus;
use App\Enums\HsPatientIdType;
use App\Enums\HsSchedulePeriod;
use App\Enums\HsScheduleStatus;
use App\Models\HsAppointmentSetting;
use App\Models\HsCampus;
use App\Models\HsDepartment;
use App\Models\HsDoctor;
use App\Models\HsPatient;
use App\Models\HsQuota;
use App\Models\HsSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HospitalPatientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_hospital_endpoints_return_enabled_master_data(): void
    {
        $campus = HsCampus::query()->create(['name' => '主院区', 'slug' => 'main', 'is_enabled' => true]);
        HsCampus::query()->create(['name' => '停用院区', 'slug' => 'off', 'is_enabled' => false]);

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

        $this->getJson('/api/hospital/campuses')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'main');

        $this->getJson('/api/hospital/departments?campus=main')
            ->assertOk()
            ->assertJsonPath('data.data.0.slug', 'cardiology');

        $this->getJson('/api/hospital/doctors?department=cardiology')
            ->assertOk()
            ->assertJsonPath('data.data.0.slug', 'zhang-wei');

        $this->getJson('/api/hospital/doctors/zhang-wei')
            ->assertOk()
            ->assertJsonPath('data.name', '张伟');
    }

    public function test_patient_crud_requires_auth_and_scopes_to_current_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->postJson('/api/patients', [
            'name' => '本人',
            'id_number' => '110101199001011234',
            'phone' => '13800138000',
        ])->assertUnauthorized();

        $this->withToken($token)->postJson('/api/patients', [
            'name' => '本人',
            'id_type' => HsPatientIdType::IdCard->value,
            'id_number' => '110101199001011234',
            'phone' => '13800138000',
            'is_default' => true,
        ])->assertCreated()->assertJsonPath('data.name', '本人');

        $otherPatient = HsPatient::query()->create([
            'user_id' => $other->id,
            'name' => '别人',
            'id_number' => '110101199001011111',
            'phone' => '13900139000',
        ]);

        $this->withToken($token)->getJson('/api/patients')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->withToken($token)->getJson('/api/patients/'.$otherPatient->id)
            ->assertNotFound();
    }

    public function test_user_can_book_and_cancel_appointment(): void
    {
        [$user, $patient, $quota] = $this->seedBookableFixture();
        $token = $user->createToken('test')->plainTextToken;

        $book = $this->withToken($token)->postJson('/api/appointments', [
            'patient_id' => $patient->id,
            'quota_id' => $quota->id,
            'remark' => '初诊',
        ]);

        $book->assertCreated()
            ->assertJsonPath('data.status', HsAppointmentStatus::Pending->value)
            ->assertJsonPath('data.patient_id', $patient->id);

        $appointmentNo = $book->json('data.appointment_no');
        $this->assertDatabaseHas('hs_quotas', ['id' => $quota->id, 'remaining' => 4]);

        $this->withToken($token)->getJson('/api/appointments')
            ->assertOk()
            ->assertJsonCount(1, 'data.data');

        $this->withToken($token)->postJson("/api/appointments/{$appointmentNo}/cancel", [
            'reason' => '行程冲突',
        ])->assertOk()->assertJsonPath('data.status', HsAppointmentStatus::Cancelled->value);

        $this->assertDatabaseHas('hs_quotas', ['id' => $quota->id, 'remaining' => 5]);
    }

    public function test_booking_fails_when_quota_is_empty(): void
    {
        [$user, $patient, $quota] = $this->seedBookableFixture(remaining: 0);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/appointments', [
            'patient_id' => $patient->id,
            'quota_id' => $quota->id,
        ])->assertStatus(409);
    }

    /**
     * @return array{0: User, 1: HsPatient, 2: HsQuota}
     */
    protected function seedBookableFixture(int $remaining = 5): array
    {
        HsAppointmentSetting::query()->create([
            'key' => HsAppointmentSetting::DEFAULT_KEY,
            'registration_enabled' => true,
            'advance_days' => 7,
            'cancel_hours_before' => 0,
            'no_show_limit' => 3,
            'no_show_ban_days' => 30,
        ]);

        $user = User::factory()->create();
        $patient = HsPatient::query()->create([
            'user_id' => $user->id,
            'name' => '就诊人',
            'id_number' => '110101199001011234',
            'phone' => '13800138000',
            'is_default' => true,
        ]);

        $campus = HsCampus::query()->create(['name' => '主院区', 'slug' => 'main', 'is_enabled' => true]);
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

        $schedule = HsSchedule::query()->create([
            'campus_id' => $campus->id,
            'department_id' => $department->id,
            'doctor_id' => $doctor->id,
            'schedule_date' => now()->addDay()->toDateString(),
            'period' => HsSchedulePeriod::Morning,
            'total_quota' => 5,
            'status' => HsScheduleStatus::Normal,
            'fee' => 50,
        ]);

        $quota = HsQuota::query()->create([
            'schedule_id' => $schedule->id,
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'total' => 5,
            'remaining' => $remaining,
            'locked' => 0,
            'sort' => 1,
            'is_enabled' => true,
        ]);

        return [$user, $patient, $quota];
    }
}
