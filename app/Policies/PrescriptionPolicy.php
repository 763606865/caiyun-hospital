<?php

namespace App\Policies;

class PrescriptionPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'prescriptions';
    }
}
