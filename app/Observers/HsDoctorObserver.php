<?php

namespace App\Observers;

use App\Models\HsDoctor;
use App\Services\Hospital\ScheduleTemplateBootstrapService;
use Throwable;

use function Illuminate\Support\defer;

/**
 * 新建医生后，自动按主科室一键生成出诊周模板（需等科室关联完成，故 defer）。
 */
class HsDoctorObserver
{
    public function created(HsDoctor $doctor): void
    {
        $doctorId = (int) $doctor->getKey();

        defer(function () use ($doctorId): void {
            $doctor = HsDoctor::query()->find($doctorId);
            if (! $doctor) {
                return;
            }

            try {
                app(ScheduleTemplateBootstrapService::class)->bootstrapForDoctor($doctor);
            } catch (Throwable $exception) {
                report($exception);
            }
        }, name: "hs-doctor-bootstrap-templates:{$doctorId}");
    }
}
