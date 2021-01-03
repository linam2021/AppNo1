<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



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

///// User routes /////

Route::post('user/register', 'Api\user\AuthController@register');
Route::post('user/login', 'Api\user\AuthController@login');
Route::post('user/reset-password','Api\user\PasswordResetRequestController@sendEmail');
Route::post('user/confirm-reset','Api\user\ChangePasswordController@passwordResetProcess');

Route::group(['middleware' => 'auth:user-api', 'prefix' => 'user', 'as' => 'user.','namespace'=> 'Api\User'],
function ($router) {
    Route::post('requests/{id}/rating', 'RequestController@rate')->name('requests.rate');
    Route::get('me', 'AuthController@user')->name('show');
    Route::put('me', 'AuthController@update')->name('update');
    Route::post('logout', 'AuthController@logout')->name('logout');

    Route::resource('requests', 'RequestController')->only('store','index','show');

});

///// Employee routes /////

Route::post('employee/SendSolvedRequest','Api\employee\SendSolvedRequestEmail@sendEmail');
Route::post('employee/login', 'Api\Employee\AuthController@login');

Route::group(['middleware' => 'auth:employee-api', 'prefix' => 'employee', 'namespace'=> 'Api\Employee'],
function ($router) {
    Route::get('requests/filter-by-type', 'RequestController@filter');
    Route::put('requests/{id}/change-status-to-solved', 'RequestController@changeStatustoSolved');
    Route::get('me', 'AuthController@employee')->name('show');
    Route::put('me', 'AuthController@update')->name('update');
    Route::post('logout', 'AuthController@logout')->name('logout');
    Route::resource('requests', 'RequestController')->only('index','show','update');
});








