<?php

namespace App\Admin\Resources\HsQuotas\Pages;

use App\Admin\Resources\HsQuotas\HsQuotaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHsQuota extends EditRecord
{
    protected static string $resource = HsQuotaResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
