<?php

namespace App\Admin\Resources\HsCheckupPackages\Pages;

use App\Admin\Resources\HsCheckupPackages\HsCheckupPackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsCheckupPackages extends ListRecords
{
    protected static string $resource = HsCheckupPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
