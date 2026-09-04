<?php

namespace App\Admin\Resources\HsPatients\Pages;

use App\Admin\Resources\HsPatients\HsPatientResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsPatient extends EditRecord
{
    protected static string $resource = HsPatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
