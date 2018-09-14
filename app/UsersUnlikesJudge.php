<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserLikedJudge
 * @package Toecyd
 */
class UsersUnlikesJudge extends Model
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
	 * повертає TRUE якщо користувач поставив дизлайк судді
	 * або FALSE якщо ні
	 * @param $judge
	 * @return bool
	 */
	public static function isUnlikedJudge($judge) {
		$data_status = static::select('id')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		return (($data_status && $data_status->id )  ? true : false);
	}
	
	
	
	/**
	 * оновлює існуючий або створює новий запис в БД
	 * про те, що користувач поставив дизлайк судді
	 * @param $judge
	 */
	public static function putUnlike($judge) {
		// відмічаєм, що цей користувач поставив дизлайк
		static::insert(
			['user' => Auth::user()->id,
			'judge' => $judge]);
	}
	
	
	/**
	 * видаляє запис з БД про те, що користувач
	 * ставив дизлайк судді
	 * @param $judge
	 */
	public static function deleteUnlike($judge) {
		// видаляємо запис про дизлайк
		static::where('user', '=', Auth::user()->id)
			->where('judge',  '=', $judge)
			->delete();
	}
	
}
