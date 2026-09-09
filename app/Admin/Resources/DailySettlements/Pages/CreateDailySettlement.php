<?php

namespace App\Admin\Resources\DailySettlements\Pages;

use App\Admin\Resources\DailySettlements\DailySettlementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailySettlement extends CreateRecord
{
    protected static string $resource = DailySettlementResource::class;
}
