<?php

namespace App\Admin\Resources\HsCampuses\Pages;

use App\Admin\Resources\HsCampuses\HsCampusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsCampuses extends ListRecords
{
    protected static string $resource = HsCampusResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
