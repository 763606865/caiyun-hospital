<?php

namespace App\Admin\Resources\HsCheckupPackages\Pages;

use App\Admin\Resources\HsCheckupPackages\HsCheckupPackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHsCheckupPackage extends CreateRecord
{
    protected static string $resource = HsCheckupPackageResource::class;
}
