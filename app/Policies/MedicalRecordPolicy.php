<?php

namespace App\Policies;

class MedicalRecordPolicy extends TenantResourcePolicy
{
    protected function permissionPrefix(): string
    {
        return 'medical-records';
    }
}
