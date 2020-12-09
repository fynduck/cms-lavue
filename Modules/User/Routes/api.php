<?php

use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:api'])->group(function () {
    Route::get('user', 'AccountInfo');

    Route::prefix('admin')->group(function () {
        Route::apiResource('user', 'UserController');
    });
});

Route::namespace('Auth')->group(function () {
    Route::middleware(['auth:api', 'cors'])->group(function () {
        Route::post('refresh', 'LoginController@refresh');
        Route::post('logout', 'LoginController@logout');
    });

    Route::middleware(['guest:api', 'cors'])->group(function () {
        Route::post('login', 'LoginController@login');
        Route::post('register', 'RegisterController@register');

        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/reset', 'ResetPasswordController@reset');

        Route::post('email/verify/{user}', 'VerificationController@verify')->name('verification.verify');
        Route::post('email/resend', 'VerificationController@resend');
    });
});