<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsSchedule;

class HsSchedulePolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('schedules.view');
    }

    public function view(AdminUser $u, HsSchedule $m): bool
    {
        return $u->can('schedules.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('schedules.create');
    }

    public function update(AdminUser $u, HsSchedule $m): bool
    {
        return $u->can('schedules.update');
    }

    public function delete(AdminUser $u, HsSchedule $m): bool
    {
        return $u->can('schedules.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('schedules.delete');
    }

    public function restore(AdminUser $u, HsSchedule $m): bool
    {
        return $u->can('schedules.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('schedules.restore');
    }

    public function forceDelete(AdminUser $u, HsSchedule $m): bool
    {
        return $u->can('schedules.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('schedules.delete');
    }
}
