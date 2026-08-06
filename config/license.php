<?php

return [
    /* 本地和测试环境默认关闭；生产部署必须显式开启。 */
    'enabled' => env('LICENSE_ENABLED', false),

    /* 项目已在 bootstrap/app.php 显式引用包中间件，关闭包的自动全局注入以避免重复执行。 */
    'global_middleware' => env('LICENSE_GLOBAL_MIDDLEWARE', false),

    /* 由授权系统使用私钥签发的 <payload>.<signature> 许可证。 */
    'token' => env('LICENSE_TOKEN'),

    /* 建议在生产环境使用只读公钥文件，也可传入 Base64 编码的 PEM。 */
    'public_key' => env('LICENSE_PUBLIC_KEY'),
    'public_key_path' => env('LICENSE_PUBLIC_KEY_PATH', storage_path('app/license/public.pem')),

    /* 时钟偏差容忍时间，单位秒。 */
    'leeway' => (int) env('LICENSE_LEEWAY', 300),

    /* 是否校验当前 HTTP Host 在许可证 domains 中。 */
    'verify_domain' => env('LICENSE_VERIFY_DOMAIN', true),

    'remote' => [
        'enabled' => env('LICENSE_REMOTE_ENABLED', false),
        'endpoint' => env('LICENSE_REMOTE_ENDPOINT'),
        'client_id' => env('LICENSE_OPENAPI_CLIENT_ID'),
        'client_secret' => env('LICENSE_OPENAPI_CLIENT_SECRET'),
        'project' => env('LICENSE_PROJECT', env('APP_NAME', 'cms')),
        'timeout' => (int) env('LICENSE_REMOTE_TIMEOUT', 5),
        'retries' => (int) env('LICENSE_REMOTE_RETRIES', 1),
        'check_interval_minutes' => (int) env('LICENSE_REMOTE_CHECK_INTERVAL', 60),
        'offline_grace_hours' => (int) env('LICENSE_OFFLINE_GRACE_HOURS', 72),
        'allow_insecure_http' => env('LICENSE_REMOTE_ALLOW_INSECURE_HTTP', false),
        'instance_id' => env('LICENSE_INSTANCE_ID'),
        'instance_path' => env('LICENSE_INSTANCE_PATH', storage_path('app/license/instance_id')),
        'receipt_path' => env('LICENSE_RECEIPT_PATH', storage_path('app/license/remote_receipt.json')),
    ],

    /* 功能标识集中定义，业务路由中使用 feature:payment 等中间件。 */
    'features' => [
        'payment' => '支付模块',
        'mobile' => '移动端模块',
        'ai' => 'AI 模块',
    ],

    /* 授权检查例外，健康检查应始终可用。 */
    'except' => ['up'],
];
