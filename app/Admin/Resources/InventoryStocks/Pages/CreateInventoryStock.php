<?php

namespace App\Admin\Resources\InventoryStocks\Pages;

use App\Admin\Resources\InventoryStocks\InventoryStockResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryStock extends CreateRecord
{
    protected static string $resource = InventoryStockResource::class;
}
