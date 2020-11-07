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

Route::get('get-media', 'FrontController@getMedia')->name('get-media');

Route::get('/', 'FrontController@pages')->name('home');
Route::get('{page?}/{category?}/{url?}', 'FrontController@pages')->name('pages');