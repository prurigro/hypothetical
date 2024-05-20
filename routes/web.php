<?php

use Illuminate\Support\Facades\Route;
use App\Dashboard;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::auth([ 'register' => Dashboard::canRegister() ]);
Route::get('/logout', 'App\Http\Controllers\Auth\LoginController@logout');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::group([ 'prefix' => 'dashboard' ], function() {
    // Dashboard CMS
    Route::get('/', 'App\Http\Controllers\DashboardController@getIndex');
    Route::get('/view/{model}', 'App\Http\Controllers\DashboardController@getView');
    Route::get('/edit/{model}', 'App\Http\Controllers\DashboardController@getEditList');
    Route::get('/edit/{model}/{id}', 'App\Http\Controllers\DashboardController@getEditItem');
    Route::get('/export/{model}', 'App\Http\Controllers\DashboardController@getExport');
    Route::post('/reorder', 'App\Http\Controllers\DashboardController@postReorder');
    Route::post('/update', 'App\Http\Controllers\DashboardController@postUpdate');
    Route::post('/image-upload', 'App\Http\Controllers\DashboardController@postImageUpload');
    Route::post('/file-upload', 'App\Http\Controllers\DashboardController@postFileUpload');
    Route::delete('/delete', 'App\Http\Controllers\DashboardController@deleteDelete');
    Route::delete('/image-delete', 'App\Http\Controllers\DashboardController@deleteImageDelete');
    Route::delete('/file-delete', 'App\Http\Controllers\DashboardController@deleteFileDelete');

    // Dashboard Settings
    Route::get('/settings', 'App\Http\Controllers\DashboardController@getSettings');
    Route::post('/user/password-update', 'App\Http\Controllers\DashboardController@postUserPasswordUpdate');
    Route::post('/user/profile-update', 'App\Http\Controllers\DashboardController@postUserProfileUpdate');
    Route::post('/user/profile-image-upload', 'App\Http\Controllers\DashboardController@postUserProfileImageUpload');
    Route::delete('/user/profile-image-delete', 'App\Http\Controllers\DashboardController@deleteUserProfileImageDelete');

    // Credits Page
    Route::get('/credits', 'App\Http\Controllers\DashboardController@getCredits');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/language/{lang}', function($lang) {
    return Language::setSessionLanguage($lang);
});

Route::get('/{vue?}', function() {
    return view('templates.public');
})->where('vue', '[\/\w\.-]*');
