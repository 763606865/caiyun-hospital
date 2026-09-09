<?php

namespace App\Admin\Resources\InventoryStocks\Pages;

use App\Admin\Resources\InventoryStocks\InventoryStockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventoryStocks extends ListRecords
{
    protected static string $resource = InventoryStockResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
