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

Route::prefix('admin')->middleware(['auth:api'])->group(
    function () {
        Route::apiResource('custom-form', 'CustomFormController');
        Route::get('custom-form-list/{id}', 'CustomFormController@completed')->name('custom-form-list');
        Route::post('custom-form-clone/{id}', 'CustomFormController@cloneForm')->name('custom-form.create');
    }
);

Route::get('get-form/{page_type}/{page_id}/{form_id?}', 'FrontController@getForm')->name('get-form');
Route::post('call-back', 'FrontController@saveCallBack')->name('send-call-back');