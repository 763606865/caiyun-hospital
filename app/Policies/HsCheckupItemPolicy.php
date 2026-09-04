<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsCheckupItem;

class HsCheckupItemPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('checkup-items.view');
    }

    public function view(AdminUser $u, HsCheckupItem $m): bool
    {
        return $u->can('checkup-items.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('checkup-items.create');
    }

    public function update(AdminUser $u, HsCheckupItem $m): bool
    {
        return $u->can('checkup-items.update');
    }

    public function delete(AdminUser $u, HsCheckupItem $m): bool
    {
        return $u->can('checkup-items.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-items.delete');
    }

    public function restore(AdminUser $u, HsCheckupItem $m): bool
    {
        return $u->can('checkup-items.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('checkup-items.restore');
    }

    public function forceDelete(AdminUser $u, HsCheckupItem $m): bool
    {
        return $u->can('checkup-items.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-items.delete');
    }
}
