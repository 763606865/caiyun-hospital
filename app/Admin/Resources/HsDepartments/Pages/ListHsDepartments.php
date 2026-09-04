<?php

namespace App\Admin\Resources\HsDepartments\Pages;

use App\Admin\Imports\Concerns\HasCsvImportActions;
use App\Admin\Imports\HsDepartmentCsvImporter;
use App\Admin\Resources\HsDepartments\HsDepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsDepartments extends ListRecords
{
    /** @use HasCsvImportActions<HsDepartmentCsvImporter> */
    use HasCsvImportActions;

    protected static string $resource = HsDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getCsvImportHeaderActions(),
            CreateAction::make(),
        ];
    }

    protected function csvImporterClass(): string
    {
        return HsDepartmentCsvImporter::class;
    }

    protected function csvImportPermission(): string
    {
        return 'departments.create';
    }
}
