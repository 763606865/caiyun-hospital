<?php

namespace App\Admin\Resources\InventoryStocks\Pages;

use App\Admin\Resources\InventoryStocks\InventoryStockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryStock extends EditRecord
{
    protected static string $resource = InventoryStockResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
