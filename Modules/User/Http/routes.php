<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the UserServiceProvider within a group
| which contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:api', 'can:isAdmin'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('search', [UserController::class, 'search']);
    Route::get('{uuid}', [UserController::class, 'show']);
    Route::post('{uuid}/suspend', [UserController::class, 'suspend']);
    Route::post('{uuid}/activate', [UserController::class, 'activate']);
});
