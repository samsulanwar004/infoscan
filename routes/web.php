<?php

$routePrefix = null;

Route::group([
    //'middleware' => 'auth',
], function () use ($routePrefix) {

    // dashboard
    Route::get('/', 'Web\AdminController@dashboard')
         ->name($routePrefix == null ? 'dashboard' : '.dashboard');

    Route::get('/transfer', 'Web\AssetController@transferImages')
         ->name($routePrefix == null ? 'transfer' : '.transfer');

});
