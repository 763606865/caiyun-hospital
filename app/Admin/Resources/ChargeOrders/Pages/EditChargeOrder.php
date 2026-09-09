<?php

namespace App\Admin\Resources\ChargeOrders\Pages;

use App\Admin\Resources\ChargeOrders\ChargeOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChargeOrder extends EditRecord
{
    protected static string $resource = ChargeOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
