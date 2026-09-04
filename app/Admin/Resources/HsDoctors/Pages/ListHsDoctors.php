<?php

namespace App\Admin\Resources\HsDoctors\Pages;

use App\Admin\Imports\Concerns\HasCsvImportActions;
use App\Admin\Imports\HsDoctorCsvImporter;
use App\Admin\Resources\HsDoctors\HsDoctorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsDoctors extends ListRecords
{
    /** @use HasCsvImportActions<HsDoctorCsvImporter> */
    use HasCsvImportActions;

    protected static string $resource = HsDoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getCsvImportHeaderActions(),
            CreateAction::make(),
        ];
    }

    protected function csvImporterClass(): string
    {
        return HsDoctorCsvImporter::class;
    }

    protected function csvImportPermission(): string
    {
        return 'doctors.create';
    }
}
