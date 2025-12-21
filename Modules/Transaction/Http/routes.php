<?php

use Illuminate\Support\Facades\Route;
use Modules\Transaction\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Transaction Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the TransactionServiceProvider within a group
| which contains the "web" middleware group. Now create something great!
|
*/

// transaction-like endpoints
Route::prefix('{uuid}/transactions')->group(function () {
    Route::post('deposit', [TransactionController::class, 'deposit']);
    Route::post('withdraw', [TransactionController::class, 'withdraw']);
    // Route::post('transfer', [TransactionController::class, 'transfer']);
});

