<?php

namespace App\Admin\Resources\HsCampuses\Pages;

use App\Admin\Resources\HsCampuses\HsCampusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsCampus extends CreateRecord
{
    protected static string $resource = HsCampusResource::class;
}
