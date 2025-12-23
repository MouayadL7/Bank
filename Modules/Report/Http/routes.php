<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
|
| API endpoints for generating reports and retrieving audit logs.
|
*/

Route::middleware(['auth:api', 'can:isAdmin'])->prefix('reports')->group(function () {
    Route::get('transactions/daily', [ReportController::class, 'dailyTransactions']);
    Route::get('accounts/summary', [ReportController::class, 'accountSummary']);
    Route::get('audit-logs', [ReportController::class, 'auditLogs']);
});

