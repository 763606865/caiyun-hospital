<?php

namespace App\Policies;

class OrganizationMemberPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'organization-members';
    }
}
