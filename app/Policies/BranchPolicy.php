<?php

namespace App\Policies;

class BranchPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'branches';
    }
}
