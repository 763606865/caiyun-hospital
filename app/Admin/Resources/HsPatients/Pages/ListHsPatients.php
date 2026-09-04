<?php

namespace App\Admin\Resources\HsPatients\Pages;

use App\Admin\Resources\HsPatients\HsPatientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsPatients extends ListRecords
{
    protected static string $resource = HsPatientResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
