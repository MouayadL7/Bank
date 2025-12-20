<?php

use Illuminate\Support\Facades\Route;
use Modules\AccessControl\Http\Controllers\AccessControlController;
use Modules\AccessControl\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| AccessControl Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the AccessControlServiceProvider within a group
| which contains the "web" middleware group. Now create something great!
|
*/

Route::apiResource('roles', RoleController::class)->only(['index', 'store'])
    ->middleware(['auth:api', 'can:isAdmin']);

