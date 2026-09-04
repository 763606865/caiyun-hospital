<?php

namespace App\Admin\Resources\HsCheckupOrders\Pages;

use App\Admin\Resources\HsCheckupOrders\HsCheckupOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsCheckupOrders extends ListRecords
{
    protected static string $resource = HsCheckupOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
