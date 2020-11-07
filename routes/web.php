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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;

//Auth::routes();

//Route::get('login', 'Auth\LoginController@showLoginForm');
Route::get('register', 'Auth\LoginController@showLoginForm');
Route::get('password/reset', 'Auth\LoginController@showLoginForm');
Route::get('password/reset/{token}', 'Auth\LoginController@showLoginForm');
Route::get('email/verify/{id}', 'Auth\LoginController@showLoginForm');
Route::get('email/resend', 'Auth\LoginController@showLoginForm');
//Route::get('admin/{path?}','Api\DashboardController@dashboard')->where('path', '(.*)');
Route::get('login', function () {
    return file_get_contents(public_path('_nuxt/index.html'));
});
Route::get('admin/{path?}', function () {
    return file_get_contents(public_path('_nuxt/index.html'));
})->where('path', '(.*)');


Route::get('custom-auth/{user_id}', 'HomeController@customAuth')->name('custom-auth');