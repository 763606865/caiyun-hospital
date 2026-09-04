<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsAppointment;

class HsAppointmentPolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('appointments.view');
    }

    public function view(AdminUser $u, HsAppointment $m): bool
    {
        return $u->can('appointments.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('appointments.create');
    }

    public function update(AdminUser $u, HsAppointment $m): bool
    {
        return $u->can('appointments.update');
    }

    public function delete(AdminUser $u, HsAppointment $m): bool
    {
        return $u->can('appointments.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('appointments.delete');
    }

    public function restore(AdminUser $u, HsAppointment $m): bool
    {
        return $u->can('appointments.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('appointments.restore');
    }

    public function forceDelete(AdminUser $u, HsAppointment $m): bool
    {
        return $u->can('appointments.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('appointments.delete');
    }
}
