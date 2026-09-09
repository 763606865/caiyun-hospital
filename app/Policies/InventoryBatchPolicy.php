<?php

namespace App\Policies;

class InventoryBatchPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'inventory-batches';
    }
}
