<?php

namespace App\Admin\Resources\ConsultationRequests\Pages;

use App\Admin\Resources\ConsultationRequests\ConsultationRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditConsultationRequest extends EditRecord
{
    protected static string $resource = ConsultationRequestResource::class;
}
