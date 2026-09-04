<?php

namespace App\Admin\Resources\HsCheckupPackages\Pages;

use App\Admin\Resources\HsCheckupPackages\HsCheckupPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsCheckupPackage extends EditRecord
{
    protected static string $resource = HsCheckupPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
