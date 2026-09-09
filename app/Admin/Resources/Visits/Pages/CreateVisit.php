<?php

namespace App\Admin\Resources\Visits\Pages;

use App\Admin\Resources\Visits\VisitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisit extends CreateRecord
{
    protected static string $resource = VisitResource::class;
}
