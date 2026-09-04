<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsCheckupSetting;

class HsCheckupSettingPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('checkup-settings.view');
    }

    public function view(AdminUser $u, HsCheckupSetting $m): bool
    {
        return $u->can('checkup-settings.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('checkup-settings.create');
    }

    public function update(AdminUser $u, HsCheckupSetting $m): bool
    {
        return $u->can('checkup-settings.update');
    }

    public function delete(AdminUser $u, HsCheckupSetting $m): bool
    {
        return $u->can('checkup-settings.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('checkup-settings.delete');
    }
}
