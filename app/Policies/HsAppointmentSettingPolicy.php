<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsAppointmentSetting;

class HsAppointmentSettingPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('appointment-settings.view');
    }

    public function view(AdminUser $u, HsAppointmentSetting $m): bool
    {
        return $u->can('appointment-settings.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('appointment-settings.create');
    }

    public function update(AdminUser $u, HsAppointmentSetting $m): bool
    {
        return $u->can('appointment-settings.update');
    }

    public function delete(AdminUser $u, HsAppointmentSetting $m): bool
    {
        return $u->can('appointment-settings.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('appointment-settings.delete');
    }
}
