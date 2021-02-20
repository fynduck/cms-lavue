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
use Modules\Article\Http\Controllers\Api\ArticleController;
use Modules\Article\Http\Controllers\Api\FrontController;

Route::prefix('admin')->middleware('auth:api')->group(
    function () {
        Route::apiResource('article', ArticleController::class);
        Route::post('article-settings', [ArticleController::class, 'saveSettings'])->name('article.settings.store');
    }
);

Route::get('get-articles', [FrontController::class, 'getArticles'])->name('get-articles');
Route::get('articles/{slug}', [FrontController::class, 'article'])->name('article');