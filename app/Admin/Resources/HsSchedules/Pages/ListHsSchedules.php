<?php

namespace App\Admin\Resources\HsSchedules\Pages;

use App\Admin\Resources\HsSchedules\HsScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHsSchedules extends ListRecords
{
    protected static string $resource = HsScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
