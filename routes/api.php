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


//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

// Дані маршрути призначені для API V1 доступ здійснюється по "host/api/v1/"
Route::group(['prefix' => 'v1/', 'namespace' => 'Api\V1',], function () {
	
	// Аутенфікація користувача
	Route::post('login', 'AuthController@login');
    Route::post('login/google', 'AuthController@loginGoogle');
    Route::post('login/facebook', 'AuthController@loginFacebook');
	Route::post('signup', 'AuthController@signup');
	
	/**
	 * Судді
	 */
	// список суддів з застосованими фільтрами (Рейтинг->судді) для незареєстрованого користуача
	Route::get('guest/judges/list', 'JudgesController@indexGuest');
	// сторінка судді з інформацією про нього
	//Route::get('guest/judges/{id}', 'JudgesController@show')->middleware('checkId:judge');
	
	// швидкий пошук за прізвищем судді, для поля автодоповнення
	Route::get('judges/autocomplete', 'JudgesController@autocomplete');
	
	
	// Маршрути які вимагають реєстрації користувача
	Route::group(['middleware' => 'auth:api'], function() {
		Route::get('logout', 'AuthController@logout');
		Route::get('user', 'AuthController@user');
		
		/**
		 * Судді
		 */
		// список суддів з застосованими фільтрами (Рейтинг->судді)
		Route::get('judges/list', 'JudgesController@index');
		// сторінка судді з інформацією про нього
		//Route::get('judges/{id}', 'JudgesController@show')->middleware('checkId:judge');
		
		// додати суддю в закладки
		Route::put('/judges/{id}/bookmark', 'JudgesController@addJudgeBookmark')->middleware('checkId:judge');
		// видалити суддю з закладок
		Route::delete('/judges/{id}/bookmark', 'JudgesController@delJudgeBookmark')->middleware('checkId:judge');

		// оновити статус судді
		Route::put('judges/{id}/update-status', 'JudgesController@updateJudgeStatus')->middleware('checkId:judge');
		
		// поставити лайк судді
		Route::put('/judges/{id}/like', 'JudgesController@putLike')->middleware('checkId:judge');
		// видалити лайк судді
		Route::delete('/judges/{id}/like', 'JudgesController@deleteLike')->middleware('checkId:judge');
		// поставити дизлайк судді
		Route::put('/judges/{id}/unlike', 'JudgesController@putUnlike')->middleware('checkId:judge');
		// видалити дизлайк судді
		Route::delete('/judges/{id}/unlike', 'JudgesController@deleteUnlike')->middleware('checkId:judge');
		
		
		/**
		 * Судді
		 */
		// список судів з застосованими фільтрами (Рейтинг->суди)
		Route::get('courts/list', 'CourtsController@index');
		// todo протестувати, дописати документацію, (розділ Responses)
		
	});
	
});

