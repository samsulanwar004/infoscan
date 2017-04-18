<?php

$routePrefix = null;
Route::get('/verification/{token}', 'Web\MemberController@verify')->name('member_verification');
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

    Route::resource(
        '/promo-points',
        'Web\PromoPointController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'promo-points')]
    );

    Route::resource(
        '/questionnaire',
        'Web\QuestionnaireController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'questionnaire')]
    );

    Route::resource(
        '/questions',
        'Web\QuestionController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'questions')]
    );

    Route::resource(
        '/snaps',
        'Web\SnapController',
        ['names' => route_resource_name($routePrefix, 'snaps')]
    );

    Route::resource(
        '/product-categories',
        'Web\ProductCategoryController',
        ['names' => route_resource_name($routePrefix, 'product-categories')]
    );

    Route::resource(
        '/exchange',
        'Web\ExchangeController',
        ['names' => route_resource_name($routePrefix, 'exchange')]
    );

    Route::resource(
        '/payment-portal',
        'Web\PaymentPortalController',
        ['names' => route_resource_name($routePrefix, 'payment-portal')]
    );

    Route::get('/questionnaire/publish/{id}', 'Web\QuestionnaireController@publish')->name('questionnaire.publish');

    Route::get('/questionnaire/results/{id}', 'Web\QuestionnaireController@resultList')->name('questionnaire.result');

    Route::get('/questionnaire/results/{id}/detail', 'Web\QuestionnaireController@resultShow')->name('questionnaire.resultDetail');

    Route::get(
        '/users/{id}/activities',
        'Web\UserController@activities'
    )->name('users.activities');

    Route::get(
        '/users/{id}/activities',
        'Web\UserController@activities'
    )->name('users.activities');

    Route::get(
        '/snaps/{id}/edit-snap-file',
        'Web\SnapController@editSnapFile'
    )->name('snaps.editSnapFile');

    Route::get(
        '/snaps/{id}/snap-detail',
        'Web\SnapController@snapDetail'
    )->name('snaps.snapDetail');

    Route::get(
        '/download/snaps',
        'Web\SnapController@export'
    )->name('snaps.export');

    Route::get(
        '/snaps/{id}/tagging',
        'Web\SnapController@tagging'
    )->name('snaps.tagging');

    Route::get(
        '/points/get-task-table',
        'Web\PointController@getTaskTable'
    )->name('points.task');

    Route::get(
        '/points/get-task-limit',
        'Web\PointController@getTaskLimit'
    )->name('points.limit');

    Route::get(
        '/points/point-manager',
        'Web\PointController@pointManager'
    )->name('points.manager');

    Route::post(
        '/points/point-manager-update',
        'Web\PointController@pointManagerUpdate'
    )->name('points.manager.update');

    Route::get(
        '/promo-points/get-promo-level-table',
        'Web\PromoPointController@getPromoLevelTable'
    )->name('points.promo');

    /*Route::get(
        '/pages/404', function() {
            return view('errors.404');
        }
    )->name('pages.404');*/

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

    Route::get(
        '/reports',
        'Web\ReportController@index'
    )->name($routePrefix == null ? 'reports' : '.reports');

    Route::post(
        '/reports',
        'Web\ReportController@index'
    )->name($routePrefix == null ? 'reports' : '.reports');

    Route::post(
        '/merchants/settingReports',
        'Web\MerchantController@storeSettingReports'
    )->name($routePrefix == null ? 'merchants.settingReports.store' : '.merchants.settingReports.store');

    Route::post(
        '/merchants/settingReports/{id}',
        'Web\MerchantController@updateSettingReports'
    )->name($routePrefix == null ? 'merchants.settingReports.update' : '.merchants.settingReports.update');

    Route::post(
        '/reports/chart',
        'Web\ReportController@createChart'
    )->name('create.chart');

    Route::get(
        '/crowdsource',
        'Web\CrowdsourceController@index'
    )->name('crowdsource.index');

    Route::get(
        '/crowdsource/{id}',
        'Web\CrowdsourceController@show'
    )->name('crowdsource.show');

    Route::get(
        '/crowdsource/{id}/detail-activity',
        'Web\CrowdsourceController@detailActivity'
    )->name('crowdsource.detail-activity');

    Route::post(
        '/crowdsource/date-filter',
        'Web\CrowdsourceController@dateFilter'
    )->name('crowdsource.date-filter');

    Route::get(
        '/lucky/winner',
        'Web\LuckyDrawController@winner'
    )->name('lucky.winner');

    Route::get(
        '/tagging/save',
        'Web\SnapController@taggingSave'
    )->name('tagging.save');
});

Auth::routes();
Route::get('/secure/{requestCode}/{social}', 'SecureController@redirect');
Route::get('/callback/{social}', 'SecureController@callback');