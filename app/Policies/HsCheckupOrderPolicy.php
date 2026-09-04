<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsCheckupOrder;

class HsCheckupOrderPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('checkup-orders.view');
    }

    public function view(AdminUser $u, HsCheckupOrder $m): bool
    {
        return $u->can('checkup-orders.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('checkup-orders.create');
    }

    public function update(AdminUser $u, HsCheckupOrder $m): bool
    {
        return $u->can('checkup-orders.update');
    }

    public function delete(AdminUser $u, HsCheckupOrder $m): bool
    {
        return $u->can('checkup-orders.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-orders.delete');
    }

    public function restore(AdminUser $u, HsCheckupOrder $m): bool
    {
        return $u->can('checkup-orders.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('checkup-orders.restore');
    }

    public function forceDelete(AdminUser $u, HsCheckupOrder $m): bool
    {
        return $u->can('checkup-orders.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-orders.delete');
    }
}
