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
//Lina, I changed the name of the "api" guard to "user-api"
Route::middleware('auth:user-api')->get('/user', function (Request $request) {
    return $request->user();
});


///// admin routes /////
Route::post('employee/login', 'Api\Employee\AuthController@login');

Route::group(['middleware' => 'auth:employee-api', 'prefix' => 'admin', 'namespace'=> 'Api\Employee'],
function ($router) {
// to do later

});


Route::post('user/register', 'Api\user\AuthController@register');
Route::post('user/login', 'Api\user\AuthController@login');






