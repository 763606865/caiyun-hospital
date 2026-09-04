<?php

use App\Api\Controllers\AppointmentController;
use App\Api\Controllers\AuthController;
use App\Api\Controllers\CheckupController;
use App\Api\Controllers\CheckupOrderController;
use App\Api\Controllers\ClientVersionController;
use App\Api\Controllers\CmsController;
use App\Api\Controllers\DeviceController;
use App\Api\Controllers\FileController;
use App\Api\Controllers\HospitalController;
use App\Api\Controllers\PatientController;
use App\Api\Controllers\PatientOverviewController;
use App\Api\Controllers\SystemSettingController;
use App\Api\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/sms-code', [AuthController::class, 'sendSmsCode'])->middleware('throttle:1,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/wechat/login', [AuthController::class, 'wechatLogin'])->middleware('throttle:10,1');
});

Route::post('/devices/sync', [DeviceController::class, 'sync'])->middleware('throttle:10,1');
Route::get('/client/version/check', [ClientVersionController::class, 'check'])->middleware('throttle:60,1');
Route::get('/system/settings', SystemSettingController::class)->middleware('throttle:60,1');

Route::prefix('cms')->middleware('throttle:120,1')->group(function (): void {
    Route::get('/categories', [CmsController::class, 'categories']);
    Route::get('/contents', [CmsController::class, 'index']);
    Route::get('/contents/{slug}', [CmsController::class, 'show']);
});

Route::prefix('hospital')->middleware('throttle:120,1')->group(function (): void {
    Route::get('/home', [HospitalController::class, 'home']);
    Route::get('/campuses', [HospitalController::class, 'campuses']);
    Route::get('/campuses/{slug}', [HospitalController::class, 'campus']);
    Route::get('/department-categories', [HospitalController::class, 'departmentCategories']);
    Route::get('/departments', [HospitalController::class, 'departments']);
    Route::get('/departments/{slug}', [HospitalController::class, 'department']);
    Route::get('/doctors', [HospitalController::class, 'doctors']);
    Route::get('/doctors/{slug}', [HospitalController::class, 'doctor']);
    Route::get('/schedules', [HospitalController::class, 'schedules']);
    Route::get('/schedules/{id}', [HospitalController::class, 'schedule'])->whereNumber('id');
    Route::get('/appointment-settings', [HospitalController::class, 'appointmentSettings']);
    Route::get('/checkup-packages', [CheckupController::class, 'packages']);
    Route::get('/checkup-packages/{slug}', [CheckupController::class, 'package']);
    Route::get('/checkup-slots', [CheckupController::class, 'slots']);
    Route::get('/checkup-settings', [CheckupController::class, 'settings']);
});

Route::middleware('auth:api')->group(function (): void {
    // 文件上传
    Route::post('/files/upload', [FileController::class, 'upload'])->middleware('throttle:30,1');
    // ==============================================================================
    // 认证相关
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/me/overview', PatientOverviewController::class);
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
    // ==============================================================================
    // 我的挂号
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store'])->middleware('throttle:20,1');
    Route::get('/appointments/{appointmentNo}', [AppointmentController::class, 'show']);
    Route::post('/appointments/{appointmentNo}/cancel', [AppointmentController::class, 'cancel'])->middleware('throttle:20,1');
    // ==============================================================================
    // 我的体检预约
    Route::get('/checkup-orders', [CheckupOrderController::class, 'index']);
    Route::post('/checkup-orders', [CheckupOrderController::class, 'store'])->middleware('throttle:20,1');
    Route::get('/checkup-orders/{orderNo}', [CheckupOrderController::class, 'show']);
    Route::post('/checkup-orders/{orderNo}/cancel', [CheckupOrderController::class, 'cancel'])->middleware('throttle:20,1');
    // ==============================================================================
});
