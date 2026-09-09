<?php

namespace App\Admin\Resources\MedicalRecords\Pages;

use App\Admin\Resources\MedicalRecords\MedicalRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicalRecord extends EditRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
