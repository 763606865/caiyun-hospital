<?php

namespace App\Admin\Resources\HsDepartments\Pages;

use App\Admin\Resources\HsDepartments\HsDepartmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsDepartment extends CreateRecord
{
    protected static string $resource = HsDepartmentResource::class;
}
