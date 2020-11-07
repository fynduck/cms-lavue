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
Route::prefix('admin')->middleware('auth:api')->group(function () {
    Route::apiResource('article', 'ArticleController');
});

Route::get('get-articles', 'FrontController@getArticles')->name('get-articles');