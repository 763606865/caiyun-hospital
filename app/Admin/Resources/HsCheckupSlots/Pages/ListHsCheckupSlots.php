<?php

namespace App\Admin\Resources\HsCheckupSlots\Pages;

use App\Admin\Resources\HsCheckupSlots\HsCheckupSlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsCheckupSlots extends ListRecords
{
    protected static string $resource = HsCheckupSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
