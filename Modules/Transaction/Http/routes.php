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

Route::prefix('accounts')->group(function () {
    Route::post('/{uuid}/transactions/deposit', [TransactionController::class, 'deposit']);
    Route::post('/{uuid}/transactions/withdraw', [TransactionController::class, 'withdraw']);
    Route::post('/{fromUuid}/transactions/transfer/{toUuid}', [TransactionController::class, 'transfer']);
});

