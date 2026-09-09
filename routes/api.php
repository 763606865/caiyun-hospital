<?php

use App\Api\Controllers\AuthController;
use App\Api\Controllers\CmsController;
use App\Api\Controllers\FileController;
use App\Api\Controllers\PatientController;
use App\Api\Controllers\SystemSettingController;
use App\Api\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/sms-code', [AuthController::class, 'sendSmsCode'])->middleware('throttle:1,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/wechat/login', [AuthController::class, 'wechatLogin'])->middleware('throttle:10,1');
});

Route::get('/system/settings', SystemSettingController::class)->middleware('throttle:60,1');

Route::prefix('cms')->middleware('throttle:120,1')->group(function (): void {
    Route::get('/categories', [CmsController::class, 'categories']);
    Route::get('/contents', [CmsController::class, 'index']);
    Route::get('/contents/{slug}', [CmsController::class, 'show']);
});

Route::middleware('auth:api')->group(function (): void {
    // 文件上传
    Route::post('/files/upload', [FileController::class, 'upload'])->middleware('throttle:30,1');
    // ==============================================================================
    // 认证相关
    Route::get('/me', [AuthController::class, 'me']);
    // ==============================================================================
    // 用户资料
    Route::patch('/user', [UserController::class, 'update']);
    Route::post('/user/phone/sms-code', [UserController::class, 'sendPhoneCode'])->middleware('throttle:1,1');
    Route::patch('/user/phone', [UserController::class, 'updatePhone'])->middleware('throttle:10,1');
    // ==============================================================================
    // 就诊人
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store'])->middleware('throttle:30,1');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->whereNumber('id');
    Route::patch('/patients/{id}', [PatientController::class, 'update'])->whereNumber('id');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->whereNumber('id');
});
