<?php

use Illuminate\Support\Facades\Route;
use Modules\Language\Http\Controllers\Api\LanguageController;

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

Route::prefix('admin')->middleware(['auth:api'])->group(
    function () {
        Route::apiResource('language', LanguageController::class);
    }
);