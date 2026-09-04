<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsPatient;

class HsPatientPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('patients.view');
    }

    public function view(AdminUser $u, HsPatient $m): bool
    {
        return $u->can('patients.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('patients.create');
    }

    public function update(AdminUser $u, HsPatient $m): bool
    {
        return $u->can('patients.update');
    }

    public function delete(AdminUser $u, HsPatient $m): bool
    {
        return $u->can('patients.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('patients.delete');
    }

    public function restore(AdminUser $u, HsPatient $m): bool
    {
        return $u->can('patients.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('patients.restore');
    }

    public function forceDelete(AdminUser $u, HsPatient $m): bool
    {
        return $u->can('patients.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('patients.delete');
    }
}
