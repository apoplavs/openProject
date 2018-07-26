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

/**
 * опис для Swagger
 *
 * @SWG\Swagger(
 *     basePath="",
 *     host="openproject.local",
 *     produces={"application/json"},
 * 	   consumes={"application/json"},
 *     @SWG\Info(
 *         version="1.0",
 *         title="Open Project API",
 *     	   description="Опис REST API",
 *         @SWG\Contact(name="Developers", url="https://www.google.com"),
 *     ),
 *     @SWG\Definition(
 *         definition="Error",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Дані маршрути призначені для API V1 доступ здійснюється по "host/api/v1/"
Route::group(['prefix' => 'v1/'], function () {
	
	Route::post('register', 'Api\V1\Auth\RegisterController@register');
	Route::get('register', 'Api\V1\Auth\RegisterController@show');
	
	
});

