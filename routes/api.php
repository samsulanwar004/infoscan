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

Route::group([
    'prefix' => 'v1',
    'middleware' => ['api', 'verifySignature'],
], function () {
    Route::get('/snap', 'Api\SnapController@index');
    Route::post('/snap', 'Api\SnapController@store');
    Route::get('/promotion', 'Api\PromotionController@index');
    Route::get('/luckydraw', 'Api\LuckyDrawController@index');
    Route::post('/luckydraw', 'Api\LuckyDrawController@store');
    Route::get('/me', 'Api\MemberController@show');
    Route::put('/me', 'Api\MemberController@update');
});