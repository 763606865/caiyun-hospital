<?php

namespace App\Admin\Resources\MedicalRecords\Pages;

use App\Admin\Resources\MedicalRecords\MedicalRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;
}
