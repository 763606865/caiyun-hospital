<?php

namespace App\Policies;

class PaymentTransactionPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'payment-transactions';
    }
}
