<?php

namespace App\Admin\Resources\ChargeOrders\Pages;

use App\Admin\Resources\ChargeOrders\ChargeOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChargeOrder extends CreateRecord
{
    protected static string $resource = ChargeOrderResource::class;
}
