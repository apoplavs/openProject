<?php

namespace Toecyd;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserBookmarkJudge extends Model
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
	 * створює нову закладку на суддю
	 * @param $user_id
	 * @param $judge_id
	 */
	public static function createBookmark($user_id, $judge_id) {
		static::insert([
			'user'=>$user_id,
			'judge'=>$judge_id
		]);
	}
	
	
	/**
	 * видаляє закладку поточного користувача на суддю
	 * @param $user_id
	 * @param $judge_id
	 */
	public static function deleteBookmark($user_id, $judge_id) {
		static::where('user', '=', $user_id)
			->where('judge', '=', $judge_id)
			->delete();
	}
	
	
	/**
	 * Отримати всіх суддів, що перебувають
	 * в закладках користувача
	 * @return mixed
	 */
	public static function getBookmarkJudges() {

		return (DB::table('judges')->select('judges.id', 'courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%m.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%m.%Y") AS due_date_status'),
			'judges.rating')
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->join('user_bookmark_judges', 'user_bookmark_judges.user', '=', Auth::user()->id)
			->get());
	}
	
	
	/**
	 * перевірити чи є суддя в закладках користувача
	 * @param $user_id
	 * @param $judge_id
	 * @return boolean
	 */
	public static function checkBookmark($user_id, $judge_id) {
		$bookmark = static::select('id')
			->where('judge', '=', $judge_id)
			->where('user', '=', $user_id)
			->first();
		if (empty($bookmark)) {
			return (false);
		}
		return (true);
	}
}
