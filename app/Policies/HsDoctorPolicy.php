<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsDoctor;

class HsDoctorPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('doctors.view');
    }

    public function view(AdminUser $u, HsDoctor $m): bool
    {
        return $u->can('doctors.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('doctors.create');
    }

    public function update(AdminUser $u, HsDoctor $m): bool
    {
        return $u->can('doctors.update');
    }

    public function delete(AdminUser $u, HsDoctor $m): bool
    {
        return $u->can('doctors.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('doctors.delete');
    }

    public function restore(AdminUser $u, HsDoctor $m): bool
    {
        return $u->can('doctors.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('doctors.restore');
    }

    public function forceDelete(AdminUser $u, HsDoctor $m): bool
    {
        return $u->can('doctors.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('doctors.delete');
    }
}
