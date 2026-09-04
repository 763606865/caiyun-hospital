<?php

namespace App\Admin\Resources\HsAppointmentSettings\Pages;

use App\Admin\Resources\HsAppointmentSettings\HsAppointmentSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsAppointmentSettings extends ListRecords
{
    protected static string $resource = HsAppointmentSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
