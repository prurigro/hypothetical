<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
