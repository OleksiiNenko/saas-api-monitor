<?php

use App\Http\Controllers\Api\MonitorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('monitors', MonitorController::class);
