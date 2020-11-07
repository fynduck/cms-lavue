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

Route::namespace('Auth')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('refresh', 'LoginController@refresh');
        Route::post('logout', 'LoginController@logout');
    });

    Route::middleware('guest:api')->group(function () {
        Route::post('login', 'LoginController@login');
        Route::post('register', 'RegisterController@register');

        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/reset', 'ResetPasswordController@reset');

        Route::post('email/verify/{user}', 'VerificationController@verify')->name('verification.verify');
        Route::post('email/resend', 'VerificationController@resend');
    });
});

/**
 * |---------------------
 * | Route for settings
 * |---------------------
 **/
Route::prefix('admin')->group(function () {
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
//    Route::get('dashboard', 'DashboardController@dashboard')->name('admin_dashboard.view');
//    Route::get('get-aside', 'DashboardController@getAside')->name('admin_aside_menu.view');
//    Route::get('get-tags', 'DashboardController@getTags')->name('admin_get_tags.view');
});