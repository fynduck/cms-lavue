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
use Modules\Banner\Http\Controllers\Api\BannerController;
use Modules\Banner\Http\Controllers\Api\FrontController;

Route::prefix('admin')->middleware('auth:api')->group(
    function () {
        Route::apiResource('banner', BannerController::class);
        Route::post('banner-settings', [BannerController::class, 'saveSettings'])->name('banner.settings.store');
    }
);

Route::get('get-slides', [FrontController::class, 'getBanner'])->name('get-slides');