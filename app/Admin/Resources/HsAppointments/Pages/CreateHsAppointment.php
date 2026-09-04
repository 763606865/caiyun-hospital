<?php

namespace App\Admin\Resources\HsAppointments\Pages;

use App\Admin\Resources\HsAppointments\HsAppointmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsAppointment extends CreateRecord
{
    protected static string $resource = HsAppointmentResource::class;
}
