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
Route::group(['middleware' => 'auth:user-api', 'prefix' => 'user', 'as' => 'user.','namespace'=> 'Api\User'],
function ($router) {
    Route::resource('requests', 'RequestController')->only('store','index','show','rate');
});


///// Employee routes /////
Route::post('employee/login', 'Api\Employee\AuthController@login');

Route::group(['middleware' => 'auth:employee-api', 'prefix' => 'employee', 'namespace'=> 'Api\Employee'],
function ($router) {
// to do later

});
