<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\ConsultationRequest;

class ConsultationRequestPolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return $admin->can('consultation-requests.view');
    }

    public function view(AdminUser $admin, ConsultationRequest $consultationRequest): bool
    {
        return $admin->can('consultation-requests.view');
    }

    public function update(AdminUser $admin, ConsultationRequest $consultationRequest): bool
    {
        return $admin->can('consultation-requests.update');
    }

    public function create(AdminUser $admin): bool
    {
        return false;
    }

    public function delete(AdminUser $admin, ConsultationRequest $consultationRequest): bool
    {
        return false;
    }
}
