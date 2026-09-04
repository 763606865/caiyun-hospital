<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsDepartment;

class HsDepartmentPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('departments.view');
    }

    public function view(AdminUser $u, HsDepartment $m): bool
    {
        return $u->can('departments.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('departments.create');
    }

    public function update(AdminUser $u, HsDepartment $m): bool
    {
        return $u->can('departments.update');
    }

    public function delete(AdminUser $u, HsDepartment $m): bool
    {
        return $u->can('departments.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('departments.delete');
    }

    public function restore(AdminUser $u, HsDepartment $m): bool
    {
        return $u->can('departments.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('departments.restore');
    }

    public function forceDelete(AdminUser $u, HsDepartment $m): bool
    {
        return $u->can('departments.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('departments.delete');
    }
}
