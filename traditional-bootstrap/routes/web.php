<?php

use App\Utilities\Language;

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

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::auth();
Route::get('/logout', 'Auth\LoginController@logout');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::group([ 'prefix' => 'dashboard' ], function() {
    Route::get('/', 'DashboardController@index');
    Route::get('/view/{model}', 'DashboardController@getView');
    Route::get('/edit/{model}', 'DashboardController@getEditList');
    Route::get('/edit-item/{model}/{id}', 'DashboardController@getEditItem');
    Route::get('/export/{model}', 'DashboardController@getExport');
    Route::post('/image-upload', 'DashboardController@postImageUpload');
    Route::post('/file-upload', 'DashboardController@postFileUpload');
    Route::post('/update', 'DashboardController@postUpdate');
    Route::post('/reorder', 'DashboardController@postReorder');
    Route::delete('/delete', 'DashboardController@deleteDelete');
    Route::delete('/image-delete', 'DashboardController@deleteImageDelete');
    Route::delete('/file-delete', 'DashboardController@deleteFileDelete');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/language/{lang}', function($lang) {
    Language::setSessionLanguage($lang);
    return redirect()->back();
});

Route::get('/', function() {
    return view('pages.index');
});

Route::get('/contact', function() {
    return view('pages.contact');
});
