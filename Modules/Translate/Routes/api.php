<?php

use Illuminate\Support\Facades\Route;
use Modules\Translate\Http\Controllers\Api\TranslateController;

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
        Route::apiResource('translate', TranslateController::class)->only(['index', 'show', 'store']);
    }
);
