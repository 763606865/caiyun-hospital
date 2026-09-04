<?php

namespace App\Admin\Resources\HsSchedules\Pages;

use App\Admin\Resources\HsSchedules\HsScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditHsSchedule extends EditRecord
{
    protected static string $resource = HsScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
