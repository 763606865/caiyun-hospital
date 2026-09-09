<?php

namespace App\Policies;

class ChargeOrderPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'charge-orders';
    }
}
