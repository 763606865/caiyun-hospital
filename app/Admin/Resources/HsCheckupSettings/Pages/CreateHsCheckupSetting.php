<?php

namespace App\Admin\Resources\HsCheckupSettings\Pages;

use App\Admin\Resources\HsCheckupSettings\HsCheckupSettingResource;
use App\Models\HsCheckupSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateHsCheckupSetting extends CreateRecord
{
    protected static string $resource = HsCheckupSettingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['key'] = HsCheckupSetting::DEFAULT_KEY;

        return $data;
    }
}
