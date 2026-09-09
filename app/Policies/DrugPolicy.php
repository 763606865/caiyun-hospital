<?php

namespace App\Policies;

class DrugPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'drugs';
    }
}
