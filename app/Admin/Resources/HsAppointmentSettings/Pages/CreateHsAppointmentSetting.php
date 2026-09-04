<?php

namespace App\Admin\Resources\HsAppointmentSettings\Pages;

use App\Admin\Resources\HsAppointmentSettings\HsAppointmentSettingResource;
use App\Models\HsAppointmentSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateHsAppointmentSetting extends CreateRecord
{
    protected static string $resource = HsAppointmentSettingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['key'] = HsAppointmentSetting::DEFAULT_KEY;

        return $data;
    }
}
