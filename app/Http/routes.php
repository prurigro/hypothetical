<?php

Route::group(['middleware' => 'web'], function () {
    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/', function() {
        Head::setTitle('Home');
        return view('website.index');
    });

    Route::get('/contact', function() {
        Head::setTitle('Contact');
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

    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'dashboard'], function() {
        Route::get('/', 'DashboardController@index');
        Route::controller('', DashboardController::class);
    });
});
