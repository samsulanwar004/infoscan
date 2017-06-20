<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('v1/login', 'SecureController@login');
Route::post('v1/register', 'SecureController@register');
Route::get('v1/settings', 'Api\SettingController@index');
Route::get('v1/promotion', 'Api\PromotionController@index');
Route::get('v1/categories', 'Api\PromotionController@categories');
Route::get('v1/internal', 'Api\InternalController@index');
Route::get('v1/city/{id}', 'Api\SettingController@city');
Route::get('v1/referral/', 'Api\ReferralController@index');

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api', 'api', 'verifySignature'],
], function () {
    Route::get('/snap', 'Api\SnapController@index');
    Route::post('/snap', 'Api\SnapController@store');
    Route::get('/luckydraw', 'Api\LuckyDrawController@index');
    Route::post('/luckydraw', 'Api\LuckyDrawController@store');
    Route::get('/me', 'Api\MemberController@show');
    Route::put('/me', 'Api\MemberController@update');
    Route::put('/update', 'Api\MemberController@updateEmailAndName');
    Route::post('/avatar', 'Api\MemberController@changeAvatar');
    Route::get('/questionnaires', 'Api\QuestionnaireController@index');
    Route::get('/questionnaires/{id}', 'Api\QuestionnaireController@show');
    Route::post('/questionnaires', 'Api\QuestionnaireController@store');
    Route::get('/portalpoint', 'Api\PortalPointController@index');
    Route::get('/paymentportal', 'Api\PaymentPortalController@index');
    Route::post('/paymentportal', 'Api\PaymentPortalController@store');
    Route::get('/history', 'Api\HistoryController@index');
    Route::get('/notification', 'Api\NotificationController@index');
    Route::post('/log', 'Api\LogController@store');
    Route::get('/leaderboard', 'Api\LeaderboardController@index');
    Route::get('/limit', 'Api\LimitController@index');
});