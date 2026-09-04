<?php

namespace App\Admin\Resources\HsDepartmentCategories\Pages;

use App\Admin\Resources\HsDepartmentCategories\HsDepartmentCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsDepartmentCategories extends ListRecords
{
    protected static string $resource = HsDepartmentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
