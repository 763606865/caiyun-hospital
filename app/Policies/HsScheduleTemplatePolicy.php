<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\HsScheduleTemplate;

class HsScheduleTemplatePolicy
{
    public function viewAny(AdminUser $u): bool
    {
        return $u->can('schedule-templates.view');
    }

    public function view(AdminUser $u, HsScheduleTemplate $m): bool
    {
        return $u->can('schedule-templates.view');
    }

    public function create(AdminUser $u): bool
    {
        return $u->can('schedule-templates.create');
    }

    public function update(AdminUser $u, HsScheduleTemplate $m): bool
    {
        return $u->can('schedule-templates.update');
    }

    public function delete(AdminUser $u, HsScheduleTemplate $m): bool
    {
        return $u->can('schedule-templates.delete');
    }

    public function deleteAny(AdminUser $u): bool
    {
        return $u->can('schedule-templates.delete');
    }

    public function restore(AdminUser $u, HsScheduleTemplate $m): bool
    {
        return $u->can('schedule-templates.restore');
    }

    public function restoreAny(AdminUser $u): bool
    {
        return $u->can('schedule-templates.restore');
    }

    public function forceDelete(AdminUser $u, HsScheduleTemplate $m): bool
    {
        return $u->can('schedule-templates.delete');
    }

    public function forceDeleteAny(AdminUser $u): bool
    {
        return $u->can('schedule-templates.delete');
    }
}
