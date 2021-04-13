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
use Modules\Search\Http\Controllers\Api\FrontController;

Route::get('query', 'FrontController@liveSearch')->name('query');
Route::get('search-result', [FrontController::class, 'searchResult'])->name('search-result');

Route::prefix('admin')->group(function () {
    Route::get('search-data', 'SearchController@searchData')->name('search-info');
});