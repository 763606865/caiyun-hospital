<?php

namespace App\Admin\Resources\HsSchedules\Pages;

use App\Admin\Resources\HsSchedules\HsScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsSchedule extends CreateRecord
{
    protected static string $resource = HsScheduleResource::class;
}
