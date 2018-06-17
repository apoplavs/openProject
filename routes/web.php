<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// головна сторінка сайту
Route::get('/', function () {
    return view('welcome');
});
// rotes авторизації користувача
Auth::routes();

// домашня сторінка користувача
Route::get('/home', 'HomeController@index')->name('home');
/*Route::get('/home', function () {
	return view('welcome');
});*/

// налаштування (Username->налаштування) ЩЕ НЕ ЗРОБЛЕНО
Route::get('/settings', 'HomeController@index')->name('settings');


/**
 * Розділ "Судді"
 */
// список суддів (Рейтинг->судді) використовується для ajax
Route::get('/judges-list', 'Judges\JudgesController@index')->name('judges-list');

// сторінка судді з інформацією про нього
Route::get('/judges/{id}', 'Judges\JudgesController@show')->name('judges');

// отримання результатів для автодоповнення в формі пошуку використовується для ajax
Route::get('/judges-autocomplete', 'Judges\JudgesController@autocompleteSearch')->name('judges-autocomplete');

// оновити статус судді
Route::put('/judge-status/{id}', 'Judges\JudgesController@updateJudgeStatus');

// оболонка сторінки яка влючає фільтри, і пошук (Рейтинг->судді)
Route::get('/judges', function () {
	return view('judges.judges');
})->name('judges');


// додати суддю в закладки
Route::put('/judges/{id}/bookmark', 'User\BookmarksController@addJudgeBookmark')->middleware('auth');
// видалити суддю з закладок
Route::delete('/judges/{id}/bookmark', 'User\BookmarksController@delJudgeBookmark')->middleware('auth');


// поставити лайк судді
Route::put('/judges/{id}/like', 'Judges\JudgesController@putLike');
// поставити дизлайк судді
Route::put('/judges/{id}/unlike', 'Judges\JudgesController@putUnlike')->middleware('auth');

// додати фото судді
Route::post('/judges/add-photo', 'Judges\JudgesController@addPhoto');
