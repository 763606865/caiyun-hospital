<?php

namespace App\Support\Tenancy;

use App\Models\Organization;

class TenantContext
{
    public function __construct(private ?Organization $organization = null) {}

    public function set(Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function get(): ?Organization
    {
        return $this->organization;
    }

    public function clear(): void
    {
        $this->organization = null;
    }
}
