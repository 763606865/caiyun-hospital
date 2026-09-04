<?php

namespace App\Admin\Resources\HsCheckupOrders\Pages;

use App\Admin\Resources\HsCheckupOrders\HsCheckupOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsCheckupOrder extends CreateRecord
{
    protected static string $resource = HsCheckupOrderResource::class;
}
