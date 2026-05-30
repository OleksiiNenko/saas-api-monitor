<?php

use App\Http\Controllers\Api\GeneratedPageController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\WordpressSiteController;
use Illuminate\Support\Facades\Route;

Route::apiResource('monitors', MonitorController::class);

// WordPress sites — connect and inspect target sites.
Route::apiResource('wordpress-sites', WordpressSiteController::class)
    ->only(['index', 'store', 'destroy']);
Route::get('wordpress-sites/{site}/meta', [WordpressSiteController::class, 'meta']);
Route::post('wordpress-sites/{site}/test', [WordpressSiteController::class, 'test']);

// AI-generated pages.
Route::get('generated-pages', [GeneratedPageController::class, 'index']);
Route::get('generated-pages/{generatedPage}', [GeneratedPageController::class, 'show']);
Route::patch('generated-pages/{generatedPage}', [GeneratedPageController::class, 'update']);
Route::post('generated-pages/{generatedPage}/push', [GeneratedPageController::class, 'push'])
    ->middleware('throttle:20,1');
Route::post('generated-pages/generate', [GeneratedPageController::class, 'generate'])
    ->middleware('throttle:20,1');
