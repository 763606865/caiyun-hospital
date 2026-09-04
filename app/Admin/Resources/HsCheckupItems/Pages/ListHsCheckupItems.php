<?php

namespace App\Admin\Resources\HsCheckupItems\Pages;

use App\Admin\Resources\HsCheckupItems\HsCheckupItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsCheckupItems extends ListRecords
{
    protected static string $resource = HsCheckupItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
