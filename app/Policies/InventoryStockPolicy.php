<?php

namespace App\Policies;

class InventoryStockPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'inventory-stocks';
    }
}
