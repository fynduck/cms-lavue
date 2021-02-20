<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\Api\FrontController;
use Modules\Menu\Http\Controllers\Api\MenuController;

Route::middleware(['auth:api'])->prefix('admin')->group(
    function () {
        Route::apiResource('menu', MenuController::class);
        Route::post('menu-settings', [MenuController::class, 'saveSettings'])->name('menu.settings.store');
    }
);
Route::get('get-menu', [FrontController::class, 'getMenu'])->name('get-menu');