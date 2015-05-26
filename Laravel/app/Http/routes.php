<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function() {
    return "Time Manager.";
});

Route::post('api/user/login', 'UserController@login');

Route::get('api/user/{userId}/timerow/export/{dateFrom?}/{dateTill?}', 'TimerowController@export');

Route::resource('api/user.timerow', 'TimerowController');

Route::resource('api/user', 'UserController');

Route::resource('api/hour', 'HourController');