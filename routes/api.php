<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Get Routes
|--------------------------------------------------------------------------
*/

Route::get('/blog-entries', 'App\Http\Controllers\ApiController@getBlogEntries');

/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/

Route::post('/contact-submit', 'App\Http\Controllers\ApiController@postContactSubmit');
Route::post('/subscription-submit', 'App\Http\Controllers\ApiController@postSubscriptionSubmit');
