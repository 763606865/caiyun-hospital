<?php

namespace App\Admin\Resources\HsQuotas\Pages;

use App\Admin\Resources\HsQuotas\HsQuotaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsQuotas extends ListRecords
{
    protected static string $resource = HsQuotaResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
