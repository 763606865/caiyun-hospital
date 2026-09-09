<?php

namespace App\Admin\Resources\Branches\Pages;

use App\Admin\Resources\Branches\BranchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;
}
