<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Organization;

class OrganizationPolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return $admin->can('organizations.view');
    }

    public function view(AdminUser $admin, Organization $organization): bool
    {
        return $admin->can('organizations.view');
    }

    public function create(AdminUser $admin): bool
    {
        return $admin->can('organizations.create');
    }

    public function update(AdminUser $admin, Organization $organization): bool
    {
        return $admin->can('organizations.update');
    }

    public function delete(AdminUser $admin, Organization $organization): bool
    {
        return $admin->can('organizations.delete');
    }
}
