<?php

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function() {
    return view('website.index');
});

Route::get('/contact', function() {
    return view('website.contact');
});

/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/

Route::post('/contact-submit', 'ContactController@postContactSubmit');
Route::post('/subscription-submit', 'SubscriptionController@postSubscriptionSubmit');

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
    Route::get('/contact', 'DashboardController@getContact');
    Route::get('/subscriptions', 'DashboardController@getSubscriptions');
    Route::get('/export/{model}', 'DashboardController@getExport');
    Route::post('/image-upload', 'DashboardController@postImageUpload');
    Route::post('/edit', 'DashboardController@postEdit');
    Route::post('/reorder', 'DashboardController@postReorder');
    Route::delete('/delete', 'DashboardController@deleteDelete');
});
