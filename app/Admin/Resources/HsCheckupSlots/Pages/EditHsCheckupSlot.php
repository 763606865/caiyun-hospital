<?php

namespace App\Admin\Resources\HsCheckupSlots\Pages;

use App\Admin\Resources\HsCheckupSlots\HsCheckupSlotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHsCheckupSlot extends EditRecord
{
    protected static string $resource = HsCheckupSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
