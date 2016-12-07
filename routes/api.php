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




Route::group([
    'prefix' => 'v1',
    'middleware' => 'api',
], function () {
    Route::post('/snap', 'Api\SnapController@store');
    Route::get('/me', 'Api\MemberController@show');
});
