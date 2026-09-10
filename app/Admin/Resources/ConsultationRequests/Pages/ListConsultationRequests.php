<?php

namespace App\Admin\Resources\ConsultationRequests\Pages;

use App\Admin\Resources\ConsultationRequests\ConsultationRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListConsultationRequests extends ListRecords
{
    protected static string $resource = ConsultationRequestResource::class;
}
