<?php

namespace App\Admin\Resources\HsCheckupSettings\Pages;

use App\Admin\Resources\HsCheckupSettings\HsCheckupSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditHsCheckupSetting extends EditRecord
{
    protected static string $resource = HsCheckupSettingResource::class;
}
