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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Дані маршрути призначені для API V1 доступ здійснюється по "host/api/v1/"
Route::group(['prefix' => 'v1/', 'namespace' => 'Api\V1',], function () {
	
	// Аутенфікація користувача
	Route::post('login', 'Auth\AuthController@login');
	Route::post('signup', 'Auth\AuthController@signup');
	
	Route::group([
		'middleware' => 'auth:api'
	], function() {
		Route::get('logout', 'Auth\AuthController@logout');
		Route::get('user', 'Auth\AuthController@user');
	});
	
	//Route::post('register', 'Auth\RegisterController@register');
	//Route::get('register', 'Auth\RegisterController@show');
//	Route::resource();
	
	
});

