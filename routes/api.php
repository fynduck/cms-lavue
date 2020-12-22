<?php

use App\Http\Controllers\Api\AdminSearch;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FrontController;
use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;

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

/**
 * |---------------------
 * | Route for settings
 * |---------------------
 **/
Route::get('get-app-data', [FrontController::class, 'getAppData'])->name('app-data');

Route::prefix('admin')->group(function () {
    Route::get('get-app-data', [DashboardController::class, 'getAppData'])->name('app-admin-data');

    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::namespace('Api')->group(function () {
            Route::get('live-select', [AdminSearch::class, 'liveSelect'])->name('admin-live-select-list');
            Route::options('trans-slug', [DashboardController::class, 'transSlug'])->name('trans-slug-list');
        });

        Route::prefix('filemanager')
            ->group(function () {
                Lfm::routes();
            });
    });
});