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
use Modules\Page\Http\Controllers\Api\FrontController;
use Modules\Page\Http\Controllers\Api\PageController;

Route::middleware(['auth:api'])->prefix('admin')->group(
    function () {
        Route::apiResource('page', PageController::class);
    }
);

Route::get('find-page/{slug}', [FrontController::class, 'findPage'])->name('find-page');