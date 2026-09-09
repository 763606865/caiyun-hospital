<?php

namespace App\Admin\Resources\DailySettlements\Pages;

use App\Admin\Resources\DailySettlements\DailySettlementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailySettlements extends ListRecords
{
    protected static string $resource = DailySettlementResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
