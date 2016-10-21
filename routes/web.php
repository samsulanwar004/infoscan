<?php

$routePrefix = null;

Route::group([
    'middleware' => 'auth',
], function () use ($routePrefix) {

    // dashboard
    Route::get('/', 'Web\AdminController@dashboard')
         ->name($routePrefix == null ? 'dashboard' : '.dashboard');

    Route::post('/transfer', 'Web\AssetController@transferImages')
         ->name($routePrefix == null ? 'transfer' : '.transfer');

    Route::resource('/users', 'Web\UserController', ['names' => route_resource_name($routePrefix, 'users')]);
});

Auth::routes();

Route::get('/home', 'HomeController@index');
