<?php

namespace App\Admin\Resources\HsCheckupSettings\Pages;

use App\Admin\Resources\HsCheckupSettings\HsCheckupSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsCheckupSettings extends ListRecords
{
    protected static string $resource = HsCheckupSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
