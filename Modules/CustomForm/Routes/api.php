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

Route::prefix('admin')->middleware(['auth:api'])->group(function () {
    Route::apiResource('custom-form', 'CustomFormController');
    Route::get('custom-form-list/{id}', 'CustomFormController@completed')->name('custom-form-list');
    Route::post('custom-form-clone/{id}', 'CustomFormController@cloneForm')->name('custom-form.create');
});
