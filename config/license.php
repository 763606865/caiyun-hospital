<?php

$serverUrl = env('LICENSE_SERVER_URL');

return [
    'enabled' => env('LICENSE_ENABLED', false),
    'global_middleware' => env('LICENSE_GLOBAL_MIDDLEWARE', true),
    // 推荐：后台下载的 .license 授权书路径；也可用 LICENSE_TOKEN 直接传入内容（兼容旧部署）。
    'file' => env('LICENSE_FILE', storage_path('license/caiyun.license')),
    'token' => env('LICENSE_TOKEN'),
    'server_url' => $serverUrl,
    'public_key' => env('LICENSE_PUBLIC_KEY'),
    'public_key_path' => env('LICENSE_PUBLIC_KEY_PATH', storage_path('app/license/public.pem')),
    'leeway' => (int) env('LICENSE_LEEWAY', 300),
    'verify_domain' => env('LICENSE_VERIFY_DOMAIN', true),
    'remote' => [
        // 配置了 LICENSE_SERVER_URL 时默认开启远程校验；仍可用 LICENSE_REMOTE_ENABLED 显式关闭。
        'enabled' => env('LICENSE_REMOTE_ENABLED', is_string($serverUrl) && $serverUrl !== ''),
        // 完整核验 URL（优先）；未配置时由 server_url + 授权书 endpoints.lease_valid 拼出。
        'endpoint' => env('LICENSE_REMOTE_ENDPOINT'),
        'client_id' => env('LICENSE_OPENAPI_CLIENT_ID'),
        'client_secret' => env('LICENSE_OPENAPI_CLIENT_SECRET'),
        'project' => env('LICENSE_PROJECT'),
        'timeout' => (int) env('LICENSE_REMOTE_TIMEOUT', 5),
        'retries' => (int) env('LICENSE_REMOTE_RETRIES', 1),
        'check_interval_minutes' => (int) env('LICENSE_REMOTE_CHECK_INTERVAL', 60),
        'offline_grace_hours' => (int) env('LICENSE_OFFLINE_GRACE_HOURS', 72),
        'allow_insecure_http' => env('LICENSE_REMOTE_ALLOW_INSECURE_HTTP', false),
        'instance_id' => env('LICENSE_INSTANCE_ID'),
        'instance_path' => env('LICENSE_INSTANCE_PATH', storage_path('app/license/instance_id')),
        'receipt_path' => env('LICENSE_RECEIPT_PATH', storage_path('app/license/remote_receipt.json')),
        'heartbeat_schedule_enabled' => env('LICENSE_HEARTBEAT_SCHEDULE_ENABLED', true),
        'heartbeat_schedule_at' => env('LICENSE_HEARTBEAT_SCHEDULE_AT', '01:00'),
    ],
    'features' => ['payment' => '支付模块', 'mobile' => '移动端模块', 'ai' => 'AI 模块'],
    'except' => ['up'],
];
