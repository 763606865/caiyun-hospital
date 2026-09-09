<?php

namespace App\Policies;

class DailySettlementPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'daily-settlements';
    }
}
