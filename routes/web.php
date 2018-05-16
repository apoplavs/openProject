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

// налаштування (Username->налаштування)
//Route::get('/settings', 'HomeController@index')->name('settings');

// список суддів (Рейтинг->судді) використовується для ajax
Route::get('/judges-list', 'Judges\JudgesController@index')->name('judges-list');

// оболонка сторінки (Рейтинг->судді)
Route::get('/judges', function () {
	return view('judges.judges');
})->name('judges');


// для отримання сторінки конкретного судді
//Route::get('/judges{}', 'Judges\JudgesController@show')->name('judges');
