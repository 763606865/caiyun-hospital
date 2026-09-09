<?php

namespace App\Policies;

class InventoryMovementPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'inventory-movements';
    }
}
