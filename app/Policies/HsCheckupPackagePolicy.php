<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsCheckupPackage;

class HsCheckupPackagePolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('checkup-packages.view');
    }

    public function view(AdminUser $u, HsCheckupPackage $m): bool
    {
        return $u->can('checkup-packages.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('checkup-packages.create');
    }

    public function update(AdminUser $u, HsCheckupPackage $m): bool
    {
        return $u->can('checkup-packages.update');
    }

    public function delete(AdminUser $u, HsCheckupPackage $m): bool
    {
        return $u->can('checkup-packages.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-packages.delete');
    }

    public function restore(AdminUser $u, HsCheckupPackage $m): bool
    {
        return $u->can('checkup-packages.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('checkup-packages.restore');
    }

    public function forceDelete(AdminUser $u, HsCheckupPackage $m): bool
    {
        return $u->can('checkup-packages.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-packages.delete');
    }
}
