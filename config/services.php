<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'wechat' => [
        'app_id' => env('WECHAT_MINI_PROGRAM_APP_ID'),
        'app_secret' => env('WECHAT_MINI_PROGRAM_APP_SECRET'),
        'base_url' => env('WECHAT_API_BASE_URL', 'https://api.weixin.qq.com'),
    ],

    'amap' => [
        'web_key' => env('AMAP_WEB_KEY'),
        'security_js_code' => env('AMAP_SECURITY_JS_CODE'),
        'default_longitude' => (float) env('AMAP_DEFAULT_LONGITUDE', 116.397428),
        'default_latitude' => (float) env('AMAP_DEFAULT_LATITUDE', 39.90923),
        'default_zoom' => (int) env('AMAP_DEFAULT_ZOOM', 15),
    ],

];
