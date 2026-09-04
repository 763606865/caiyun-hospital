<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsDepartmentCategory;

class HsDepartmentCategoryPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('department-categories.view');
    }

    public function view(AdminUser $u, HsDepartmentCategory $m): bool
    {
        return $u->can('department-categories.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('department-categories.create');
    }

    public function update(AdminUser $u, HsDepartmentCategory $m): bool
    {
        return $u->can('department-categories.update');
    }

    public function delete(AdminUser $u, HsDepartmentCategory $m): bool
    {
        return $u->can('department-categories.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('department-categories.delete');
    }

    public function restore(AdminUser $u, HsDepartmentCategory $m): bool
    {
        return $u->can('department-categories.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('department-categories.restore');
    }

    public function forceDelete(AdminUser $u, HsDepartmentCategory $m): bool
    {
        return $u->can('department-categories.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('department-categories.delete');
    }
}
