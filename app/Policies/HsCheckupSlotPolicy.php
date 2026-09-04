<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsCheckupSlot;

class HsCheckupSlotPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('checkup-slots.view');
    }

    public function view(AdminUser $u, HsCheckupSlot $m): bool
    {
        return $u->can('checkup-slots.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('checkup-slots.create');
    }

    public function update(AdminUser $u, HsCheckupSlot $m): bool
    {
        return $u->can('checkup-slots.update');
    }

    public function delete(AdminUser $u, HsCheckupSlot $m): bool
    {
        return $u->can('checkup-slots.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-slots.delete');
    }
}
