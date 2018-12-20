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
	Route::get('guest/judges/{id}', 'JudgesController@showGuest')->middleware('checkId:judge');
	// швидкий пошук за прізвищем судді, для поля автодоповнення
	Route::get('judges/autocomplete', 'JudgesController@autocomplete');
	
	/**
	 * Суди
	 */
	// список судів з застосованими фільтрами (Рейтинг->суди)
	Route::get('guest/courts/list', 'CourtsController@indexGuest');
	// швидкий пошук за назвою суду, для поля автодоповнення
	Route::get('courts/autocomplete', 'CourtsController@autocomplete');
	
	
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
		Route::get('judges/{id}', 'JudgesController@show')->middleware('checkId:judge');
		
		// додати суддю в закладки
		Route::put('judges/{id}/bookmark', 'JudgesController@addJudgeBookmark')->middleware('checkId:judge');
		// видалити суддю з закладок
		Route::delete('judges/{id}/bookmark', 'JudgesController@delJudgeBookmark')->middleware('checkId:judge');

		// оновити статус судді
		Route::put('judges/{id}/update-status', 'JudgesController@updateJudgeStatus')->middleware('checkId:judge');
		
		// поставити лайк судді
		Route::put('judges/{id}/like', 'JudgesController@putLike')->middleware('checkId:judge');
		// видалити лайк судді
		Route::delete('judges/{id}/like', 'JudgesController@deleteLike')->middleware('checkId:judge');
		// поставити дизлайк судді
		Route::put('judges/{id}/unlike', 'JudgesController@putUnlike')->middleware('checkId:judge');
		// видалити дизлайк судді
		Route::delete('judges/{id}/unlike', 'JudgesController@deleteUnlike')->middleware('checkId:judge');
		
		
		/**
		 * Суди
		 */
		// список судів з застосованими фільтрами (Рейтинг->суди)
		Route::get('courts/list', 'CourtsController@index');
		// додати суд в закладки
		Route::put('courts/{id}/bookmark', 'CourtsController@addCourtBookmark')->middleware('checkId:court');
		// видалити суд з закладок
		Route::delete('courts/{id}/bookmark', 'CourtsController@delCourtBookmark')->middleware('checkId:court');
		
		/**
		 * Cудові засідання
		 */
		// Додати судове засідання в закладки
		Route::put('court-sessions/{id}/bookmark', 'CourtSessionController@addSessionBookmark')->middleware('checkId:session');
        // Видалити судове засідання з закладок
        Route::delete('court-sessions/{id}/bookmark', 'CourtSessionController@deleteSessionBookmark')->middleware('checkId:session');
		// Додати примітку до закладки на судове засідання
		Route::post('court-sessions/bookmark/{id}/note', 'CourtSessionController@addNote')->middleware('checkId:session-bookmark')->middleware('checkAccess:session-bookmark');
		
		
		/**
		 * Особистий кабінет
		 */
		// Історія переглядів користувача
		Route::get('user/history', 'HomeController@indexHistory');
		// Закладки користувача
		Route::get('user/bookmarks', 'HomeController@indexBookmarks');
		
		/**
		 * Налаштування користувача
		 */
		// Отримати налаштування користувача
		Route::get('user/settings', 'UserSettingsController@indexSettings');
		// Змінити пароль користувача
		Route::post('user/settings/password', 'UserSettingsController@changePassword');
		// Змінити дані користувача
		Route::post('user/settings/user-data', 'UserSettingsController@changeUserData');
		// Змінити налаштування повідомлень користувача
		Route::post('user/settings/notification', 'UserSettingsController@changeNotifications');
		
		
	});
	
});

