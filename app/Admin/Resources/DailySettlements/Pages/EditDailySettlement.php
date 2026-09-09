<?php

namespace App\Admin\Resources\DailySettlements\Pages;

use App\Admin\Resources\DailySettlements\DailySettlementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailySettlement extends EditRecord
{
    protected static string $resource = DailySettlementResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
