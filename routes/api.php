<?php

use App\Api\Controllers\AuthController;
use App\Api\Controllers\DeviceController;
use App\Api\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/sms-code', [AuthController::class, 'sendSmsCode'])->middleware('throttle:1,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/wechat/login', [AuthController::class, 'wechatLogin'])->middleware('throttle:10,1');
});

Route::post('/devices/sync', [DeviceController::class, 'sync'])->middleware('throttle:10,1');

Route::middleware('auth:api')->group(function (): void {
    // 认证相关
    Route::get('/me', [AuthController::class, 'me']);
    // ==============================================================================
    // 用户资料
    Route::patch('/user', [UserController::class, 'update']);
    Route::post('/user/phone/sms-code', [UserController::class, 'sendPhoneCode'])
        ->middleware('throttle:1,1');
    Route::patch('/user/phone', [UserController::class, 'updatePhone'])
        ->middleware('throttle:10,1');
    // ==============================================================================
});
