<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsCampus;

class HsCampusPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('campuses.view');
    }

    public function view(AdminUser $u, HsCampus $m): bool
    {
        return $u->can('campuses.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('campuses.create');
    }

    public function update(AdminUser $u, HsCampus $m): bool
    {
        return $u->can('campuses.update');
    }

    public function delete(AdminUser $u, HsCampus $m): bool
    {
        return $u->can('campuses.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('campuses.delete');
    }

    public function restore(AdminUser $u, HsCampus $m): bool
    {
        return $u->can('campuses.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('campuses.restore');
    }

    public function forceDelete(AdminUser $u, HsCampus $m): bool
    {
        return $u->can('campuses.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('campuses.delete');
    }
}
