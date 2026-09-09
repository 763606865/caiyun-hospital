<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Branch;
use App\Models\Organization;
use Database\Seeders\AdminAuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_available(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_inactive_admin_cannot_access_the_panel(): void
    {
        $admin = AdminUser::query()->create([
            'name' => 'Inactive Admin',
            'email' => 'inactive@example.com',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_super_admin_can_access_all_management_resources(): void
    {
        $this->seed(AdminAuthorizationSeeder::class);

        $admin = AdminUser::query()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        $admin->assignRole('super-admin');
        $organization = Organization::query()->create([
            'name' => '测试诊所',
            'code' => 'test-clinic',
            'status' => 'active',
        ]);
        $organization->organizationMembers()->create([
            'admin_user_id' => $admin->id,
            'display_name' => $admin->name,
            'role' => 'owner',
            'status' => 'active',
        ]);
        Branch::query()->create(['organization_id' => $organization->id, 'name' => '本组织门店', 'code' => 'own']);
        $otherOrganization = Organization::query()->create(['name' => '其他诊所', 'code' => 'other', 'status' => 'active']);
        Branch::query()->create(['organization_id' => $otherOrganization->id, 'name' => '其他组织门店', 'code' => 'other']);

        $this->actingAs($admin, 'admin');
        $baseUrl = '/admin/'.$organization->uuid;

        $this->get($baseUrl)->assertOk();
        $this->get($baseUrl.'/branches')->assertOk();
        $this->get($baseUrl.'/organization-members')->assertOk();
        $this->get($baseUrl.'/visits')->assertOk();
        $this->get($baseUrl.'/medical-records')->assertOk();
        $this->get($baseUrl.'/prescriptions')->assertOk();
        $this->get($baseUrl.'/drugs')->assertOk();
        $this->get($baseUrl.'/inventory-stocks')->assertOk();
        $this->get($baseUrl.'/charge-orders')->assertOk();
        $this->get($baseUrl.'/daily-settlements')->assertOk();

        $this->get($baseUrl.'/branches')->assertSee('本组织门店')->assertDontSee('其他组织门店');
    }

    public function test_admin_create_command_creates_a_super_admin(): void
    {
        $this->artisan('admin:create', [
            '--name' => 'System Admin',
            '--email' => 'system@example.com',
            '--password' => 'password123',
        ])->assertSuccessful();

        $admin = AdminUser::query()->where('email', 'system@example.com')->firstOrFail();

        $this->assertTrue($admin->hasRole('super-admin'));
        $this->assertTrue($admin->organizations()->exists());
    }
}
