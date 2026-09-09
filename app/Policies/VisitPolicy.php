<?php

namespace App\Policies;

class VisitPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'visits';
    }
}
