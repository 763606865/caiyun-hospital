<?php

namespace App\Admin\Resources\HsAppointments\Pages;

use App\Admin\Resources\HsAppointments\HsAppointmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsAppointments extends ListRecords
{
    protected static string $resource = HsAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
