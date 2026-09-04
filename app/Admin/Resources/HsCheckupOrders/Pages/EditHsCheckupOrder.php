<?php

namespace App\Admin\Resources\HsCheckupOrders\Pages;

use App\Admin\Resources\HsCheckupOrders\HsCheckupOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsCheckupOrder extends EditRecord
{
    protected static string $resource = HsCheckupOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
