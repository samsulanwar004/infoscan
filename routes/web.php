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

    Route::resource('/users', 'Web\UserController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'users')]);
    Route::get('/users/{id}/profile', 'Web\UserController@getProfile');
    Route::put('/users/{id}/credential', 'Web\UserController@putCredential');

    Route::resource('/users/roles', 'Web\RoleController',
        ['names' => route_resource_name($routePrefix, 'roles')]);
    Route::resource('/users/permissions', 'Web\PermissionController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'permissions')]);
    Route::resource('/settings', 'Web\SettingController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'settings')]);
    Route::resource('/promotions', 'Web\PromotionController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'promotions')]);

});

Auth::routes();

Route::get('/home', 'HomeController@index');
