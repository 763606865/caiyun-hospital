<?php

namespace App\Admin\Resources\HsCheckupItems\Pages;

use App\Admin\Resources\HsCheckupItems\HsCheckupItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsCheckupItem extends CreateRecord
{
    protected static string $resource = HsCheckupItemResource::class;
}
