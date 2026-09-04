<?php

namespace App\Admin\Resources\HsCheckupItems\Pages;

use App\Admin\Resources\HsCheckupItems\HsCheckupItemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsCheckupItem extends EditRecord
{
    protected static string $resource = HsCheckupItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
