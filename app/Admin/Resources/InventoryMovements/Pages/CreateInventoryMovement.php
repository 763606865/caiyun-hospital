<?php

namespace App\Admin\Resources\InventoryMovements\Pages;

use App\Admin\Resources\InventoryMovements\InventoryMovementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryMovement extends CreateRecord
{
    protected static string $resource = InventoryMovementResource::class;
}
