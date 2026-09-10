<?php

use App\Http\Controllers\CmsPreviewController;
use App\Http\Controllers\ConsultationRequestController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::post('/consultations', [ConsultationRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('consultations.store');
Route::get('/cms/preview/{content}', CmsPreviewController::class)->middleware('signed')->name('cms.preview');
Route::get('/sitemap.xml', SitemapController::class)->name('cms.sitemap');
