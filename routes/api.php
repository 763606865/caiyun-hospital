<?php

use App\Api\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/sms-code', [AuthController::class, 'sendSmsCode'])->middleware('throttle:1,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/wechat/login', [AuthController::class, 'wechatLogin'])->middleware('throttle:10,1');
});

Route::middleware('auth:sanctum')->group(function (): void {
    // 认证相关
    Route::get('/me', [AuthController::class, 'me']);
    // ==============================================================================
});
