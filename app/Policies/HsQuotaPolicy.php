<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsQuota;

class HsQuotaPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('quotas.view');
    }

    public function view(AdminUser $u, HsQuota $m): bool
    {
        return $u->can('quotas.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('quotas.create');
    }

    public function update(AdminUser $u, HsQuota $m): bool
    {
        return $u->can('quotas.update');
    }

    public function delete(AdminUser $u, HsQuota $m): bool
    {
        return $u->can('quotas.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('quotas.delete');
    }
}
