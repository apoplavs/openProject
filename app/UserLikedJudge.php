<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserLikedJudge
 * @package Toecyd
 */
class UserLikedJudge extends Model
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
	 * повертає TRUE якщо користувач поставив лайк/дизлайк судді
	 * або FALSE якщо ні
	 * @param $judge
	 * @return bool
	 */
	public static function isNoticeJudge($judge) {
		$data_status = static::select('like', 'unlike')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		return (($data_status && ($data_status->like || $data_status->unlike))  ? (true) : (false));
	}
	
	
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
		$data_status = static::select('like')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		return (($data_status && $data_status->like )  ? (true) : (false));
	}
	
	
	/**
	 * повертає TRUE якщо користувач поставив дизлайк судді
	 * або FALSE якщо ні
	 * @param $judge
	 * @return bool
	 */
	public static function isUnlikedJudge($judge) {
		if (!Auth::check()) {
			return (false);
		}
		$data_status = static::select('unlike')
			->where('user', '=', Auth::user()->id)
			->where('judge', '=', $judge)
			->first();
		return (($data_status && $data_status->unlike )  ? (true) : (false));
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
		'judge' => $judge,
		'like' => 1,
		'unlike' => 0]);

		// отримуємо кількість лайків в даного судді
		$judge_likes = DB::table('judges')->where('id', '=', $judge)->first();
		$judge_likes->likes++;
		
		// додаємо ще один лайк
		Judge::where('id', '=', $judge)->update(['likes' => $judge_likes->likes]);
		return ($judge_likes);
	}
	
	/**
	 * оновлює існуючий або створює новий запис в БД
	 * про те, що користувач поставив дизлайк судді
	 * @param $judge
	 * @return array
	 */
	public static function putUnlike($judge) {
		// відмічаєм, що цей користувач поставив дизлайк
		static::insert(
			['user' => Auth::user()->id,
			'judge' => $judge,
			'like' => 0,
			'unlike' => 1]);
		
		// отримуємо кількість дизлайків в даного судді
		$judge_unlikes = DB::table('judges')->where('id', '=', $judge)->first();
		$judge_unlikes->unlikes++;
		
		// додаємо ще один дизлайк
		Judge::where('id', '=', $judge)->update(['unlikes' => $judge_unlikes->unlikes]);
		return ($judge_unlikes);
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
		
		// отримуємо кількість лайків в даного судді
		$judge_likes = DB::table('judges')->where('id', '=', $judge)->first();
		$judge_likes->likes--;
		
		// віднімаємо один лайк
		Judge::where('id', '=', $judge)->update(['likes' => $judge_likes->likes]);
		return ($judge_likes);
	}
	
	/**
	 * видаляє запис з БД про те, що користувач
	 * ставив дизлайк судді
	 * @param $judge
	 * @return array
	 */
	public static function deleteUnlike($judge) {
		// видаляємо запис про дизлайк
		static::where('user', '=', Auth::user()->id)
			->where('judge',  '=', $judge)
			->delete();
		
		// отримуємо кількість дизлайків в даного судді
		$judge_unlikes = DB::table('judges')->where('id', '=', $judge)->first();
		$judge_unlikes->unlikes--;
		// віднімаємо один дизлайк
		Judge::where('id', '=', $judge)->update(['unlikes' => $judge_unlikes->unlikes]);
		return ($judge_unlikes);
	}
	
}
