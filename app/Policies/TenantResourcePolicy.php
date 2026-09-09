<?php

namespace App\Policies;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;

abstract class TenantResourcePolicy
{
    abstract protected function permissionPrefix(): string;

    public function viewAny(AdminUser $user): bool
    {
        return $user->can($this->permission().'.view');
    }

    public function view(AdminUser $user, Model $model): bool
    {
        return $user->can($this->permission().'.view');
    }

    public function create(AdminUser $user): bool
    {
        return $user->can($this->permission().'.create');
    }

    public function update(AdminUser $user, Model $model): bool
    {
        return $user->can($this->permission().'.update');
    }

    public function delete(AdminUser $user, Model $model): bool
    {
        return $user->can($this->permission().'.delete');
    }

    public function deleteAny(AdminUser $user): bool
    {
        return $user->can($this->permission().'.delete');
    }

    private function permission(): string
    {
        return $this->permissionPrefix();
    }
}
