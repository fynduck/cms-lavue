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
use Modules\Settings\Http\Controllers\Api\FrontController;
use Modules\Settings\Http\Controllers\Api\SettingsController;

Route::middleware(['auth:api'])->prefix('admin')->group(
    function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'store'])->name('settings.create');
        Route::get('settings-css', [SettingsController::class, 'css'])->name('settings-css.index');
        Route::post('settings-css', [SettingsController::class, 'saveCss'])->name('settings-css.create');
        Route::get('settings-env', [SettingsController::class, 'config'])->name('settings-env.index');
        Route::post('settings-env', [SettingsController::class, 'saveEnv'])->name('settings-env.create');
        Route::get('settings-socials', [SettingsController::class, 'socials'])->name('settings-socials.index');
        Route::post('settings-socials', [SettingsController::class, 'saveSocials'])->name('settings-socials.create');
        Route::get('settings-paginate', [SettingsController::class, 'pagination'])->name('settings-paginate.index');
        Route::post('settings-paginate', [SettingsController::class, 'savePagination'])->name('settings-paginate.create');
    }
);

Route::get('get-settings', [FrontController::class, 'settings'])->name('settings');