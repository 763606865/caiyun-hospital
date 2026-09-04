<?php

namespace App\Admin\Resources\HsPatients\Pages;

use App\Admin\Resources\HsPatients\HsPatientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsPatient extends CreateRecord
{
    protected static string $resource = HsPatientResource::class;
}
