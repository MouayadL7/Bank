<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| Account Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the AccountServiceProvider within a group
| which contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('api/accounts')->group(function () {

    // create & show
    Route::post('/', [AccountController::class, 'store']);
    Route::get('{uuid}', [AccountController::class, 'show']);

    // close & state
    Route::post('{uuid}/close', [AccountController::class, 'close']);
    Route::post('{uuid}/state', [AccountController::class, 'changeState']);

    // update
    Route::patch('{uuid}/meta', [AccountController::class, 'updateMeta']);
    Route::patch('{uuid}/parent', [AccountController::class, 'changeParent']);

    // transaction-like endpoints
    Route::prefix('{uuid}/transactions')->group(function () {
        Route::post('deposit', [AccountController::class, 'deposit']);
        Route::post('withdraw', [AccountController::class, 'withdraw']);
        // Route::post('transfer', [AccountController::class, 'transfer']);  ← لاحقًا
    });
});

