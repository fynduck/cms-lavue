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
use Modules\Search\Http\Controllers\Api\SearchController;

Route::get('search-result', [FrontController::class, 'searchResult'])->name('search-result');

Route::prefix('admin')->group(function () {
    Route::resource('search', SearchController::class)->only(['index', 'store']);
});