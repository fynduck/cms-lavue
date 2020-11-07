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
Route::middleware(['auth:api'])->prefix('admin')->group(function () {
    Route::get('settings', 'SettingsController@index')->name('settings.index');
    Route::post('settings', 'SettingsController@store')->name('settings.create');
    Route::get('settings-css', 'SettingsController@css')->name('settings-css.index');
    Route::post('settings-css', 'SettingsController@saveCss')->name('settings-css.create');
    Route::get('settings-env', 'SettingsController@config')->name('settings-env.index');
    Route::post('settings-env', 'SettingsController@saveEnv')->name('settings-env.create');
    Route::get('settings-socials', 'SettingsController@socials')->name('settings-socials.index');
    Route::post('settings-socials', 'SettingsController@saveSocials')->name('settings-socials.create');
    Route::get('settings-paginate', 'SettingsController@pagination')->name('settings-paginate.index');
    Route::post('settings-paginate', 'SettingsController@savePagination')->name('settings-paginate.create');
});