<?php

namespace App\Admin\Resources\HsAppointmentSettings\Pages;

use App\Admin\Resources\HsAppointmentSettings\HsAppointmentSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditHsAppointmentSetting extends EditRecord
{
    protected static string $resource = HsAppointmentSettingResource::class;
}
