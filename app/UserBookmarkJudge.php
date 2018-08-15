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
		// отримуємо всі закладки користувача
		$bookmark_judges = static::select('judge')
		->where('user', '=', Auth::user()->id)
		->get();
		// якщо закладок немає - виходимо
		if ($bookmark_judges->isEmpty()) {
			return (NULL);
		}
		
		// формуємо масив з id суддів, які в закладках
		$judges_id = [];
		foreach($bookmark_judges as $judge_id) {
			$judges_id[] = $judge_id->judge;
		}
		
		return (DB::table('judges')->select('judges.id', 'courts.name AS court_name', 'judges.surname', 'judges.name',
			'judges.patronymic', 'judges.photo', 'judges.status',
			DB::raw('DATE_FORMAT(judges.updated_status, "%d.%c.%Y") AS updated_status'),
			DB::raw('DATE_FORMAT(judges.due_date_status, "%d.%c.%Y") AS due_date_status'),
			'judges.rating')
			->join('courts', 'judges.court', '=', 'courts.court_code')
			->whereIn('judges.id', $judges_id)
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
