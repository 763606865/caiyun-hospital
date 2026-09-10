<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
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

    public function test_super_admin_can_access_platform_management_resources(): void
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

        $this->actingAs($admin, 'admin');

        $this->get('/admin')->assertOk();
        $this->get('/admin/organizations')->assertOk()->assertSee($organization->name);
        $this->get('/admin/consultation-requests')->assertOk();
        $this->get('/admin/admin-users')->assertOk();
        $this->get('/admin/contents')->assertOk();
        $this->get('/admin/visits')->assertNotFound();
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
        $this->assertFalse($admin->organizations()->exists());
    }
}
