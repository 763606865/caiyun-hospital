<?php

namespace App\Admin\Resources\HsDepartmentCategories\Pages;

use App\Admin\Resources\HsDepartmentCategories\HsDepartmentCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsDepartmentCategory extends EditRecord
{
    protected static string $resource = HsDepartmentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
