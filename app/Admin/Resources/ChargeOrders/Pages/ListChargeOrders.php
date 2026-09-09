<?php

namespace App\Admin\Resources\ChargeOrders\Pages;

use App\Admin\Resources\ChargeOrders\ChargeOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChargeOrders extends ListRecords
{
    protected static string $resource = ChargeOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
