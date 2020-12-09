<?php

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
Route::middleware('cors')->prefix('admin')->group(function () {
    Route::get('get-app-data', 'Api\DashboardController@getAppData')->name('app-data');

    Route::middleware('auth:api')->group(function () {
        Route::namespace('Api')->group(function () {
            Route::get('live-select', 'AdminSearch@liveSelect')->name('admin-live-select-list');
            Route::options('trans-slug', 'DashboardController@transSlug')->name('trans-slug-list');
        });

        Route::middleware(['admin'])
            ->prefix('filemanager')
            ->group(function () {
                Lfm::routes();
            });
    });
});