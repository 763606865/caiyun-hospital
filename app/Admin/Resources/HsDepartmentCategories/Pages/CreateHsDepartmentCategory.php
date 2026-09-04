<?php

namespace App\Admin\Resources\HsDepartmentCategories\Pages;

use App\Admin\Resources\HsDepartmentCategories\HsDepartmentCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsDepartmentCategory extends CreateRecord
{
    protected static string $resource = HsDepartmentCategoryResource::class;
}
