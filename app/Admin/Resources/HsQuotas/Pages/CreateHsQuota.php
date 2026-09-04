<?php

namespace App\Admin\Resources\HsQuotas\Pages;

use App\Admin\Resources\HsQuotas\HsQuotaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsQuota extends CreateRecord
{
    protected static string $resource = HsQuotaResource::class;
}
