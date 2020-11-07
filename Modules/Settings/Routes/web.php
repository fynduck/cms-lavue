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

//Route::middleware('web')->namespace('Modules\Settings\Http\Controllers')->prefix(config('app.locale_prefix'))->group(function () {
//    Route::prefix('admin')->group(function () {
//
//        Route::get('settings', 'SettingsController@index')->name('admin-settings');
//        Route::post('settings', 'SettingsController@update')->name('admin-settings-save');
//
//        Route::get('settings/sitemap', 'SettingsController@sitemap')->name('admin-settings-sitemap');
//        Route::get('settings/sitemap/generate', 'SettingsController@sitemapGenerate')->name('admin-settings-sitemap-generate');
//
//        Route::get('settings/contacts', 'SettingsController@contacts')->name('admin-settings-contacts');
//        Route::post('settings/contacts', 'SettingsController@saveContacts')->name('admin-contact-save');
//
//        // server config
//        Route::get('settings/config', 'SettingsController@config')->name('admin-settings-config');
//        Route::post('settings/configuration', 'SettingsController@saveConfig')->name('admin-config-file');
//
//        // css style
//        Route::get('settings/css', 'SettingsController@css')->name('admin-settings-css');
//        Route::post('settings/css', 'SettingsController@saveCss')->name('admin-css-file');
//
//        // css style
//        Route::get('settings/socials', 'SettingsController@socials')->name('admin-settings-socials');
//        Route::post('settings/socials', 'SettingsController@saveSocials')->name('admin-socials-save');
//
//        // paginate nr
//        Route::get('settings/pagination', 'SettingsController@pagination')->name('admin-settings-pagination');
//        Route::post('settings/pagination', 'SettingsController@savePagination')->name('admin-pagination-save');
//    });
//});