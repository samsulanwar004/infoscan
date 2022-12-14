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

    Route::group(['prefix' => '/chart'], function () {
        Route::get('/active-users/{timeRange?}', [
            'as'   => 'chart.active-users',
            'uses' => 'Web\ChartController@activeUsers',
        ]);
        Route::get('/active-users/{timeRange}/{export}', [
            'as'   => 'chart.active-users.export',
            'uses' => 'Web\ChartController@activeUsers',
        ]);

        Route::get('/snaps-status/{timeRange?}', [
            'as'   => 'chart.snaps-status',
            'uses' => 'Web\ChartController@snapsStatus',
        ]);
        Route::get('/snaps-status/{timeRange}/{export}', [
            'as'   => 'chart.snaps-status.export',
            'uses' => 'Web\ChartController@snapsStatus',
        ]);

        Route::get('/snaps-rejection-reason/{timeRange?}', [
            'as'   => 'chart.snaps-rejection-reason',
            'uses' => 'Web\ChartController@snapsRejections',
        ]);
        Route::get('/snaps-rejection-reason/{timeRange}/{export}', [
            'as'   => 'chart.snaps-rejection-reason.export',
            'uses' => 'Web\ChartController@snapsRejections',
        ]);

        Route::get('/top-ten/{timeRange?}', [
            'uses' => 'Web\ChartController@topTen',
            'as'   => 'chart.top-ten',
        ]);

    });

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
        '/bonus-points',
        'Web\BonusPointController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'bonus-points')]
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
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'exchange')]
    );

    Route::resource(
        '/payment-portal',
        'Web\PaymentPortalController',
        ['names' => route_resource_name($routePrefix, 'payment-portal')]
    );

    Route::resource(
        '/leaderboard',
        'Web\LeaderboardController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'leaderboard')]
    );

    Route::resource(
        '/referral',
        'Web\ReferralController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'referral')]
    );

    Route::resource(
        '/forced-assign',
        'Web\ForcedAssignController',
        ['except' => ['show'], 'names' => route_resource_name($routePrefix, 'forced-assign')]
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
        '/download/payment',
        'Web\PaymentPortalController@export'
    )->name('payment.export');

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

    Route::post(
        '/crop/upload',
        'Web\SnapController@cropUpload'
    )->name('crop.upload');

    Route::put(
        '/approve-image/{id}',
        'Web\SnapController@approveImage'
    )->name('approve.image');

    Route::get(
        '/exchange/city/create',
        'Web\ExchangeController@cityCreate'
    )->name('city.rate.create');

    Route::post(
        '/exchange/city/store',
        'Web\ExchangeController@cityStore'
    )->name('city.rate.store');

    Route::get(
        '/exchange/city/{id}/edit',
        'Web\ExchangeController@cityEdit'
    )->name('city.rate.edit');

    Route::put(
        '/exchange/city/{id}/update',
        'Web\ExchangeController@cityUpdate'
    )->name('city.rate.update');

    Route::delete(
        '/exchange/city/{id}',
        'Web\ExchangeController@cityDestroy'
    )->name('city.rate.destroy');

    Route::get(
        '/download/reports',
        'Web\ReportController@export'
    )->name('reports.export');

    Route::get(
        '/exchange/setting',
        'Web\ExchangeController@setting'
    )->name('exchange.setting');

    Route::post(
        '/exchange/setting-update',
        'Web\ExchangeController@settingUpdate'
    )->name('exchange.setting.update');

    Route::get(
        '/point/limit/create',
        'Web\PointController@limitCreate'
    )->name('task.limit.create');

    Route::post(
        '/point/limit/store',
        'Web\PointController@limitStore'
    )->name('task.limit.store');

    Route::get(
        '/point/limit/{id}/edit',
        'Web\PointController@limitEdit'
    )->name('task.limit.edit');

    Route::put(
        '/point/limit/{id}/update',
        'Web\PointController@limitUpdate'
    )->name('task.limit.update');

});

Auth::routes();
Route::get('/secure/{requestCode}/{social}', 'SecureController@redirect');
Route::get('/callback/{social}', 'SecureController@callback');
