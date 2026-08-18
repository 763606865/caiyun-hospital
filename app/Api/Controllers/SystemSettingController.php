<?php

namespace App\Api\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    /**
     * 获取客户端可用的系统设置。
     *
     * GET /api/system/settings
     */
    public function __invoke(): JsonResponse
    {
        $setting = SystemSetting::current();

        return $this->success([
            'site_name' => $setting->site_name,
            'logo' => $this->publicUrl($setting->logo),
            'favicon' => $this->publicUrl($setting->favicon),
            'customer_service_phone' => $setting->customer_service_phone,
            'icp_number' => $setting->icp_number,
            'sms_enabled' => $setting->sms_enabled,
            'registration_enabled' => $setting->registration_enabled,
            'default_avatar' => $this->publicUrl($setting->default_avatar),
            'upload_max_size_mb' => $setting->upload_max_size_mb,
            'payment_enabled' => $setting->payment_enabled,
            'maintenance_message' => $setting->maintenance_message,
        ]);
    }

    private function publicUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
