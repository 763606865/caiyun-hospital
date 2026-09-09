<?php

namespace App\Admin\Resources\OrganizationMembers\Pages;

use App\Admin\Resources\OrganizationMembers\OrganizationMemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationMembers extends ListRecords
{
    protected static string $resource = OrganizationMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
