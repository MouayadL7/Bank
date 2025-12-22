<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| API endpoints for management dashboard and system monitoring
|
*/

Route::middleware(['auth:api', 'can:isAdmin'])->prefix('dashboard')->group(function () {
    Route::get('statistics', [DashboardController::class, 'statistics']);
    Route::get('health', [DashboardController::class, 'health']);
    Route::get('performance', [DashboardController::class, 'performance']);
    Route::get('activity', [DashboardController::class, 'activity']);
    Route::get('financial', [DashboardController::class, 'financial']);
});

