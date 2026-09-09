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
            'department-categories.view',
            'department-categories.create',
            'department-categories.update',
            'department-categories.delete',
            'department-categories.restore',
            'doctors.view',
            'doctors.create',
            'doctors.update',
            'doctors.delete',
            'doctors.restore',
            'patients.view',
            'patients.create',
            'patients.update',
            'patients.delete',
            'patients.restore',
            'branches.view',
            'branches.create',
            'branches.update',
            'branches.delete',
            'organization-members.view',
            'organization-members.create',
            'organization-members.update',
            'organization-members.delete',
            'visits.view',
            'visits.create',
            'visits.update',
            'visits.delete',
            'medical-records.view',
            'medical-records.create',
            'medical-records.update',
            'medical-records.delete',
            'prescriptions.view',
            'prescriptions.create',
            'prescriptions.update',
            'prescriptions.delete',
            'drugs.view',
            'drugs.create',
            'drugs.update',
            'drugs.delete',
            'inventory-batches.view',
            'inventory-batches.create',
            'inventory-batches.update',
            'inventory-batches.delete',
            'inventory-stocks.view',
            'inventory-stocks.create',
            'inventory-stocks.update',
            'inventory-stocks.delete',
            'inventory-movements.view',
            'inventory-movements.create',
            'inventory-movements.update',
            'inventory-movements.delete',
            'charge-orders.view',
            'charge-orders.create',
            'charge-orders.update',
            'charge-orders.delete',
            'payment-transactions.view',
            'payment-transactions.create',
            'payment-transactions.update',
            'payment-transactions.delete',
            'daily-settlements.view',
            'daily-settlements.create',
            'daily-settlements.update',
            'daily-settlements.delete',
        ];

        foreach ($permissions as $permission) {
            AdminPermission::findOrCreate($permission, 'admin');
        }

        AdminRole::findOrCreate('super-admin', 'admin')
            ->syncPermissions(AdminPermission::query()->where('guard_name', 'admin')->get());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
