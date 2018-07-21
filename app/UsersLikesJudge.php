<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserLikedJudge
 * @package Toecyd
 */
class UsersLikesJudge extends Model
{
	public $timestamps = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user', 'judge'
	];
	
	/**
	 * повертає TRUE якщо користувач поставив лайк судді
	 * або FALSE якщо ні
	 * @param $judge
	 * @return bool
	 */
	public static function isLikedJudge($judge) {
		if (!Auth::check()) {
			return (false);
		}
		$data_status = static::select('id')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		return (($data_status && $data_status->id )  ? (true) : (false));
	}
	

	
	
	
	/**
	 * оновлює існуючий або створює новий запис в БД
	 * про те, що користувач поставив лайк судді
	 * @param $judge
	 * @return array
	 */
	public static function putLike($judge) {
		// відмічаєм, що цей користувач поставив лайк
		static::insert(
		['user' => Auth::user()->id,
		'judge' => $judge]);
	}
	
	
	/**
	 * видаляє запис з БД про те, що користувач
	 * ставив лайк судді
	 * @param $judge
	 * @return array
	 */
	public static function deleteLike($judge) {
		// видаляємо запис про лайк
		static::where('user', '=', Auth::user()->id)
		->where('judge',  '=', $judge)
		->delete();
	}
}
