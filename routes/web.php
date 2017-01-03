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

    Route::resource(
        '/users',
        'Web\UserController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'users')]
    );
    Route::get('/users/{id}/profile', 'Web\UserController@getProfile');
    Route::put('/users/{id}/credential', 'Web\UserController@putCredential');

    Route::resource(
        '/users/roles',
        'Web\RoleController',
        ['names' => route_resource_name($routePrefix, 'roles')]
    );
    Route::resource(
        '/users/permissions',
        'Web\PermissionController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'permissions')]
    );
    Route::resource(
        '/settings',
        'Web\SettingController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'settings')]
    );
    Route::resource(
        '/promotions',
        'Web\PromotionController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'promotions')]
    );
    Route::resource(
        '/merchants',
        'Web\MerchantController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'merchants')]
    );
    // Route::resource(
    //     '/merchants/user',
    //     'Web\MerchantUserController',
    //     ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'merchantusers')]
    // );
    Route::resource(
        '/ses',
        'Web\SesController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'ses')]
    );
    Route::resource(
        '/lucky',
        'Web\LuckyDrawController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'lucky')]
    );
    Route::resource(
        '/points',
        'Web\PointController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'points')]
    );

    Route::resource('/questionnaire', 'Web\QuestionnaireController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'questionnaire')]);
    Route::resource('/questions', 'Web\QuestionController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'questions')]);

    Route::resource(
        '/snaps',
        'Web\SnapController',
        ['names' => route_resource_name($routePrefix, 'snaps')]
    );

    Route::get(
        '/snaps/{id}/edit-snap-file',
        'Web\SnapController@editSnapFile'
    )->name('snaps.editSnapFile');

    Route::resource(
        '/members',
        'Web\MemberController',
        ['names' => route_resource_name($routePrefix, 'members')]
    );

    Route::get(
        '/history/transactions',
        'Web\HistoryController@transactions'
    )->name('transaction.index');

    Route::get(
        'history/{id}/transactions',
        'Web\HistoryController@showTransaction'
    )->name('transaction.show');

    Route::resource(
        '/reports',
        'Web\ReportsController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'reports')]
    );

    Route::get(
    '/reports/formatPdf',
    'Web\ReportsController@formatPdf'
    )->name('reports.formatPdf');

    Route::get(
        '/reports/formatExcel',
        'Web\ReportsController@formatExcel'
    )->name('reports.formatExcel');

    Route::get(
        '/reports/formatWord',
        'Web\ReportsController@formatWord'
    )->name('reports.formatWord');

});

Auth::routes();

Route::get('/secure/{requestCode}/{social}', 'SecureController@redirect');
