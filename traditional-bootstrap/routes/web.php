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

Route::auth([ 'register' => env('REGISTRATION', false) === true || env('REGISTRATION', false) === \Request::ip() ]);
Route::get('/logout', 'Auth\LoginController@logout');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::group([ 'prefix' => 'dashboard' ], function() {
    // Dashboard CMS
    Route::get('/', 'DashboardController@getIndex');
    Route::get('/view/{model}', 'DashboardController@getView');
    Route::get('/edit/{model}', 'DashboardController@getEditList');
    Route::get('/edit/{model}/{id}', 'DashboardController@getEditItem');
    Route::get('/export/{model}', 'DashboardController@getExport');
    Route::post('/reorder', 'DashboardController@postReorder');
    Route::post('/update', 'DashboardController@postUpdate');
    Route::post('/image-upload', 'DashboardController@postImageUpload');
    Route::post('/file-upload', 'DashboardController@postFileUpload');
    Route::delete('/delete', 'DashboardController@deleteDelete');
    Route::delete('/image-delete', 'DashboardController@deleteImageDelete');
    Route::delete('/file-delete', 'DashboardController@deleteFileDelete');

    // Dashboard Settings
    Route::get('/settings', 'DashboardController@getSettings');
    Route::post('/user/password-update', 'DashboardController@postUserPasswordUpdate');
    Route::post('/user/profile-update', 'DashboardController@postUserProfileUpdate');
    Route::post('/user/profile-image-upload', 'DashboardController@postUserProfileImageUpload');
    Route::delete('/user/profile-image-delete', 'DashboardController@deleteUserProfileImageDelete');

    // Credits Page
    Route::get('/credits', 'DashboardController@getCredits');
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
    return view('pages.home');
});

Route::get('/blog', function() {
    return view('pages.blog');
});

Route::get('/contact', function() {
    return view('pages.contact');
});
