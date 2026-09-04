<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AdminAuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'admin-users.view',
            'admin-users.create',
            'admin-users.update',
            'admin-users.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'client-versions.view',
            'client-versions.create',
            'client-versions.update',
            'client-versions.delete',
            'system-settings.view',
            'system-settings.create',
            'system-settings.update',
            'contents.view',
            'contents.create',
            'contents.update',
            'contents.delete',
            'contents.restore',
            'contents.publish',
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            'tags.view',
            'tags.create',
            'tags.update',
            'tags.delete',
            'media.view',
            'media.create',
            'media.update',
            'media.delete',
            'operation-logs.view',
            'campuses.view',
            'campuses.create',
            'campuses.update',
            'campuses.delete',
            'campuses.restore',
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
            'departments.restore',
            'doctors.view',
            'doctors.create',
            'doctors.update',
            'doctors.delete',
            'doctors.restore',
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'appointments.delete',
            'appointments.restore',
            'schedules.view',
            'schedules.create',
            'schedules.update',
            'schedules.delete',
            'schedules.restore',
            'quotas.view',
            'quotas.create',
            'quotas.update',
            'quotas.delete',
            'patients.view',
            'patients.create',
            'patients.update',
            'patients.delete',
            'patients.restore',
            'appointment-settings.view',
            'appointment-settings.create',
            'appointment-settings.update',
            'appointment-settings.delete',
            'checkup-items.view',
            'checkup-items.create',
            'checkup-items.update',
            'checkup-items.delete',
            'checkup-items.restore',
            'checkup-packages.view',
            'checkup-packages.create',
            'checkup-packages.update',
            'checkup-packages.delete',
            'checkup-packages.restore',
            'checkup-slots.view',
            'checkup-slots.create',
            'checkup-slots.update',
            'checkup-slots.delete',
            'checkup-orders.view',
            'checkup-orders.create',
            'checkup-orders.update',
            'checkup-orders.delete',
            'checkup-orders.restore',
            'checkup-settings.view',
            'checkup-settings.create',
            'checkup-settings.update',
            'checkup-settings.delete',
        ];

        foreach ($permissions as $permission) {
            AdminPermission::findOrCreate($permission, 'admin');
        }

        AdminRole::findOrCreate('super-admin', 'admin')
            ->syncPermissions(AdminPermission::query()->where('guard_name', 'admin')->get());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
