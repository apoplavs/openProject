<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserBookmarkCourt
 * @package Toecyd
 */
class UserBookmarkCourt extends Model
{
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user', 'court'
	];
	
	
	/**
	 * створює нову закладку на суд
	 * @param $user_id
	 * @param $court_id
	 */
	public static function createBookmark($user_id, $court_id) {
		static::insert([
			'user'=>$user_id,
			'court'=>$court_id
		]);
	}
	
	
	/**
	 * видаляє закладку поточного користувача на суд
	 * @param $user_id
	 * @param $court_id
	 */
	public static function deleteBookmark($user_id, $court_id) {
		static::where('user', '=', $user_id)
			->where('court', '=', $court_id)
			->delete();
	}
	
	
	/**
	 * перевірити чи є суд в закладках користувача
	 * @param $user_id
	 * @param $court_id
	 * @return boolean
	 */
	public static function checkBookmark($user_id, $court_id) {
		$bookmark = static::select('id')
			->where('court', '=', $court_id)
			->where('user', '=', $user_id)
			->first();
		if (empty($bookmark)) {
			return (false);
		}
		return (true);
	}
	
	
	/**
	 * Отримати всі суди, що перебувають
	 * в закладках користувача
	 * @return mixed
	 */
	public static function getBookmarkCourts() {
		
		//
	}
}
