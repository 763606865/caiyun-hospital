<?php

namespace Tests\Feature\Api;

use App\Enums\HsCheckupGenderLimit;
use App\Enums\HsCheckupOrderStatus;
use App\Enums\HsPaymentStatus;
use App\Enums\HsSchedulePeriod;
use App\Enums\UserGender;
use App\Models\HsCampus;
use App\Models\HsCheckupPackage;
use App\Models\HsCheckupSetting;
use App\Models\HsCheckupSlot;
use App\Models\HsPatient;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckupApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_checkup_package_and_slot_endpoints(): void
    {
        [$package, $slot] = $this->seedBookableFixture();

        $this->getJson('/api/hospital/checkup-packages')
            ->assertOk()
            ->assertJsonPath('data.data.0.slug', $package->slug);

        $this->getJson('/api/hospital/checkup-packages/'.$package->slug)
            ->assertOk()
            ->assertJsonPath('data.name', $package->name);

        $this->getJson('/api/hospital/checkup-slots?package='.$package->slug)
            ->assertOk()
            ->assertJsonPath('data.data.0.id', $slot->id);

        $this->getJson('/api/hospital/checkup-settings')
            ->assertOk()
            ->assertJsonPath('data.booking_enabled', true);
    }

    public function test_user_can_book_and_cancel_checkup_order(): void
    {
        [$package, $slot, $user, $patient] = $this->seedBookableFixture();
        $token = $user->createToken('test')->plainTextToken;

        $book = $this->withToken($token)->postJson('/api/checkup-orders', [
            'patient_id' => $patient->id,
            'slot_id' => $slot->id,
            'remark' => '空腹',
        ]);

        $book->assertCreated()
            ->assertJsonPath('data.status', HsCheckupOrderStatus::Pending->value)
            ->assertJsonPath('data.package_id', $package->id);

        $orderNo = $book->json('data.order_no');
        $this->assertDatabaseHas('hs_checkup_slots', ['id' => $slot->id, 'remaining' => 9]);

        $this->withToken($token)->getJson('/api/checkup-orders')
            ->assertOk()
            ->assertJsonCount(1, 'data.data');

        $this->withToken($token)->postJson("/api/checkup-orders/{$orderNo}/cancel", [
            'reason' => '改期',
        ])->assertOk()->assertJsonPath('data.status', HsCheckupOrderStatus::Cancelled->value);

        $this->assertDatabaseHas('hs_checkup_slots', ['id' => $slot->id, 'remaining' => 10]);
    }

    public function test_booking_rejects_gender_mismatch(): void
    {
        [, $slot, $user, $patient] = $this->seedBookableFixture(
            genderLimit: HsCheckupGenderLimit::Male,
            patientGender: UserGender::Female,
        );
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/checkup-orders', [
            'patient_id' => $patient->id,
            'slot_id' => $slot->id,
        ])->assertStatus(409);
    }

    public function test_paid_checkup_package_creates_unpaid_order_when_payment_is_enabled(): void
    {
        SystemSetting::query()->create([
            'key' => SystemSetting::DEFAULT_KEY,
            'site_name' => '测试医院',
            'payment_enabled' => true,
        ]);
        [$package, $slot, $user, $patient] = $this->seedBookableFixture();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/checkup-orders', [
            'patient_id' => $patient->id,
            'slot_id' => $slot->id,
        ])->assertCreated()
            ->assertJsonPath('data.status', HsCheckupOrderStatus::Pending->value)
            ->assertJsonPath('data.payment_status', HsPaymentStatus::Unpaid->value);

        $this->withToken($token)->postJson('/api/checkup-orders/'.$response->json('data.order_no').'/cancel')
            ->assertOk()
            ->assertJsonPath('data.payment_status', HsPaymentStatus::Closed->value);

        $this->assertSame('299.00', $package->price);
    }

    /**
     * @return array{0: HsCheckupPackage, 1: HsCheckupSlot, 2: User, 3: HsPatient}
     */
    protected function seedBookableFixture(
        HsCheckupGenderLimit $genderLimit = HsCheckupGenderLimit::All,
        UserGender $patientGender = UserGender::Male,
        int $remaining = 10,
    ): array {
        HsCheckupSetting::query()->create([
            'key' => HsCheckupSetting::DEFAULT_KEY,
            'booking_enabled' => true,
            'advance_days' => 14,
            'cancel_hours_before' => 0,
            'no_show_limit' => 3,
            'no_show_ban_days' => 30,
        ]);

        $campus = HsCampus::query()->create(['name' => '主院区', 'slug' => 'main', 'is_enabled' => true]);
        $package = HsCheckupPackage::query()->create([
            'campus_id' => $campus->id,
            'name' => '入职体检套餐',
            'slug' => 'entry-check',
            'price' => 299,
            'gender_limit' => $genderLimit,
            'is_enabled' => true,
        ]);

        $slot = HsCheckupSlot::query()->create([
            'package_id' => $package->id,
            'campus_id' => $campus->id,
            'slot_date' => now()->addDay()->toDateString(),
            'period' => HsSchedulePeriod::Morning,
            'total' => 10,
            'remaining' => $remaining,
            'is_enabled' => true,
        ]);

        $user = User::factory()->create();
        $patient = HsPatient::query()->create([
            'user_id' => $user->id,
            'name' => '体检人',
            'id_number' => '110101199001011234',
            'phone' => '13800138000',
            'gender' => $patientGender,
            'is_default' => true,
        ]);

        return [$package, $slot, $user, $patient];
    }
}
