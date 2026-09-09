<?php

namespace App\Admin\Resources\InventoryMovements\Pages;

use App\Admin\Resources\InventoryMovements\InventoryMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryMovement extends EditRecord
{
    protected static string $resource = InventoryMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
