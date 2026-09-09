<?php

namespace App\Admin\Resources\Drugs\Pages;

use App\Admin\Resources\Drugs\DrugResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDrugs extends ListRecords
{
    protected static string $resource = DrugResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
