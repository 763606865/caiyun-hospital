<?php

namespace App\Admin\Resources\HsDepartments\Pages;

use App\Admin\Resources\HsDepartments\HsDepartmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsDepartment extends EditRecord
{
    protected static string $resource = HsDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
