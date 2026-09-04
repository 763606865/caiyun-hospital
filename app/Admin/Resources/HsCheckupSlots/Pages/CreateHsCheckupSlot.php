<?php

namespace App\Admin\Resources\HsCheckupSlots\Pages;

use App\Admin\Resources\HsCheckupSlots\HsCheckupSlotResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsCheckupSlot extends CreateRecord
{
    protected static string $resource = HsCheckupSlotResource::class;
}
