<?php

namespace App\Admin\Resources\Prescriptions\Pages;

use App\Admin\Resources\Prescriptions\PrescriptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrescription extends CreateRecord
{
    protected static string $resource = PrescriptionResource::class;
}
