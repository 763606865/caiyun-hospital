<?php

namespace App\Admin\Resources\HsAppointments\Pages;

use App\Admin\Resources\HsAppointments\HsAppointmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsAppointment extends EditRecord
{
    protected static string $resource = HsAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
