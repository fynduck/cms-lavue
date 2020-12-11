<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\AccountInfo;
use Modules\User\Http\Controllers\Api\Auth\ForgotPasswordController;
use Modules\User\Http\Controllers\Api\Auth\LoginController;
use Modules\User\Http\Controllers\Api\Auth\RegisterController;
use Modules\User\Http\Controllers\Api\Auth\ResetPasswordController;
use Modules\User\Http\Controllers\Api\Auth\VerificationController;
use Modules\User\Http\Controllers\Api\UserController;

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
    Route::get('user', AccountInfo::class);

    Route::prefix('admin')->group(function () {
        Route::apiResource('user', UserController::class);
    });
});

Route::namespace('Auth')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        Route::post('refresh', [LoginController::class, 'refresh']);
        Route::post('logout', [LoginController::class, 'logout']);
    });

    Route::middleware(['guest:api'])->group(function () {
        Route::post('login', [LoginController::class, 'login']);
        Route::post('register', [RegisterController::class, 'register']);

        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
        Route::post('password/reset', [ResetPasswordController::class, 'reset']);

        Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
        Route::post('email/resend', [VerificationController::class, 'resend']);
    });
});