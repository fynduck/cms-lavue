<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get(
    'login',
    function () {
        return file_get_contents(public_path('_nuxt/index.html'));
    }
);
Route::get(
    'admin/{path?}',
    function () {
        return file_get_contents(public_path('_nuxt/index.html'));
    }
)->where('path', '(.*)');