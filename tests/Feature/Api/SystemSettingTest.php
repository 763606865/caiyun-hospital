<?php

namespace Tests\Feature\Api;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_system_settings_without_authentication(): void
    {
        $storageUrl = rtrim((string) config('filesystems.disks.public.url'), '/');

        SystemSetting::query()->create([
            'site_name' => '彩云系统',
            'logo' => 'system-settings/logo.png',
            'favicon' => 'system-settings/favicon.ico',
            'customer_service_phone' => '400-123-4567',
            'icp_number' => '京ICP备12345678号',
            'sms_enabled' => false,
            'registration_enabled' => true,
            'default_avatar' => 'system-settings/avatar.png',
            'upload_max_size_mb' => 20,
            'payment_enabled' => true,
            'maintenance_message' => '系统维护中',
        ]);

        $this->getJson('/api/system/settings')
            ->assertOk()
            ->assertJsonPath('data.site_name', '彩云系统')
            ->assertJsonPath('data.logo', $storageUrl.'/system-settings/logo.png')
            ->assertJsonPath('data.favicon', $storageUrl.'/system-settings/favicon.ico')
            ->assertJsonPath('data.customer_service_phone', '400-123-4567')
            ->assertJsonPath('data.icp_number', '京ICP备12345678号')
            ->assertJsonPath('data.sms_enabled', false)
            ->assertJsonPath('data.registration_enabled', true)
            ->assertJsonPath('data.default_avatar', $storageUrl.'/system-settings/avatar.png')
            ->assertJsonPath('data.upload_max_size_mb', 20)
            ->assertJsonPath('data.payment_enabled', true)
            ->assertJsonPath('data.maintenance_message', '系统维护中')
            ->assertJsonMissingPath('data.id')
            ->assertJsonMissingPath('data.key')
            ->assertJsonMissingPath('data.created_at')
            ->assertJsonMissingPath('data.updated_at');
    }

    public function test_it_returns_defaults_when_settings_have_not_been_created(): void
    {
        $this->getJson('/api/system/settings')
            ->assertOk()
            ->assertJsonPath('data.site_name', null)
            ->assertJsonPath('data.logo', null)
            ->assertJsonPath('data.sms_enabled', true)
            ->assertJsonPath('data.registration_enabled', true)
            ->assertJsonPath('data.upload_max_size_mb', 10)
            ->assertJsonPath('data.payment_enabled', false);
    }
}
