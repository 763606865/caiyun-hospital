<?php

namespace Tests\Feature\Services;

use App\Enums\HsSchedulePeriod;
use App\Models\HsCampus;
use App\Models\HsCheckupPackage;
use App\Models\HsCheckupSlot;
use App\Services\Hospital\CheckupSlotGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckupSlotGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_selected_slots_and_skips_existing_ones(): void
    {
        $campus = HsCampus::query()->create([
            'name' => '主院区',
            'slug' => 'main',
            'is_enabled' => true,
        ]);
        $packages = collect([
            ['name' => '基础套餐', 'slug' => 'basic'],
            ['name' => '入职套餐', 'slug' => 'employment'],
        ])->map(fn (array $package): HsCheckupPackage => HsCheckupPackage::query()->create([
            ...$package,
            'campus_id' => $campus->id,
            'price' => 0,
            'is_enabled' => true,
        ]));

        HsCheckupSlot::query()->create([
            'package_id' => $packages[0]->id,
            'campus_id' => $campus->id,
            'slot_date' => '2026-09-07',
            'period' => HsSchedulePeriod::Morning,
            'total' => 20,
            'remaining' => 5,
            'is_enabled' => true,
        ]);

        $generator = app(CheckupSlotGenerationService::class);
        $arguments = [
            $packages->pluck('id')->all(),
            '2026-09-07',
            '2026-09-08',
            [1, 2],
            [HsSchedulePeriod::Morning->value, HsSchedulePeriod::Afternoon->value],
            50,
        ];

        $firstResult = $generator->generate(...$arguments);

        $this->assertSame([
            'created_slots' => 7,
            'skipped_existing' => 1,
            'days' => 2,
        ], $firstResult);
        $this->assertDatabaseCount('hs_checkup_slots', 8);
        $this->assertDatabaseHas('hs_checkup_slots', [
            'package_id' => $packages[0]->id,
            'slot_date' => '2026-09-07 00:00:00',
            'period' => HsSchedulePeriod::Morning->value,
            'total' => 20,
            'remaining' => 5,
        ]);

        $secondResult = $generator->generate(...$arguments);

        $this->assertSame([
            'created_slots' => 0,
            'skipped_existing' => 8,
            'days' => 2,
        ], $secondResult);
        $this->assertDatabaseCount('hs_checkup_slots', 8);
    }
}
